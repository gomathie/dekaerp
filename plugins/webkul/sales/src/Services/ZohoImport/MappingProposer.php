<?php

namespace Webkul\Sale\Services\ZohoImport;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Webkul\Account\Enums\AmountType;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\TaxIncludeOverride;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Tax;
use Webkul\Account\Services\PaymentWaysProvisioner;
use Webkul\Product\Models\Product;
use Webkul\Support\Models\Company;

/**
 * Proposes how each piece of a Zoho Books export lands in a DEKA company:
 * Zoho accounts to the company's own chart, taxes, bank and cash accounts to
 * journals, items to products and contacts to partners.
 *
 * Only a proposal. The user approves the table (user decision, 2026-11-25:
 * nothing is recorded before the mapping is approved), may edit the JSON, and
 * the importer then follows the file exactly. Reading only: nothing here
 * writes to the database.
 *
 * Reads are filtered by company explicitly, never through a global scope:
 * the command runs in the console, where CompanyScope does not apply.
 */
class MappingProposer
{
    public const FORMAT = 'deka-zoho-mapping';

    public const VERSION = 1;

    /** New documents use DEKA's own yearly series (user decision, 2026-11-25). */
    public const NUMBERING_DEKA = 'deka';

    /** New documents continue Zoho's counter instead. */
    public const NUMBERING_ZOHO = 'zoho';

    /**
     * Zoho account names with a fixed home in the standard (and Ghana) chart.
     * Name => [action, DEKA code, name and type when created].
     *
     * @var array<string, array{0: string, 1: string, 2?: string, 3?: string}>
     */
    protected const NAMED = [
        'accounts receivable'         => ['map', '121000'],
        'accounts payable'            => ['map', '211000'],
        'sales'                       => ['map', '400000'],
        'tax payable'                 => ['map', '251000'],
        'advance tax'                 => ['map', '132100'],
        'tds receivable'              => ['map', '132100'],
        'tds payable'                 => ['map', '230400'],
        'prepaid expenses'            => ['map', '128000'],
        'furniture and equipment'     => ['map', '151000'],
        'unearned revenue'            => ['map', '212000'],
        "owner's equity"              => ['map', '301000'],
        'retained earnings'           => ['map', '999999'],
        'opening balance offset'      => ['map', '999999'],
        'opening balance adjustments' => ['map', '999999'],
        'inventory asset'             => ['map', '110100'],
        'cost of goods sold'          => ['map', '500000'],
        'rent expense'                => ['map', '612000'],
        'bank fees and charges'       => ['map', '620000'],
        'salaries and employee wages' => ['map', '630000'],
        'exchange gain or loss'       => ['map', '641000'],
        'other expenses'              => ['map', '600000'],
        'uncategorized'               => ['map', '600000'],
        'general income'              => ['map', '450000'],
        'other charges'               => ['map', '450000'],
    ];

    /**
     * Where a created account of each Zoho type is numbered from, the step
     * between codes, and the DEKA account type it gets.
     *
     * @var array<string, array{0: int, 1: int, 2: string}>
     */
    protected const CREATE_RANGES = [
        'expense'                 => [613000, 1000, 'expense'],
        'cost_of_goods_sold'      => [501000, 1000, 'expense_direct_cost'],
        'other_expense'           => [651000, 1000, 'expense'],
        'income'                  => [451000, 1000, 'income_other'],
        'other_income'            => [451000, 1000, 'income_other'],
        'other_current_asset'     => [128100, 100, 'asset_current'],
        'fixed_asset'             => [151100, 100, 'asset_fixed'],
        'stock'                   => [110600, 100, 'asset_current'],
        'other_current_liability' => [230500, 100, 'liability_current'],
        'other_liability'         => [291100, 100, 'liability_non_current'],
        'long_term_liability'     => [291100, 100, 'liability_non_current'],
        'equity'                  => [303000, 1000, 'equity'],
        'accounts_receivable'     => [121100, 100, 'asset_receivable'],
        'accounts_payable'        => [211200, 100, 'liability_payable'],
    ];

    /** @var list<string> */
    protected array $takenCodes = [];

    /**
     * @return array<string, mixed>
     */
    /**
     * @param  array{numbering?: string, skip_unused_samples?: bool}  $options
     */
    public function propose(Company $company, ZohoExport $export, array $options = []): array
    {
        $accounts = $this->companyAccounts($company);
        $this->takenCodes = $accounts->pluck('code')->map(fn ($code) => (string) $code)->all();

        $banks = $this->proposeBanks($company, $export, $accounts);

        return [
            'format'  => self::FORMAT,
            'version' => self::VERSION,
            'company' => [
                'id'       => $company->id,
                'name'     => $company->name,
                'currency' => $company->currency?->name,
            ],
            'source' => [
                'organization_id' => $export->organizationId(),
                'currency'        => $export->currency(),
                'exported_at'     => $export->exportedAt(),
                'counts'          => $export->counts(),
            ],
            'approved'          => false,
            'accounts'          => $this->proposeAccounts($export, $accounts, $banks),
            'taxes'             => $this->proposeTaxes($company, $export),
            'bank_accounts'     => $banks,
            'payment_modes'     => $this->proposePaymentModes($export),
            'bank_transactions' => collect($export->bankTransactions())
                ->groupBy('transaction_type')
                ->map(fn ($rows) => ['count' => $rows->count(), 'amount' => round((float) $rows->sum('amount'), 2)])
                ->all(),
            'items'         => $this->proposeItems($company, $export),
            'contacts'      => $this->proposeContacts($export, $options['skip_unused_samples'] ?? true),
            'sequences'     => $this->proposeSequences($export, ($options['numbering'] ?? self::NUMBERING_DEKA) === self::NUMBERING_ZOHO),
            'options'       => [
                'numbering'           => ($options['numbering'] ?? self::NUMBERING_DEKA) === self::NUMBERING_ZOHO ? self::NUMBERING_ZOHO : self::NUMBERING_DEKA,
                'skip_unused_samples' => $options['skip_unused_samples'] ?? true,
            ],
        ];
    }

    /**
     * @return Collection<int, Account>
     */
    protected function companyAccounts(Company $company): Collection
    {
        return Account::withoutGlobalScopes()
            ->whereHas('companies', fn ($query) => $query->where('companies.id', $company->id))
            ->orderBy('code')
            ->get();
    }

    /**
     * @param  Collection<int, Account>  $accounts
     * @param  array<string, array<string, mixed>>  $banks
     * @return array<string, array<string, mixed>>
     */
    protected function proposeAccounts(ZohoExport $export, Collection $accounts, array $banks): array
    {
        $usage = $export->accountUsage();
        $byCode = $accounts->keyBy(fn (Account $account) => (string) $account->code);
        $byName = $accounts->keyBy(fn (Account $account) => Str::lower(trim((string) $account->name)));
        $proposal = [];

        foreach ($export->accounts() as $zohoId => $zoho) {
            $name = trim((string) $zoho['account_name']);
            $type = (string) $zoho['account_type'];
            $used = $usage[$zohoId] ?? 0;
            $entry = [
                'zoho_name' => $name,
                'zoho_type' => $type,
                'used'      => $used,
            ];

            if (isset($banks[$zohoId])) {
                $proposal[$zohoId] = $entry + [
                    'action' => 'map',
                    'code'   => $banks[$zohoId]['account_code'],
                    'name'   => $banks[$zohoId]['account_name'],
                    'note'   => 'The liquidity account of journal '.$banks[$zohoId]['journal_code'].'.',
                ];

                continue;
            }

            $rule = self::NAMED[Str::lower($name)] ?? null;
            $existing = $rule ? $byCode->get($rule[1]) : $byName->get(Str::lower($name));

            if ($existing) {
                $proposal[$zohoId] = $entry + [
                    'action' => $used > 0 ? 'map' : 'unused',
                    'code'   => (string) $existing->code,
                    'name'   => (string) $existing->name,
                ] + ($this->paidAsExpense($export, (string) $zohoId) && ! in_array($type, ['expense', 'cost_of_goods_sold', 'other_expense', 'fixed_asset'], true)
                    ? ['note' => 'Zoho expenses are booked to this account (a payment that reduces it, such as tax paid to the authority). Check it is the right account.']
                    : []);

                continue;
            }

            [$start, $step, $dekaType] = self::CREATE_RANGES[$type] ?? [699000, 100, 'expense'];

            $proposal[$zohoId] = $entry + [
                // Accounts no document uses are not created: they would only
                // clutter the company's chart (user: create the missing ones,
                // for what the history needs).
                'action' => $used > 0 ? 'create' : 'unused',
                'code'   => $used > 0 ? $this->freeCode($start, $step) : null,
                'name'   => $name,
                'type'   => $dekaType,
            ] + ($type === 'other_expense' && $used > 0
                ? ['note' => 'Zoho records it as an expense; kept as one so the totals match Zoho.']
                : []);
        }

        return $proposal;
    }

    protected function paidAsExpense(ZohoExport $export, string $zohoId): bool
    {
        foreach ($export->expenses() as $expense) {
            foreach ($expense['line_items'] ?? [] as $line) {
                if ((string) ($line['account_id'] ?? '') === $zohoId) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function freeCode(int $start, int $step): string
    {
        for ($code = $start; ; $code += $step) {
            if (! in_array((string) $code, $this->takenCodes, true)) {
                $this->takenCodes[] = (string) $code;

                return (string) $code;
            }
        }
    }

    /**
     * Bank and cash accounts to the company's own ways to pay (2026-11-20):
     * Bank transfer, Mobile Money and Cash, or a new way of its own.
     *
     * @param  Collection<int, Account>  $accounts
     * @return array<string, array<string, mixed>>
     */
    protected function proposeBanks(Company $company, ZohoExport $export, Collection $accounts): array
    {
        $journals = Journal::withoutGlobalScopes()
            ->where('company_id', $company->id)
            ->whereIn('type', [JournalType::BANK, JournalType::CASH])
            ->get()
            ->keyBy('code');

        $byId = $accounts->keyBy('id');
        $proposal = [];
        $taken = [];

        foreach ($export->bankAccounts() as $zohoId => $zoho) {
            $name = Str::lower((string) $zoho['account_name']);

            $code = match (true) {
                str_contains($name, 'mobile money') || str_contains($name, 'momo') => PaymentWaysProvisioner::MOBILE_MONEY_CODE,
                ($zoho['account_type'] ?? '') === 'cash'                           => 'CASH',
                default                                                            => 'BANK',
            };

            $journal = in_array($code, $taken, true) ? null : $journals->get($code);

            if ($journal) {
                $taken[] = $code;
                $account = $byId->get($journal->default_account_id);

                $proposal[$zohoId] = [
                    'zoho_name'    => (string) $zoho['account_name'],
                    'zoho_type'    => (string) $zoho['account_type'],
                    'balance'      => (float) $zoho['balance'],
                    'action'       => 'map',
                    'journal_code' => $journal->code,
                    'journal_name' => $journal->name,
                    'account_code' => $account?->code,
                    'account_name' => $account?->name,
                ];

                continue;
            }

            $proposal[$zohoId] = [
                'zoho_name'    => (string) $zoho['account_name'],
                'zoho_type'    => (string) $zoho['account_type'],
                'balance'      => (float) $zoho['balance'],
                'action'       => 'create',
                'journal_code' => null,
                'journal_name' => (string) $zoho['account_name'],
                'account_code' => null,
                'account_name' => (string) $zoho['account_name'],
                'note'         => 'A new way to pay with its own account, as "Add your own" in the Pay window.',
            ];
        }

        return $proposal;
    }

    /**
     * One DEKA tax per Zoho line tax and price mode. A Zoho tax group becomes
     * a DEKA group with the same components: Zoho rounds each component on
     * its own, and one combined rate would put some invoices a pesewa out.
     * An existing tax is reused only when it is exactly the same.
     *
     * @return array<string, array<string, mixed>>
     */
    protected function proposeTaxes(Company $company, ZohoExport $export): array
    {
        $existing = Tax::withoutGlobalScopes()
            ->where('company_id', $company->id)
            ->where('type_tax_use', TypeTaxUse::SALE)
            ->with('childrenTaxes')
            ->get();

        $proposal = [];

        foreach ($export->lineTaxes() as $key => $tax) {
            $match = $existing->first(fn (Tax $candidate): bool => $this->sameTax($candidate, $tax));
            $name = $this->taxName($tax);

            $proposal[$key] = [
                'zoho_tax_id' => $tax['zoho_tax_id'],
                'zoho_name'   => $tax['zoho_name'],
                'rate'        => $tax['rate'],
                'inclusive'   => $tax['inclusive'],
                'components'  => $tax['components'],
                'used'        => $tax['used'],
                'action'      => $match ? 'map' : 'create',
                'deka_tax_id' => $match?->id,
                'name'        => $match?->name ?? $name,
            ];
        }

        return $proposal;
    }

    /**
     * @param  array{rate: float, inclusive: bool, components: list<array{rate: float}>}  $tax
     */
    protected function sameTax(Tax $candidate, array $tax): bool
    {
        $inclusive = $candidate->price_include_override === TaxIncludeOverride::TAX_INCLUDED;
        $rates = collect($tax['components'])->pluck('rate')->map(fn ($rate) => round((float) $rate, 4))->sort()->values()->all();

        if (count($rates) === 1) {
            return $candidate->amount_type === AmountType::PERCENT
                && $inclusive === $tax['inclusive']
                && round((float) $candidate->amount, 4) === $rates[0];
        }

        if ($candidate->amount_type !== AmountType::GROUP) {
            return false;
        }

        $children = $candidate->childrenTaxes;

        return $children->every(fn (Tax $child) => ($child->price_include_override === TaxIncludeOverride::TAX_INCLUDED) === $tax['inclusive'])
            && $children->map(fn (Tax $child) => round((float) $child->amount, 4))->sort()->values()->all() === $rates;
    }

    /**
     * @param  array{zoho_name: string, rate: float, inclusive: bool}  $tax
     */
    protected function taxName(array $tax): string
    {
        $rate = rtrim(rtrim(number_format($tax['rate'], 2, '.', ''), '0'), '.');

        return trim($tax['zoho_name']).' '.$rate.' %'.($tax['inclusive'] ? ' (price includes tax)' : '');
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    protected function proposePaymentModes(ZohoExport $export): array
    {
        $modes = [];

        foreach ($export->payments() as $payment) {
            $mode = (string) ($payment['payment_mode'] ?: 'Unspecified');
            $modes[$mode] ??= ['used' => 0, 'note' => 'Kept in the payment memo. The journal follows the account the money went into.'];
            $modes[$mode]['used']++;
        }

        return $modes;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    protected function proposeItems(Company $company, ZohoExport $export): array
    {
        $existing = Product::withoutGlobalScopes()
            ->where('company_id', $company->id)
            ->whereNull('parent_id')
            ->get()
            ->keyBy(fn (Product $product) => Str::lower(trim((string) $product->name)));

        $usage = [];

        foreach ([...$export->invoices(), ...$export->estimates()] as $document) {
            foreach ($document['line_items'] ?? [] as $line) {
                if (filled($line['item_id'] ?? null)) {
                    $usage[$line['item_id']] = ($usage[$line['item_id']] ?? 0) + 1;
                }
            }
        }

        $proposal = [];

        foreach ($export->items() as $zohoId => $item) {
            $match = $existing->get(Str::lower(trim((string) $item['name'])));

            $proposal[$zohoId] = [
                'name'            => (string) $item['name'],
                'product_type'    => ($item['product_type'] ?? '') === 'goods' ? 'goods' : 'service',
                'used'            => $usage[$zohoId] ?? 0,
                'action'          => $match ? 'map' : 'create',
                'deka_product_id' => $match?->id,
                'note'            => 'No stock tracking (Zoho does not track it).',
            ];
        }

        return $proposal;
    }

    /**
     * Every Zoho contact becomes its own DEKA contact, duplicates included:
     * the user chose to keep them as Zoho has them (2026-11-25), and matching
     * by name could file one client's invoices under another's contact.
     *
     * Zoho's demo contacts ("(Sample)" in the name) are skipped when no
     * document uses them (user decision, 2026-11-25); one a document uses is
     * imported, or that document would lose its customer.
     *
     * @return array<string, array<string, mixed>>
     */
    protected function proposeContacts(ZohoExport $export, bool $skipUnusedSamples): array
    {
        $used = array_flip(array_filter(array_map('strval', [
            ...array_column($export->invoices(), 'customer_id'),
            ...array_column($export->estimates(), 'customer_id'),
            ...array_column($export->payments(), 'customer_id'),
            ...array_column($export->expenses(), 'vendor_id'),
        ])));

        $proposal = [];

        foreach ($export->contacts() as $zohoId => $contact) {
            $name = trim((string) ($contact['contact_name'] ?? ''));
            $sample = str_contains(Str::lower($name), '(sample)') && ! isset($used[(string) $zohoId]);

            $proposal[$zohoId] = [
                'name'            => $name,
                'zoho_type'       => (string) ($contact['contact_type'] ?? 'customer'),
                'action'          => $sample && $skipUnusedSamples ? 'skip' : 'create',
                'deka_partner_id' => null,
            ];
        }

        return $proposal;
    }

    /**
     * New DEKA documents continue Zoho's numbering (user decision,
     * 2026-11-25): Zoho's own counter, read from the most recently created
     * document, plus one. Not the highest number in the series: one number
     * typed by hand far above the counter would otherwise make DEKA jump
     * ahead of Zoho. A number already used is stepped over.
     *
     * @return array<string, array<string, mixed>|null>
     */
    protected function proposeSequences(ZohoExport $export, bool $continueZoho): array
    {
        return [
            // Sales receipts have their own Zoho series: not the invoice counter.
            'invoices'   => $this->continuation(array_values(array_filter($export->invoices(), fn (array $invoice): bool => ! str_starts_with((string) $invoice['invoice_id'], 'receipt:'))), 'invoice_number', $continueZoho, 'The customer invoice journal numbers its next invoice this way; it no longer restarts each year.'),
            'quotations' => $this->continuation($export->estimates(), 'estimate_number', $continueZoho, 'DEKA numbers quotations and sales orders from one counter, so sales orders take this prefix too. Set "action" to "keep" to leave DEKA\'s own numbering.'),
        ];
    }

    /**
     * The proposal as tables a person can read and approve.
     *
     * @param  array<string, mixed>  $mapping
     */
    public function markdown(array $mapping): string
    {
        $cell = fn ($value): string => str_replace(['|', "\n"], ['\\|', ' '], (string) ($value ?? ''));
        $row = fn (array $cells): string => '| '.implode(' | ', array_map($cell, $cells)).' |';
        $table = fn (array $headers, array $rows): string => implode("\n", [
            $row($headers),
            '|'.str_repeat(' --- |', count($headers)),
            ...array_map($row, $rows),
        ]);

        $out = [
            '# Zoho Books to DEKA: proposed mapping',
            '',
            'Company: '.$mapping['company']['name'].' (id '.$mapping['company']['id'].', '.$mapping['company']['currency'].'). '
                .'Zoho organization currency: '.$mapping['source']['currency'].'. Exported '.$mapping['source']['exported_at'].'.',
            '',
            'Nothing is recorded until this mapping is approved. "create" adds the account, tax, journal, product or contact; '
                .'"map" uses the existing one; "unused" means no document uses it, so nothing is created.',
            '',
            '## Accounts',
            '',
            $table(
                ['Zoho account', 'Zoho type', 'Uses', 'Action', 'DEKA code', 'DEKA name', 'DEKA type', 'Note'],
                array_map(fn (array $a): array => [$a['zoho_name'], $a['zoho_type'], $a['used'], $a['action'], $a['code'] ?? '', $a['name'] ?? '', $a['type'] ?? '', $a['note'] ?? ''], array_values($mapping['accounts'])),
            ),
            '',
            '## Taxes',
            '',
            $table(
                ['Zoho tax', 'Rate', 'Price includes tax', 'Components', 'Uses', 'Action', 'DEKA tax'],
                array_map(fn (array $t): array => [
                    $t['zoho_name'],
                    $t['rate'].' %',
                    $t['inclusive'] ? 'yes' : 'no',
                    implode(' + ', array_map(fn (array $c): string => $c['name'].' '.$c['rate'].' %', $t['components'])),
                    $t['used'],
                    $t['action'],
                    $t['name'],
                ], array_values($mapping['taxes'])),
            ),
            '',
            'Lines without a tax in Zoho get no tax in DEKA.',
            '',
            '## Bank and cash accounts',
            '',
            $table(
                ['Zoho account', 'Zoho type', 'Zoho balance', 'Action', 'DEKA journal', 'DEKA account'],
                array_map(fn (array $b): array => [$b['zoho_name'], $b['zoho_type'], number_format($b['balance'], 2), $b['action'], trim(($b['journal_code'] ?? '').' '.$b['journal_name']), trim(($b['account_code'] ?? '').' '.$b['account_name'])], array_values($mapping['bank_accounts'])),
            ),
            '',
            '## Payment modes',
            '',
            $table(['Zoho mode', 'Uses', 'In DEKA'], array_map(fn (string $mode, array $m): array => [$mode, $m['used'], $m['note']], array_keys($mapping['payment_modes']), array_values($mapping['payment_modes']))),
            '',
            '## Other bank transactions',
            '',
            'Money movements of their own (owner contributions, deposits, transfers). Each becomes a journal entry between the two mapped accounts above.',
            '',
            $table(['Zoho type', 'Count', 'Amount'], array_map(fn (string $type, array $t): array => [$type, $t['count'], number_format($t['amount'], 2)], array_keys($mapping['bank_transactions'] ?? []), array_values($mapping['bank_transactions'] ?? []))),
            '',
            '## Items to products',
            '',
            $table(
                ['Zoho item', 'Type', 'Uses', 'Action', 'Note'],
                array_map(fn (array $i): array => [$i['name'], $i['product_type'], $i['used'], $i['action'], $i['note']], array_values($mapping['items'])),
            ),
            '',
            '## Contacts to partners',
            '',
            $table(
                ['Zoho contact', 'Type', 'Action'],
                array_map(fn (array $c): array => [$c['name'], $c['zoho_type'], $c['action'].($c['deka_partner_id'] ? ' #'.$c['deka_partner_id'] : '')], array_values($mapping['contacts'])),
            ),
            '',
            '## Numbering',
            '',
            $table(
                ['Documents', 'Action', 'Next number', 'Note'],
                array_map(fn (string $kind, ?array $s): array => [$kind, $s['action'] ?? 'keep', $s['example'] ?? '', $s['note'] ?? 'No Zoho series found.'], array_keys($mapping['sequences']), array_values($mapping['sequences'])),
            ),
            '',
        ];

        return implode("\n", $out);
    }

    /**
     * @param  list<array<string, mixed>>  $documents
     * @return array<string, mixed>|null
     */
    protected function continuation(array $documents, string $field, bool $continueZoho, string $note): ?array
    {
        if ($documents === []) {
            return null;
        }

        usort($documents, fn (array $a, array $b): int => (string) ($a['created_time'] ?? '') <=> (string) ($b['created_time'] ?? ''));

        $latest = (string) end($documents)[$field];

        if (! preg_match('/^(.*?)(\d+)$/', $latest, $match)) {
            return null;
        }

        [$prefix, $digits] = [$match[1], strlen($match[2])];
        $used = array_map('strval', array_column($documents, $field));
        $next = (int) $match[2] + 1;

        while (in_array($prefix.str_pad((string) $next, $digits, '0', STR_PAD_LEFT), $used, true)) {
            $next++;
        }

        return [
            'action'  => $continueZoho ? 'apply' : 'keep',
            'prefix'  => $prefix,
            'padding' => $digits,
            'next'    => $next,
            'example' => $continueZoho ? $prefix.str_pad((string) $next, $digits, '0', STR_PAD_LEFT) : null,
            'note'    => $continueZoho ? $note : 'Imported documents keep their Zoho numbers; new ones use DEKA\'s own yearly series.',
        ];
    }
}
