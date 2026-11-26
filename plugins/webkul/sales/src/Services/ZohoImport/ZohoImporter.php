<?php

namespace Webkul\Sale\Services\ZohoImport;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Spatie\LaravelSettings\Events\LoadingSettings;
use Throwable;
use Webkul\Account\Enums\AmountType;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\DocumentType;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Enums\RepartitionType;
use Webkul\Account\Enums\TaxIncludeOverride;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\Partner;
use Webkul\Account\Models\Payment;
use Webkul\Account\Models\PaymentRegister;
use Webkul\Account\Models\PaymentTerm;
use Webkul\Account\Models\Product;
use Webkul\Account\Models\ProductCompanyAccount;
use Webkul\Account\Models\Tax;
use Webkul\Account\Models\TaxPartition;
use Webkul\Account\Services\AccountingSetupService;
use Webkul\Account\Services\ExpenseEntry;
use Webkul\Account\Services\MoveWorkflow;
use Webkul\Account\Services\PaymentRegistrar;
use Webkul\Account\Services\PaymentWaysProvisioner;
use Webkul\Account\Services\PeriodLock;
use Webkul\Account\Settings\TaxesSettings;
use Webkul\Partner\Enums\AccountType as PartnerAccountType;
use Webkul\PluginManager\Support\PluginEntitlement;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Services\CompanyCategoryProvisioner;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Facades\SaleOrder as SaleOrderFacade;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Models\OrderLine;
use Webkul\Sale\Models\ZohoImportRecord;
use Webkul\Sale\Models\ZohoImportRun;
use Webkul\Sale\Support\SalesAccess;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Country;
use Webkul\Support\Models\UOM;
use Webkul\Support\Services\CompanyContext;
use Webkul\Support\Services\SequenceService;

/**
 * Records a Zoho Books history in one DEKA company (user decisions,
 * 2026-11-25): full history, oldest first, Zoho's numbers kept, drafts as
 * drafts, voids cancelled, estimates as quotations, expenses as journal
 * entries, payments through DEKA's own payment registrar.
 *
 * Follows an approved mapping file exactly (MappingProposer writes the
 * proposal). Builds nothing of its own in the ledger: documents are DEKA
 * models, totals and taxes come from AccountFacade::computeAccountMove(),
 * posting from MoveWorkflow, payments from PaymentRegistrar. So the lock
 * date, balance checks and every saving hook apply as for a person.
 *
 * Every document is its own transaction and is remembered in
 * sales_zoho_import_records with its Zoho id, so a second run skips what the
 * first recorded. A failure stops the run and names the Zoho document; what
 * was recorded before it stays, and the next run continues from there.
 *
 * Runs in the console with no acting user and the company forced
 * (CompanyContext::runFor). Global scopes do not apply there, so every read
 * below filters by the company explicitly.
 */
class ZohoImporter
{
    /**
     * A document whose computed totals differ from Zoho's by more than this
     * is refused: the user asked for totals that match exactly.
     */
    protected const TOLERANCE = 0.005;

    protected Company $company;

    protected ZohoExport $export;

    /** @var array<string, mixed> */
    protected array $mapping;

    protected ?Closure $output = null;

    protected ?int $creatorId = null;

    /** @var array<string, int> zoho account id => DEKA account id */
    protected array $accountIds = [];

    /** @var array<string, int> tax key => DEKA tax id */
    protected array $taxIds = [];

    /** @var array<string, int> zoho bank account id => DEKA journal id */
    protected array $journalIds = [];

    /** @var array<string, int> */
    protected array $counts = [];

    protected bool $beforeCutoff = false;

    /** When this run must stop between two records (a queued step). */
    protected ?float $deadline = null;

    /** Records written in this run, so a step always makes progress. */
    protected int $worked = 0;

    /**
     * Stop between two records once the step's time is up: the next queued
     * step continues, skipping what this one recorded. At least one record
     * is written per step, or a slow document would never get through.
     */
    protected function checkDeadline(): void
    {
        if ($this->deadline !== null && $this->worked > 0 && microtime(true) >= $this->deadline) {
            throw new ZohoImportPaused($this->counts);
        }
    }

    /**
     * The finished initial load of the company, if any: the latest finished
     * import run with a cut-off.
     */
    public static function initialLoad(int $companyId): ?ZohoImportRun
    {
        return ZohoImportRun::query()
            ->where('company_id', $companyId)
            ->where('mode', ZohoImportRun::MODE_IMPORT)
            ->where('status', ZohoImportRun::FINISHED)
            ->whereNotNull('cutoff_at')
            ->latest('id')
            ->first();
    }

    public static function initialLoadDoneMessage(\DateTimeInterface $cutoff): string
    {
        return 'The initial load into this company is already done (Zoho export of '.$cutoff->format('Y-m-d H:i').' UTC). '
            .'Since then new records are entered in both Zoho and DEKA, so importing a newer export would record them twice. '
            .'Compare the two instead, or import only records dated before the cut-off.';
    }

    /**
     * Record a finished initial load and its cut-off: when the Zoho export
     * was taken. A top-up keeps the original cut-off.
     */
    public static function recordInitialLoad(int $companyId, ?int $userId, ?string $exportedAt, array $counts, array $mapping = []): ZohoImportRun
    {
        $cutoff = static::initialLoad($companyId)?->cutoff_at
            ?? ($exportedAt ? Carbon::parse($exportedAt)->utc() : now()->utc());

        return ZohoImportRun::query()->create([
            'company_id'  => $companyId,
            'user_id'     => $userId,
            'mode'        => ZohoImportRun::MODE_IMPORT,
            'status'      => ZohoImportRun::FINISHED,
            'cutoff_at'   => $cutoff,
            'counts'      => $counts,
            // Kept so a later parallel-run compare groups Zoho's accounts
            // exactly as this load did.
            'mapping'     => $mapping,
            'started_at'  => now(),
            'finished_at' => now(),
        ]);
    }

    /** @var array<string, int>|null type|zoho id => DEKA id, for this run */
    protected ?array $records = null;

    /**
     * The tax rounding of the document being imported, while it is computed.
     */
    protected static ?string $rounding = null;

    public function __construct(
        protected PaymentRegistrar $registrar,
        protected MoveWorkflow $workflow,
    ) {}

    /**
     * Compute a document with the tax rounding Zoho used for it.
     *
     * Zoho rounds per document ("tax_rounding": entity_level rounds each tax
     * on the document total, item_level on every line); DEKA rounds per
     * company. Under the company's rounding, five equal lines of 429.84 at
     * 15.9 % + 6 % give 470.65 of tax where Zoho has 470.67, and the
     * invoice no longer matches what the customer was sent. So, for this
     * document only and only in this process, the tax settings read while it
     * is computed and posted carry Zoho's choice; nothing is saved, and no
     * other request sees it.
     *
     * @param  array<string, mixed>  $document
     */
    protected function withZohoRounding(array $document, Closure $callback): mixed
    {
        // Registered once per application: rebuilding the application (tests)
        // replaces the dispatcher, a static flag would not.
        if (! app()->bound('zoho-import.rounding-listener')) {
            app()->instance('zoho-import.rounding-listener', true);

            Event::listen(LoadingSettings::class, function (LoadingSettings $event): void {
                if ($event->settingsClass === TaxesSettings::class && static::$rounding !== null) {
                    $event->properties->put('tax_calculation_rounding_method', static::$rounding);
                }
            });
        }

        static::$rounding = match ($document['tax_rounding'] ?? null) {
            'entity_level' => 'round_globally',
            'item_level'   => 'round_per_line',
            default        => null,
        };

        try {
            return $callback();
        } finally {
            static::$rounding = null;
        }
    }

    /**
     * @param  array<string, mixed>  $mapping
     * @return array<string, int> what was recorded (or, on a dry run, would be)
     */
    public function import(Company $company, ZohoExport $export, array $mapping, bool $dryRun = false, bool $force = false, ?Closure $output = null, bool $beforeCutoff = false, ?float $deadline = null): array
    {
        // The import is a one-time initial load (user, 2026-11-26): from its
        // cut-off on, the client enters every new record in both Zoho and
        // DEKA while they run side by side, so importing a newer export would
        // record those twice. Only a top-up of records dated before the
        // cut-off may follow, and only when asked for explicitly.
        $load = static::initialLoad((int) $company->id);

        if ($load && ! $beforeCutoff) {
            throw new ZohoImportException(static::initialLoadDoneMessage($load->cutoff_at));
        }

        if ($beforeCutoff) {
            if (! $load) {
                throw new ZohoImportException('There is no finished initial load for this company, so there is no cut-off to limit the import to.');
            }

            $export = $export->before($load->cutoff_at);
        }

        $this->company = $company;
        $this->export = $export;
        $this->mapping = $mapping;
        $this->output = $output;
        $this->counts = [];
        $this->records = null;
        $this->beforeCutoff = $beforeCutoff;
        $this->deadline = $deadline;
        $this->worked = 0;
        // Documents need a creator: a move with no contact names its creator
        // as the counterpart, and quotations need a salesperson. The console
        // has no user, so the company's own creator stands in, as the
        // company setup does (AccountingSetupService::rowFor()).
        $this->creatorId = $company->creator_id ? (int) $company->creator_id : null;

        return app(CompanyContext::class)->runFor($company->id, function () use ($dryRun, $force): array {
            $this->preflight($dryRun, $force);

            if (! $dryRun) {
                $this->run();

                return $this->counts;
            }

            // A dry run does everything, totals checks included, then rolls
            // the whole of it back: the report is what a real run would do.
            DB::beginTransaction();

            try {
                $this->run();
            } finally {
                DB::rollBack();
            }

            return $this->counts;
        });
    }

    protected function run(): void
    {
        DB::transaction(function (): void {
            $this->protectNumbering();
            $this->createAccounts();
            $this->resolveJournals();
            $this->createTaxes();
        });

        $this->importContacts();
        $this->importItems();

        if ($this->quotationsEnabled()) {
            foreach ($this->export->estimates() as $estimate) {
                $this->document('estimate', $estimate['estimate_id'], 'Estimate '.$estimate['estimate_number'], fn () => $this->withZohoRounding($estimate, fn () => $this->importEstimate($estimate)));
            }
        } else {
            $this->say('Quotations skipped: Sales is not enabled for this company.');
        }

        foreach ($this->export->invoices() as $invoice) {
            $this->document('invoice', $invoice['invoice_id'], 'Invoice '.$invoice['invoice_number'], fn () => $this->withZohoRounding($invoice, fn () => $this->importInvoice($invoice)));
        }

        foreach ($this->export->payments() as $payment) {
            foreach ($payment['invoices'] as $application) {
                $this->document(
                    'payment',
                    $payment['payment_id'].':'.$application['invoice_id'],
                    'Payment '.$payment['payment_number'].' for invoice '.$application['invoice_number'],
                    fn () => $this->importPayment($payment, $application),
                );
            }
        }

        foreach ($this->export->expenses() as $expense) {
            $this->document('expense', $expense['expense_id'], 'Expense of '.$expense['date'].' ('.$expense['account_name'].', '.$expense['total'].')', fn () => $this->importExpense($expense));
        }

        foreach ($this->export->bankTransactions() as $transaction) {
            $this->document(
                'bank_transaction',
                (string) $transaction['transaction_id'],
                'Bank transaction of '.$transaction['date'].' ('.$transaction['transaction_type'].', '.$transaction['amount'].')',
                fn () => $this->importBankTransaction($transaction),
            );
        }

        DB::transaction(fn () => $this->continueNumbering());
    }

    // ------------------------------------------------------------------
    // Checks made before anything is written
    // ------------------------------------------------------------------

    protected function preflight(bool $dryRun, bool $force): void
    {
        if (($this->mapping['format'] ?? null) !== MappingProposer::FORMAT || ($this->mapping['version'] ?? null) !== MappingProposer::VERSION) {
            throw new ZohoImportException('The mapping file is not a zoho:map mapping (format or version differs).');
        }

        if (! $dryRun && ($this->mapping['approved'] ?? false) !== true) {
            throw new ZohoImportException('The mapping is not approved. Review it with the user, then set "approved": true.');
        }

        if ((int) ($this->mapping['company']['id'] ?? 0) !== (int) $this->company->id) {
            throw new ZohoImportException('The mapping was made for another company.');
        }

        if ((string) ($this->mapping['source']['organization_id'] ?? '') !== $this->export->organizationId()) {
            throw new ZohoImportException('The mapping was made from another Zoho organization.');
        }

        $currency = (string) $this->company->currency?->name;

        if ($this->export->currency() !== $currency) {
            throw new ZohoImportException("Zoho's currency ({$this->export->currency()}) is not the company's ({$currency}).");
        }

        if (! app(AccountingSetupService::class)->isSetUp($this->company)) {
            throw new ZohoImportException('Accounting is not set up for this company.');
        }

        PluginEntitlement::assertForCompany('accounts', (int) $this->company->id);
        PluginEntitlement::assertForCompany('invoices', (int) $this->company->id);

        if (! $this->creatorId) {
            throw new ZohoImportException('The company has no creator to record the documents under.');
        }

        // A closed month is never written into, by anyone (A2). Refused here
        // rather than half way through, and never bypassed.
        $lock = app(PeriodLock::class)->lockDateFor((int) $this->company->id);
        $first = $this->firstDocumentDate();

        if ($lock && $first && Carbon::parse($first)->startOfDay()->lte($lock)) {
            throw new ZohoImportException("The company's books are locked up to {$lock->toDateString()}, and the history starts on {$first}. Clear the lock date first, or the import would write into a closed period.");
        }

        // A top-up after the initial load meets the documents entered in
        // DEKA since the cut-off: those are expected, not someone else's.
        if (! $force && ! $this->beforeCutoff) {
            $foreign = $this->foreignDocumentCount();

            if ($foreign > 0) {
                throw new ZohoImportException("The company already has {$foreign} document(s) that this import did not create. Replaying Zoho's history on top of them would mix two sets of books; use --force only if that is intended.");
            }
        }

        $this->checkDocuments($currency);
        $this->checkMapping();
    }

    protected function firstDocumentDate(): ?string
    {
        $dates = array_filter([
            $this->export->estimates()[0]['date'] ?? null,
            $this->export->invoices()[0]['date'] ?? null,
            $this->export->payments()[0]['date'] ?? null,
            $this->export->expenses()[0]['date'] ?? null,
            $this->export->bankTransactions()[0]['date'] ?? null,
        ]);

        sort($dates);

        return $dates[0] ?? null;
    }

    /**
     * Moves, payments and orders of the company the import did not make.
     */
    public function foreignDocumentCount(): int
    {
        $companyId = (int) $this->company->id;

        $imported = fn (string $class) => ZohoImportRecord::query()
            ->where('company_id', $companyId)
            ->where('record_type', (new $class)->getMorphClass())
            ->select('record_id');

        $payments = Payment::withoutGlobalScopes()->where('company_id', $companyId)->whereNotIn('id', $imported(Payment::class))->count();

        $moves = Move::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->whereNotIn('id', $imported(Move::class))
            ->where(fn ($query) => $query->whereNull('origin_payment_id')
                ->orWhereNotIn('origin_payment_id', $imported(Payment::class)))
            ->count();

        $orders = class_exists(Order::class)
            ? Order::withoutGlobalScopes()->where('company_id', $companyId)->whereNotIn('id', $imported(Order::class))->count()
            : 0;

        return $payments + $moves + $orders;
    }

    /**
     * Refuses what this importer does not reproduce, naming the document,
     * before anything is written.
     */
    protected function checkDocuments(string $currency): void
    {
        $invoiceIds = array_column($this->export->invoices(), 'invoice_id');

        foreach ([...$this->export->invoices(), ...$this->export->estimates()] as $document) {
            $label = isset($document['invoice_number']) ? 'Invoice '.$document['invoice_number'] : 'Estimate '.$document['estimate_number'];

            if (($document['currency_code'] ?? $currency) !== $currency) {
                throw new ZohoImportException("{$label} is in {$document['currency_code']}; only {$currency} documents can be imported.");
            }

            foreach (['discount_total', 'adjustment', 'shipping_charge', 'roundoff_value', 'write_off_amount', 'credits_applied'] as $field) {
                if (abs((float) ($document[$field] ?? 0)) > self::TOLERANCE) {
                    throw new ZohoImportException("{$label} has a {$field} of {$document[$field]}, which this import does not reproduce.");
                }
            }

            foreach ($document['line_items'] as $line) {
                if (abs((float) ($line['discount'] ?? 0)) > self::TOLERANCE) {
                    throw new ZohoImportException("{$label} has a line discount, which this import does not reproduce.");
                }
            }
        }

        foreach ($this->export->invoices() as $invoice) {
            if (! in_array($invoice['status'], ['draft', 'void', 'sent', 'paid', 'overdue', 'partially_paid', 'unpaid'], true)) {
                throw new ZohoImportException("Invoice {$invoice['invoice_number']} has status \"{$invoice['status']}\", which this import does not know.");
            }
        }

        foreach ($this->export->payments() as $payment) {
            $label = 'Payment '.$payment['payment_number'];

            if (($payment['currency_code'] ?? $currency) !== $currency) {
                throw new ZohoImportException("{$label} is in {$payment['currency_code']}; only {$currency} payments can be imported.");
            }

            if (abs((float) $payment['unused_amount']) > self::TOLERANCE || abs((float) $payment['bank_charges']) > self::TOLERANCE) {
                throw new ZohoImportException("{$label} has an unused amount or bank charges, which this import does not reproduce.");
            }

            $applied = array_sum(array_column($payment['invoices'], 'amount_applied'));

            if (abs($applied - (float) $payment['amount']) > self::TOLERANCE) {
                throw new ZohoImportException("{$label} is not fully applied to invoices.");
            }

            foreach ($payment['invoices'] as $application) {
                if (! in_array($application['invoice_id'], $invoiceIds, true)) {
                    throw new ZohoImportException("{$label} pays invoice {$application['invoice_number']}, which is not in the export.");
                }
            }
        }

        foreach ($this->export->expenses() as $expense) {
            $label = 'Expense of '.$expense['date'];

            if (($expense['currency_code'] ?? $currency) !== $currency) {
                throw new ZohoImportException("{$label} is in {$expense['currency_code']}; only {$currency} expenses can be imported.");
            }

            if (abs((float) ($expense['tax_amount'] ?? 0)) > self::TOLERANCE || filled($expense['tax_id'] ?? null)) {
                throw new ZohoImportException("{$label} carries a tax, which this import does not reproduce.");
            }

            $lines = array_sum(array_map(fn (array $line): float => (float) $line['amount'], $expense['line_items']));

            if ($expense['line_items'] === [] || abs($lines - (float) $expense['total']) > self::TOLERANCE) {
                throw new ZohoImportException("{$label}: its lines do not add up to its total.");
            }
        }
    }

    /**
     * Every account, tax, bank, item and contact a document uses must have a
     * usable entry in the mapping, checked before anything is written.
     */
    protected function checkMapping(): void
    {
        foreach ($this->export->accountUsage() as $zohoId => $uses) {
            $entry = $this->mapping['accounts'][$zohoId] ?? null;

            if (! $entry || ! in_array($entry['action'] ?? null, ['map', 'create'], true)) {
                $name = $this->export->accounts()[$zohoId]['account_name'] ?? $zohoId;

                throw new ZohoImportException("Zoho account \"{$name}\" is used by {$uses} document line(s) but the mapping does not map or create it.");
            }

            if ($entry['action'] === 'map' && ! $this->companyAccount((string) $entry['code'])) {
                throw new ZohoImportException("The mapping maps Zoho account \"{$entry['zoho_name']}\" to code {$entry['code']}, which this company does not have.");
            }

            if ($entry['action'] === 'create' && (blank($entry['code']) || blank($entry['name']) || blank($entry['type']))) {
                throw new ZohoImportException("The mapping creates Zoho account \"{$entry['zoho_name']}\" without a code, name and type.");
            }
        }

        foreach ($this->export->lineTaxes() as $key => $tax) {
            $entry = $this->mapping['taxes'][$key] ?? null;

            if (! $entry || ! in_array($entry['action'] ?? null, ['map', 'create'], true)) {
                throw new ZohoImportException("Zoho tax \"{$tax['zoho_name']}\" is used but the mapping does not map or create it.");
            }

            if ($entry['action'] === 'map' && ! Tax::withoutGlobalScopes()->where('company_id', $this->company->id)->whereKey($entry['deka_tax_id'] ?? 0)->exists()) {
                throw new ZohoImportException("The mapping maps Zoho tax \"{$tax['zoho_name']}\" to a tax this company does not have.");
            }
        }

        foreach ($this->usedBankIds() as $zohoId) {
            $entry = $this->mapping['bank_accounts'][$zohoId] ?? null;

            if (! $entry || ! in_array($entry['action'] ?? null, ['map', 'create'], true)) {
                throw new ZohoImportException("Zoho bank account \"{$zohoId}\" is used but the mapping does not map or create a journal for it.");
            }
        }

        foreach ($this->export->items() as $zohoId => $item) {
            $entry = $this->mapping['items'][$zohoId] ?? null;

            if (! $entry || ($entry['action'] === 'map' && ! Product::withoutGlobalScopes()->where('company_id', $this->company->id)->whereKey($entry['deka_product_id'] ?? 0)->exists())) {
                throw new ZohoImportException("Zoho item \"{$item['name']}\" has no usable entry in the mapping.");
            }
        }

        foreach ($this->export->contacts() as $zohoId => $contact) {
            $entry = $this->mapping['contacts'][$zohoId] ?? null;

            if (! $entry || ($entry['action'] === 'map' && ! Partner::withoutGlobalScopes()->where('company_id', $this->company->id)->whereKey($entry['deka_partner_id'] ?? 0)->exists())) {
                throw new ZohoImportException("Zoho contact \"{$contact['contact_name']}\" has no usable entry in the mapping.");
            }
        }
    }

    /**
     * @return list<string>
     */
    protected function usedBankIds(): array
    {
        $ids = [
            ...array_column($this->export->payments(), 'account_id'),
            ...array_column($this->export->expenses(), 'paid_through_account_id'),
        ];

        // A bank transaction's bank or cash side (a transfer has two).
        $banks = $this->export->bankAccounts();

        foreach ($this->export->bankTransactions() as $transaction) {
            foreach ([$transaction['zoho_from_account_id'], $transaction['zoho_to_account_id']] as $accountId) {
                if ($accountId !== null && isset($banks[$accountId])) {
                    $ids[] = $accountId;
                }
            }
        }

        return array_values(array_unique(array_map('strval', $ids)));
    }

    // ------------------------------------------------------------------
    // Accounts, journals and taxes
    // ------------------------------------------------------------------

    protected function createAccounts(): void
    {
        foreach ($this->export->accountUsage() as $zohoId => $uses) {
            $entry = $this->mapping['accounts'][$zohoId];

            if ($entry['action'] === 'map') {
                $this->accountIds[$zohoId] = $this->companyAccount((string) $entry['code'])->id;

                continue;
            }

            if ($existing = $this->recorded('account', $zohoId)) {
                $this->accountIds[$zohoId] = $existing;

                continue;
            }

            // A code already in the chart (an earlier run, or one the user
            // added since the proposal) is used, never duplicated.
            $account = $this->companyAccount((string) $entry['code']);

            if (! $account) {
                $account = Account::create([
                    'code'         => (string) $entry['code'],
                    'name'         => (string) $entry['name'],
                    'account_type' => (string) $entry['type'],
                    'currency_id'  => $this->company->currency_id,
                    'reconcile'    => false,
                    'deprecated'   => false,
                    'non_trade'    => false,
                    'creator_id'   => $this->creatorId,
                ]);

                $account->companies()->attach($this->company->id);

                $this->count('accounts created');
            }

            $this->remember('account', $zohoId, $account);
            $this->accountIds[$zohoId] = $account->id;
        }
    }

    protected function companyAccount(string $code): ?Account
    {
        return Account::withoutGlobalScopes()
            ->where('code', $code)
            ->whereHas('companies', fn ($query) => $query->where('companies.id', $this->company->id))
            ->first();
    }

    protected function resolveJournals(): void
    {
        foreach ($this->usedBankIds() as $zohoId) {
            $entry = $this->mapping['bank_accounts'][$zohoId];

            if ($entry['action'] === 'create' && ! $this->recorded('journal', $zohoId)) {
                $journalId = app(PaymentWaysProvisioner::class)->addWay($this->company, (string) $entry['journal_name']);

                if (! $journalId) {
                    throw new ZohoImportException("No journal could be created for Zoho account \"{$entry['zoho_name']}\": the company has no BANK journal to copy.");
                }

                $this->remember('journal', $zohoId, Journal::withoutGlobalScopes()->findOrFail($journalId));
                $this->count('journals created');
            }

            $journal = $entry['action'] === 'create'
                ? Journal::withoutGlobalScopes()->find($this->recorded('journal', $zohoId))
                : Journal::withoutGlobalScopes()
                    ->where('company_id', $this->company->id)
                    ->where('code', $entry['journal_code'])
                    ->whereIn('type', [JournalType::BANK, JournalType::CASH])
                    ->first();

            if (! $journal || ! $journal->default_account_id) {
                throw new ZohoImportException("Journal {$entry['journal_code']} for Zoho account \"{$entry['zoho_name']}\" is missing or has no account.");
            }

            $this->journalIds[$zohoId] = $journal->id;

            // The bank or cash account itself is the journal's account, so
            // a Zoho account mapped "to its journal" lands on the same line.
            $this->accountIds[$zohoId] ??= (int) $journal->default_account_id;

            if ((int) $this->accountIds[$zohoId] !== (int) $journal->default_account_id) {
                throw new ZohoImportException("Zoho account \"{$entry['zoho_name']}\" is mapped to an account other than its journal's own account.");
            }
        }
    }

    protected function createTaxes(): void
    {
        $template = $this->templateTax();
        $children = [];

        foreach ($this->export->lineTaxes() as $key => $tax) {
            $entry = $this->mapping['taxes'][$key];

            if ($entry['action'] === 'map') {
                $this->taxIds[$key] = (int) $entry['deka_tax_id'];

                continue;
            }

            if ($existing = $this->recorded('tax', $key)) {
                $this->taxIds[$key] = $existing;

                continue;
            }

            $include = $tax['inclusive'] ? TaxIncludeOverride::TAX_INCLUDED : TaxIncludeOverride::TAX_EXCLUDED;
            $suffix = $tax['inclusive'] ? ' (price includes tax)' : '';

            if (count($tax['components']) === 1) {
                $created = $this->createPercentTax($template, (string) $entry['name'], (float) $tax['components'][0]['rate'], $include);
            } else {
                $created = $this->newTax($template, [
                    'name'                   => (string) $entry['name'],
                    'amount_type'            => AmountType::GROUP,
                    'amount'                 => 0,
                    'price_include_override' => $include,
                ]);

                $childIds = [];

                foreach ($tax['components'] as $component) {
                    $childKey = $component['zoho_tax_id'].':'.$include->value;

                    $children[$childKey] ??= $this->createPercentTax(
                        $template,
                        $component['name'].' '.$this->rate($component['rate']).' %'.$suffix,
                        (float) $component['rate'],
                        $include,
                    )->id;

                    $childIds[] = $children[$childKey];
                }

                $created->childrenTaxes()->sync($childIds);
            }

            $this->remember('tax', $key, $created);
            $this->taxIds[$key] = $created->id;
        }
    }

    /**
     * The company's own sale VAT: its tax group, country and distribution
     * lines (which account the tax is posted to) are what every created tax
     * copies, so imported tax lands where the company's VAT already does.
     */
    protected function templateTax(): ?Tax
    {
        return Tax::withoutGlobalScopes()
            ->where('company_id', $this->company->id)
            ->where('type_tax_use', TypeTaxUse::SALE)
            ->where('amount_type', AmountType::PERCENT)
            ->where('amount', '>', 0)
            ->whereHas('invoiceRepartitionLines', fn ($query) => $query->where('repartition_type', RepartitionType::TAX)->whereNotNull('account_id'))
            ->orderBy('id')
            ->first();
    }

    protected function createPercentTax(?Tax $template, string $name, float $rate, TaxIncludeOverride $include): Tax
    {
        if (! $template) {
            throw new ZohoImportException('The company has no sale tax with an account to copy; set up its VAT first.');
        }

        $tax = $this->newTax($template, [
            'name'                   => $name,
            'amount_type'            => AmountType::PERCENT,
            'amount'                 => $rate,
            'price_include_override' => $include,
        ]);

        foreach (TaxPartition::withoutGlobalScopes()->where('tax_id', $template->id)->get() as $line) {
            TaxPartition::create([
                'tax_id'             => $tax->id,
                'company_id'         => $this->company->id,
                'account_id'         => $line->account_id,
                'sort'               => $line->sort,
                'repartition_type'   => $line->repartition_type,
                'document_type'      => $line->document_type,
                'use_in_tax_closing' => $line->use_in_tax_closing,
                'factor_percent'     => $line->factor_percent,
                'creator_id'         => $this->creatorId,
            ]);
        }

        if (! TaxPartition::withoutGlobalScopes()->where('tax_id', $tax->id)->where('document_type', DocumentType::INVOICE)->exists()) {
            throw new ZohoImportException("Tax \"{$name}\" got no distribution lines from the company's VAT.");
        }

        $this->count('taxes created');

        return $tax;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    protected function newTax(?Tax $template, array $attributes): Tax
    {
        $tax = Tax::create(array_merge([
            'company_id'    => $this->company->id,
            'tax_group_id'  => $template?->tax_group_id,
            'country_id'    => $template?->country_id,
            'type_tax_use'  => TypeTaxUse::SALE,
            'tax_scope'     => $template?->tax_scope,
            'invoice_label' => $attributes['name'],
            'is_active'     => true,
            'creator_id'    => $this->creatorId,
        ], $attributes));

        if ($attributes['amount_type'] === AmountType::GROUP) {
            $this->count('taxes created');
        }

        return $tax;
    }

    protected function rate(float $rate): string
    {
        return rtrim(rtrim(number_format($rate, 2, '.', ''), '0'), '.');
    }

    // ------------------------------------------------------------------
    // Contacts and items
    // ------------------------------------------------------------------

    protected function importContacts(): void
    {
        $receivable = $this->accountIds[$this->export->receivableAccountId() ?? ''] ?? null;
        $payable = $this->companyAccount('211000')?->id;

        foreach ($this->export->contacts() as $zohoId => $contact) {
            $entry = $this->mapping['contacts'][$zohoId];

            // "skip": a Zoho demo contact no document uses (user decision).
            if (in_array($entry['action'], ['map', 'skip'], true) || $this->recorded('contact', $zohoId)) {
                continue;
            }

            $this->checkDeadline();

            DB::transaction(function () use ($zohoId, $contact, $receivable, $payable): void {
                $address = $contact['billing_address'] ?? [];
                $isVendor = ($contact['contact_type'] ?? '') === 'vendor';

                $partner = Partner::create([
                    'name'         => trim((string) $contact['contact_name']),
                    'account_type' => ($contact['customer_sub_type'] ?? '') === 'individual' ? PartnerAccountType::INDIVIDUAL : PartnerAccountType::COMPANY,
                    'email'        => filled($contact['email'] ?? null) ? trim((string) $contact['email']) : null,
                    'phone'        => filled($contact['phone'] ?? null) ? (string) $contact['phone'] : null,
                    'mobile'       => filled($contact['mobile'] ?? null) ? (string) $contact['mobile'] : null,
                    'website'      => filled($contact['website'] ?? null) ? (string) $contact['website'] : null,
                    'tax_id'       => filled($contact['tax_reg_no'] ?? null) ? (string) $contact['tax_reg_no'] : null,
                    'street1'      => filled($address['address'] ?? null) ? (string) $address['address'] : null,
                    'street2'      => filled($address['street2'] ?? null) ? (string) $address['street2'] : null,
                    'city'         => filled($address['city'] ?? null) ? (string) $address['city'] : null,
                    'zip'          => filled($address['zip'] ?? null) ? (string) $address['zip'] : null,
                    'country_id'   => $this->countryId($address['country'] ?? null),
                    'company_id'   => $this->company->id,
                    'creator_id'   => $this->creatorId,
                    // Listed among customers or vendors from the start; posting
                    // raises the rank further, as for any contact.
                    'customer_rank' => $isVendor ? 0 : 1,
                    'supplier_rank' => $isVendor ? 1 : 0,
                    // Per-company receivable and payable: the console reads
                    // accounts without the company scope, so an unset one
                    // could be resolved to the wrong receivable.
                    'property_account_receivable_id' => $receivable,
                    'property_account_payable_id'    => $payable,
                    'comment'                        => filled($contact['notes'] ?? null) ? e((string) $contact['notes']) : null,
                    'property_payment_term_id'       => $this->paymentTermId($contact['payment_terms_label'] ?? null),
                ]);

                $this->importContactPersons($partner, $contact);

                $this->remember('contact', $zohoId, $partner);
            });

            $this->count('contacts created');
            $this->worked++;
        }
    }

    /**
     * Zoho's contact persons become the company contact's own people, as
     * DEKA files a person who works for a company.
     *
     * @param  array<string, mixed>  $contact
     */
    protected function importContactPersons(Partner $company, array $contact): void
    {
        foreach ($contact['contact_persons'] ?? [] as $person) {
            $name = trim(implode(' ', array_filter([$person['salutation'] ?? null, $person['first_name'] ?? null, $person['last_name'] ?? null])));

            if ($name === '' || $name === $company->name) {
                continue;
            }

            Partner::create([
                'name'         => $name,
                'account_type' => PartnerAccountType::INDIVIDUAL,
                'parent_id'    => $company->id,
                'email'        => filled($person['email'] ?? null) ? trim((string) $person['email']) : null,
                'phone'        => filled($person['phone'] ?? null) ? (string) $person['phone'] : null,
                'mobile'       => filled($person['mobile'] ?? null) ? (string) $person['mobile'] : null,
                'job_title'    => filled($person['designation'] ?? null) ? (string) $person['designation'] : null,
                'company_id'   => $company->company_id,
                'creator_id'   => $this->creatorId,
            ]);

            $this->count('contact persons created');
        }
    }

    /**
     * Zoho's payment terms by name, to DEKA's own terms. Only exact
     * equivalents: a "Custom" Zoho term has none, and imported invoices
     * keep their own due dates either way.
     */
    protected const PAYMENT_TERMS = [
        'due on receipt'        => 'Immediate Payment',
        'net 15'                => '15 Days',
        'net 30'                => '30 Days',
        'net 45'                => '45 Days',
        'due end of next month' => 'End of Following Month',
    ];

    protected function paymentTermId(?string $label): ?int
    {
        $name = self::PAYMENT_TERMS[mb_strtolower(trim((string) $label))] ?? null;

        if (! $name) {
            return null;
        }

        $id = PaymentTerm::withoutGlobalScopes()
            ->where('name', $name)
            ->where(fn ($query) => $query->whereNull('company_id')->orWhere('company_id', $this->company->id))
            ->value('id');

        return $id === null ? null : (int) $id;
    }

    /**
     * Zoho's unit names to DEKA units: "pcs" and friends are Units, any
     * other name a DEKA unit of the same name, else Units.
     */
    protected function unitId(?string $unit, UOM $default): int
    {
        $unit = mb_strtolower(trim((string) $unit));

        if ($unit === '' || in_array($unit, ['pcs', 'pc', 'piece', 'pieces', 'unit', 'units', 'nos', 'no', 'each', 'ea'], true)) {
            return (int) $default->id;
        }

        return (int) (UOM::query()->whereRaw('LOWER(name) = ?', [$unit])->value('id') ?? $default->id);
    }

    protected function countryId(?string $name): ?int
    {
        if (blank($name)) {
            return null;
        }

        return Country::query()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower(trim($name))])
            ->value('id');
    }

    protected function importItems(): void
    {
        $uom = UOM::query()->where('name', 'Units')->first() ?? UOM::query()->orderBy('id')->first();
        $categoryId = app(CompanyCategoryProvisioner::class)->defaultCategoryId((int) $this->company->id);

        if (! $uom || ! $categoryId) {
            throw new ZohoImportException('The company has no product category or unit of measure to file products under.');
        }

        foreach ($this->export->items() as $zohoId => $item) {
            $entry = $this->mapping['items'][$zohoId];

            if ($entry['action'] === 'map' || $this->recorded('item', $zohoId)) {
                continue;
            }

            $this->checkDeadline();

            DB::transaction(function () use ($zohoId, $item, $entry, $uom, $categoryId): void {
                $product = Product::create([
                    'type'                 => ($entry['product_type'] ?? 'service') === 'goods' ? ProductType::GOODS : ProductType::SERVICE,
                    'name'                 => trim((string) $item['name']),
                    'reference'            => filled($item['sku'] ?? null) ? (string) $item['sku'] : null,
                    'price'                => (float) ($item['rate'] ?? 0),
                    'cost'                 => (float) ($item['purchase_rate'] ?? 0),
                    'description_sale'     => filled($item['description'] ?? null) ? (string) $item['description'] : null,
                    'description_purchase' => filled($item['purchase_description'] ?? null) ? (string) $item['purchase_description'] : null,
                    'uom_id'               => $this->unitId($item['unit'] ?? null, $uom),
                    'uom_po_id'            => $this->unitId($item['unit'] ?? null, $uom),
                    'category_id'          => $categoryId,
                    'company_id'           => $this->company->id,
                    'sales_ok'             => true,
                    'purchase_ok'          => str_contains((string) ($item['item_type'] ?? ''), 'purchase'),
                ]);

                $product->forceFill(['creator_id' => $this->creatorId])->saveQuietly();

                $this->applyIncomeAccount($product, (string) ($item['account_id'] ?? ''));

                // The item's default sale tax, so a new line for it starts
                // with the tax Zoho put on it. Imported lines carry their own.
                $taxId = $this->taxIds[ZohoExport::taxKey((string) ($item['tax_id'] ?? ''), false)]
                    ?? $this->taxIds[ZohoExport::taxKey((string) ($item['tax_id'] ?? ''), true)]
                    ?? null;

                if (filled($item['tax_id'] ?? null) && $taxId) {
                    $product->productTaxes()->sync([$taxId]);
                }

                $this->remember('item', $zohoId, $product);
            });

            $this->count('products created');
            $this->worked++;
        }
    }

    /**
     * Invoice lines with a product are posted to the product's income
     * account, so a Zoho item booked to an account other than the company's
     * default income keeps that account here.
     */
    protected function applyIncomeAccount(Product $product, string $zohoAccountId): void
    {
        $wanted = $this->accountIds[$zohoAccountId] ?? null;

        if (! $wanted || (int) $product->getAccounts((int) $this->company->id)['income']?->id === (int) $wanted) {
            return;
        }

        ProductCompanyAccount::updateOrCreate(
            ['product_id' => $product->id, 'company_id' => $this->company->id],
            ['property_account_income_id' => $wanted],
        );
    }

    // ------------------------------------------------------------------
    // Documents
    // ------------------------------------------------------------------

    /**
     * One document, in its own transaction, remembered with its Zoho id.
     * Already recorded: skipped. Failed: the run stops and names it.
     */
    protected function document(string $type, string $zohoId, string $label, Closure $import): void
    {
        if ($this->recorded($type, $zohoId)) {
            $this->count($type.'s skipped (already imported)');

            return;
        }

        $this->checkDeadline();

        try {
            DB::transaction(function () use ($type, $zohoId, $import): void {
                $record = $import();

                $this->remember($type, $zohoId, $record);
            });
        } catch (ZohoImportException $exception) {
            throw new ZohoImportException("{$label}: {$exception->getMessage()}", previous: $exception);
        } catch (Throwable $exception) {
            // A database error quotes the whole statement, client data and
            // all; only the reason is kept for the person running the import.
            $reason = Str::limit(Str::before($exception->getMessage(), ' (Connection:'), 300);

            throw new ZohoImportException("{$label} could not be recorded: {$reason}", previous: $exception);
        }

        $this->count($type.'s imported');
        $this->worked++;
    }

    protected function partnerId(string $zohoContactId): int
    {
        $entry = $this->mapping['contacts'][$zohoContactId] ?? null;

        $id = ($entry['action'] ?? null) === 'map'
            ? (int) $entry['deka_partner_id']
            : $this->recorded('contact', $zohoContactId);

        if (! $id) {
            throw new ZohoImportException("its Zoho contact {$zohoContactId} is not in the export.");
        }

        return $id;
    }

    protected function productId(?string $zohoItemId): ?int
    {
        if (blank($zohoItemId)) {
            return null;
        }

        $entry = $this->mapping['items'][$zohoItemId] ?? null;

        $id = ($entry['action'] ?? null) === 'map'
            ? (int) $entry['deka_product_id']
            : $this->recorded('item', $zohoItemId);

        if (! $id) {
            throw new ZohoImportException("its Zoho item {$zohoItemId} is not in the export.");
        }

        return $id;
    }

    /**
     * @param  array<string, mixed>  $line
     * @return list<int>
     */
    protected function lineTaxIds(array $line, bool $inclusive): array
    {
        if (blank($line['tax_id'] ?? null)) {
            return [];
        }

        return [$this->taxIds[ZohoExport::taxKey((string) $line['tax_id'], $inclusive)]];
    }

    /**
     * @param  array<string, mixed>  $line
     */
    protected function lineName(array $line): string
    {
        $name = trim((string) ($line['name'] ?? ''));
        $description = trim((string) ($line['description'] ?? ''));

        return trim($name.($description !== '' && $description !== $name ? "\n".$description : '')) ?: '-';
    }

    /**
     * Quantity and unit price of a line, as DEKA can hold them.
     *
     * DEKA keeps unit prices to 4 decimals. Zoho keeps the line total the
     * person typed and shows a rounded rate: 942 at "9.023355" is 8,500.00
     * in Zoho, and 8,499.99 or 8,500.04 when multiplied out again, so the
     * invoice would no longer be what the customer was sent. Such a line is
     * recorded as one unit at Zoho's line total, and its quantity and rate
     * are written into the description so nothing is lost.
     *
     * @param  array<string, mixed>  $line
     * @return array{name: string, quantity: float, price: float}
     */
    protected function linePricing(array $line, bool $inclusive): array
    {
        $quantity = (float) $line['quantity'];
        $rate = (float) $line['rate'];
        $name = $this->lineName($line);

        if ($inclusive || abs(round($quantity * round($rate, 4), 2) - (float) $line['item_total']) <= self::TOLERANCE) {
            return ['name' => $name, 'quantity' => $quantity, 'price' => $rate];
        }

        return [
            'name'     => $name."\n".rtrim(rtrim(number_format($quantity, 4, '.', ''), '0'), '.').' x '.$line['rate'],
            'quantity' => 1.0,
            'price'    => (float) $line['item_total'],
        ];
    }

    /**
     * @param  array<string, mixed>  $estimate
     */
    protected function importEstimate(array $estimate): Order
    {
        $inclusive = (bool) $estimate['is_inclusive_tax'];
        $partnerId = $this->partnerId((string) $estimate['customer_id']);

        $order = Order::create([
            'name'             => (string) $estimate['estimate_number'],
            'company_id'       => $this->company->id,
            'partner_id'       => $partnerId,
            'currency_id'      => $this->company->currency_id,
            'date_order'       => $estimate['date'],
            'validity_date'    => filled($estimate['expiry_date'] ?? null) ? $estimate['expiry_date'] : null,
            'client_order_ref' => filled($estimate['reference_number'] ?? null) ? (string) $estimate['reference_number'] : null,
            'note'             => $this->estimateNote($estimate),
            'state'            => $this->orderState((string) $estimate['status']),
            'creator_id'       => $this->creatorId,
            'user_id'          => $this->creatorId,
        ]);

        foreach ($estimate['line_items'] as $line) {
            $productId = $this->productId($line['item_id'] ?? null);

            $orderLine = OrderLine::create([
                'order_id'         => $order->id,
                'company_id'       => $this->company->id,
                'currency_id'      => $order->currency_id,
                'order_partner_id' => $partnerId,
                'product_id'       => $productId,
                'product_uom_id'   => $productId ? Product::withoutGlobalScopes()->find($productId)?->uom_id : null,
                'name'             => $this->linePricing($line, $inclusive)['name'],
                'product_uom_qty'  => $this->linePricing($line, $inclusive)['quantity'],
                'product_qty'      => $this->linePricing($line, $inclusive)['quantity'],
                'price_unit'       => $this->linePricing($line, $inclusive)['price'],
                'discount'         => 0,
                'customer_lead'    => 0,
                'state'            => $order->state,
                'creator_id'       => $this->creatorId,
                'salesman_id'      => $this->creatorId,
            ]);

            if ($taxes = $this->lineTaxIds($line, $inclusive)) {
                $orderLine->taxes()->sync($taxes);
            }
        }

        $order = SaleOrderFacade::computeSaleOrder($order->refresh());

        $this->assertTotals($order->amount_untaxed, $order->amount_tax, $order->amount_total, $estimate);

        $this->keepZohoDetails($order, $estimate);

        return $order;
    }

    /**
     * An estimate Zoho marks "invoiced" stays a quotation (sent), never a
     * confirmed order: its invoice is imported on its own, and a confirmed
     * order beside it would count the same sale twice in the sales figures
     * (DirectInvoices counts invoices that no order produced). The note says
     * which invoice it became.
     */
    protected function orderState(string $status): OrderState
    {
        return match ($status) {
            'draft'                         => OrderState::DRAFT,
            'declined', 'expired', 'void'   => OrderState::CANCEL,
            default                         => OrderState::SENT,
        };
    }

    /**
     * @param  array<string, mixed>  $estimate
     */
    protected function estimateNote(array $estimate): ?string
    {
        $parts = array_filter([
            filled($estimate['notes'] ?? null) ? (string) $estimate['notes'] : null,
            'Imported from Zoho Books (status: '.$estimate['status'].').',
        ]);

        if (($estimate['status'] ?? '') === 'invoiced') {
            $numbers = collect($this->export->invoices())
                ->whereIn('invoice_id', (array) ($estimate['invoice_ids'] ?? []))
                ->pluck('invoice_number')
                ->implode(', ');

            $parts[] = 'Invoiced in Zoho Books as '.($numbers ?: 'an invoice').'; the invoice is the sale.';
        }

        return e(implode("\n", $parts));
    }

    /**
     * @param  array<string, mixed>  $invoice
     */
    protected function importInvoice(array $invoice): Move
    {
        $inclusive = (bool) $invoice['is_inclusive_tax'];
        $journal = Journal::withoutGlobalScopes()
            ->where('company_id', $this->company->id)
            ->where('type', JournalType::SALE)
            ->orderBy('id')
            ->first();

        if (! $journal) {
            throw new ZohoImportException('the company has no customer invoice journal.');
        }

        $move = Move::create([
            'move_type'         => MoveType::OUT_INVOICE,
            'state'             => MoveState::DRAFT,
            'company_id'        => $this->company->id,
            'journal_id'        => $journal->id,
            'currency_id'       => $this->company->currency_id,
            'partner_id'        => $this->partnerId((string) $invoice['customer_id']),
            // Zoho's own number (user decision): set before the move is
            // created, so DEKA's created hook does not number it.
            'name'              => (string) $invoice['invoice_number'],
            'date'              => $invoice['date'],
            'invoice_date'      => $invoice['date'],
            'invoice_date_due'  => $invoice['due_date'] ?: $invoice['date'],
            'payment_reference' => (string) $invoice['invoice_number'],
            'reference'         => filled($invoice['reference_number'] ?? null) ? (string) $invoice['reference_number'] : null,
            'narration'         => $this->invoiceNarration($invoice),
            'creator_id'        => $this->creatorId,
            'invoice_user_id'   => $this->creatorId,
        ]);

        foreach ($invoice['line_items'] as $line) {
            $productId = $this->productId($line['item_id'] ?? null);
            $accountId = $this->accountIds[(string) $line['account_id']] ?? null;

            if (! $accountId) {
                throw new ZohoImportException('a line has no mapped account.');
            }

            $moveLine = $move->lines()->create([
                'name'         => $this->linePricing($line, $inclusive)['name'],
                'date'         => $move->date,
                'creator_id'   => $this->creatorId,
                'parent_state' => $move->state,
                // Set explicitly (as Logistics' ExpensePoster does): a line
                // created without a display type is costed through the
                // "default" branch, which replaces account_id with the
                // journal's account, and its totals are zeroed.
                'display_type' => DisplayType::PRODUCT,
                'product_id'   => $productId,
                'uom_id'       => $productId ? Product::withoutGlobalScopes()->find($productId)?->uom_id : null,
                'quantity'     => $this->linePricing($line, $inclusive)['quantity'],
                'price_unit'   => $this->linePricing($line, $inclusive)['price'],
                'discount'     => 0,
                'company_id'   => $this->company->id,
                'currency_id'  => $move->currency_id,
                'partner_id'   => $move->partner_id,
                'account_id'   => $accountId,
            ]);

            if ($taxes = $this->lineTaxIds($line, $inclusive)) {
                $moveLine->taxes()->sync($taxes);
            }

            if ((int) $moveLine->refresh()->account_id !== (int) $accountId) {
                throw new ZohoImportException('a line would be posted to another account than the mapping says (check the product\'s income account).');
            }
        }

        $move = AccountFacade::computeAccountMove($move->refresh());

        $this->assertTotals($move->amount_untaxed, $move->amount_tax, $move->amount_total, $invoice);

        $move = match ($invoice['status']) {
            'draft' => $move,
            // Never in the books: cancelled straight from draft, which keeps
            // the number and needs no reason (MoveWorkflow::cancel()).
            'void'  => $this->workflow->cancel($move),
            default => AccountFacade::confirmMove($move),
        };

        if ($move->state === MoveState::POSTED) {
            $this->assertTotals($move->amount_untaxed, $move->amount_tax, $move->amount_total, $invoice);
        }

        $this->keepZohoDetails($move, $invoice);

        return $move;
    }

    /**
     * Zoho's customer notes and terms both print on the invoice, so both are
     * kept in the one field DEKA prints there, notes first.
     *
     * @param  array<string, mixed>  $invoice
     */
    protected function invoiceNarration(array $invoice): ?string
    {
        $parts = array_filter([
            trim((string) ($invoice['notes'] ?? '')),
            trim((string) ($invoice['terms'] ?? '')),
        ]);

        return $parts === [] ? null : e(implode("\n\n", $parts));
    }

    /**
     * What Zoho holds about a document and DEKA has no field for (subject,
     * salesperson, payment terms by name, Zoho status, attachment names) is
     * kept as one internal note on the document, so nothing is lost. A note
     * is not a message: nobody is emailed or notified.
     *
     * @param  array<string, mixed>  $document
     */
    protected function keepZohoDetails(Model $record, array $document): void
    {
        if (! method_exists($record, 'addMessage')) {
            return;
        }

        $details = array_filter([
            'Subject'       => $document['subject_content'] ?? null,
            'Salesperson'   => $document['salesperson_name'] ?? null,
            'Payment terms' => $document['payment_terms_label'] ?? null,
            'Zoho status'   => $document['status'] ?? null,
            'Attachments'   => collect($document['documents'] ?? [])->pluck('file_name')->filter()->implode(', ') ?: null,
            'Receipt'       => $document['expense_receipt_name'] ?? null,
        ], fn ($value) => filled($value));

        if ($details === []) {
            return;
        }

        $record->addMessage([
            'type'       => 'note',
            'company_id' => $this->company->id,
            'body'       => 'Imported from Zoho Books.<br>'.collect($details)->map(fn ($value, $label) => e($label).': '.e((string) $value))->implode('<br>'),
        ]);
    }

    /**
     * @param  array<string, mixed>  $document
     */
    protected function assertTotals(mixed $untaxed, mixed $tax, mixed $total, array $document): void
    {
        $differences = [];

        foreach ([['untaxed', $untaxed, $document['sub_total']], ['tax', $tax, $document['tax_total']], ['total', $total, $document['total']]] as [$label, $deka, $zoho]) {
            if (abs((float) $deka - (float) $zoho) > self::TOLERANCE) {
                $differences[] = "{$label} ".number_format((float) $deka, 2, '.', '')." instead of Zoho's ".number_format((float) $zoho, 2, '.', '');
            }
        }

        if ($differences !== []) {
            throw new ZohoImportException('DEKA computes '.implode(', ', $differences).'. Nothing was recorded for it; check the tax mapping.');
        }
    }

    /**
     * One Zoho payment applied to one invoice, registered with DEKA's own
     * registrar, so the invoice becomes paid or partly paid exactly as a
     * payment made in the Pay window does.
     *
     * The money goes straight to the bank or cash account of the journal it
     * was deposited to, not to "Outstanding receipts": these payments were
     * banked long ago, and waiting for a statement match would leave every
     * bank balance at zero. Tax the customer withheld is written off to the
     * mapped account, as Zoho does, so the invoice is settled in full.
     *
     * @param  array<string, mixed>  $payment
     * @param  array<string, mixed>  $application
     */
    protected function importPayment(array $payment, array $application): Payment
    {
        $invoiceId = $this->recorded('invoice', (string) $application['invoice_id']);
        $invoice = $invoiceId ? Move::withoutGlobalScopes()->find($invoiceId) : null;

        if (! $invoice || $invoice->state !== MoveState::POSTED) {
            throw new ZohoImportException("invoice {$application['invoice_number']} is not imported as a confirmed invoice.");
        }

        $journal = Journal::withoutGlobalScopes()->findOrFail($this->journalIds[(string) $payment['account_id']]);
        $withheld = (float) ($application['tax_amount_withheld'] ?? 0);
        $amount = (float) $application['amount_applied'];
        $residualBefore = (float) $invoice->amount_residual;

        $lines = $invoice->paymentTermLines->filter(fn ($line) => ! $line->reconciled);

        if ($lines->isEmpty()) {
            throw new ZohoImportException("invoice {$application['invoice_number']} has nothing left to pay in DEKA.");
        }

        // The way to pay is chosen before the register is saved: reading a
        // register back computes its bank-account flags from it, and a bank
        // journal's register without one fails there.
        $methodLineId = $journal->getAvailablePaymentMethodLines(PaymentType::RECEIVE)->first()?->id;

        if (! $methodLineId) {
            throw new ZohoImportException("journal {$journal->code} has no way to receive a payment.");
        }

        $register = PaymentRegister::create([
            'journal_id'                  => $journal->id,
            'payment_method_line_id'      => $methodLineId,
            'currency_id'                 => $invoice->currency_id,
            'company_id'                  => $this->company->id,
            'partner_id'                  => $invoice->partner_id,
            'payment_type'                => PaymentType::RECEIVE,
            'payment_date'                => $payment['date'],
            'amount'                      => $amount,
            'installments_mode'           => 'full',
            'communication'               => $this->paymentMemo($payment, $application),
            'payment_difference_handling' => $withheld > 0 ? 'reconcile' : 'open',
            'writeoff_account_id'         => $withheld > 0 ? $this->accountIds[(string) $payment['tax_account_id']] : null,
            'writeoff_label'              => $withheld > 0 ? 'Tax withheld by the customer' : null,
        ]);

        $register->lines()->sync($lines->pluck('id')->all());
        $register->refresh();
        $register->computeFromLines();
        $register->save();
        $register->refresh();

        if ($withheld > 0 && abs($register->payment_difference - $withheld) > self::TOLERANCE) {
            throw new ZohoImportException('the tax withheld ('.$withheld.') does not equal what is left on the invoice after the payment ('.$register->payment_difference.').');
        }

        $batch = $register->batches[0];
        $attributes = $this->registrar->buildPaymentAttributesForSingleBatch($register, $batch);
        $attributes['outstanding_account_id'] = $journal->default_account_id;

        $jobs = [['create_vals' => $attributes, 'to_reconcile' => $batch['lines'], 'batch' => $batch]];

        $this->registrar->createPayments($register, $jobs, editMode: true);
        $this->registrar->postPayments($jobs);
        $this->registrar->reconcilePayments($jobs);

        $invoice->refresh();
        $expected = $residualBefore - $amount - $withheld;

        if (abs((float) $invoice->amount_residual - $expected) > self::TOLERANCE) {
            throw new ZohoImportException('the invoice was left with '.$invoice->amount_residual.' to pay instead of '.round($expected, 2).'.');
        }

        return $jobs[0]['payment']->refresh();
    }

    /**
     * @param  array<string, mixed>  $payment
     * @param  array<string, mixed>  $application
     */
    protected function paymentMemo(array $payment, array $application): string
    {
        return implode(' - ', array_filter([
            $application['invoice_number'],
            'Zoho payment '.$payment['payment_number'],
            $payment['payment_mode'] ?: null,
            filled($payment['reference_number'] ?? null) ? $payment['reference_number'] : null,
        ]));
    }

    /**
     * A Zoho expense as a journal entry (user decision): the expense account
     * debited, the account it was paid from credited, on the expense date,
     * with the vendor on the lines when Zoho has one.
     *
     * It is posted in Miscellaneous Operations. DEKA keeps bank and cash
     * journals for payments and statement lines (Move::getValidJournalTypes()
     * moves any other entry to the general journal), and a bank balance is
     * the balance of its account, which the credit line hits directly.
     *
     * @param  array<string, mixed>  $expense
     */
    protected function importExpense(array $expense): Move
    {
        $journal = Journal::withoutGlobalScopes()
            ->where('company_id', $this->company->id)
            ->where('type', JournalType::GENERAL)
            ->orderBy('id')
            ->first();

        if (! $journal) {
            throw new ZohoImportException('the company has no Miscellaneous Operations journal.');
        }

        $partnerId = filled($expense['vendor_id'] ?? null) ? $this->partnerId((string) $expense['vendor_id']) : null;
        $paidFrom = $this->accountIds[(string) $expense['paid_through_account_id']] ?? null;

        if (! $paidFrom) {
            throw new ZohoImportException('its paid-through account is not mapped.');
        }

        $lines = [];

        foreach ($expense['line_items'] as $line) {
            $accountId = $this->accountIds[(string) $line['account_id']] ?? null;

            if (! $accountId) {
                throw new ZohoImportException('a line has no mapped account.');
            }

            $lines[] = [
                'account_id' => $accountId,
                'amount'     => (float) $line['amount'],
                'name'       => (string) ($line['description'] ?: $line['account_name']),
            ];
        }

        // The one expense-entry builder (accounts' ExpenseEntry), so the
        // Expenses screen planned on 2026-11-26 posts the same entry.
        $move = app(ExpenseEntry::class)->post(
            companyId: (int) $this->company->id,
            currencyId: (int) $this->company->currency_id,
            journalId: (int) $journal->id,
            date: (string) $expense['date'],
            lines: $lines,
            paidFromAccountId: (int) $paidFrom,
            paidFromLabel: (string) ($expense['paid_through_account_name'] ?? 'Zoho expense'),
            partnerId: $partnerId,
            reference: trim('Zoho expense'.(filled($expense['reference_number'] ?? null) ? ' '.$expense['reference_number'] : '')),
            narration: filled($expense['description'] ?? null) ? e((string) $expense['description']) : null,
            creatorId: $this->creatorId,
        );

        $this->keepZohoDetails($move, array_intersect_key($expense, array_flip(['documents', 'expense_receipt_name'])));

        return $move;
    }

    /**
     * A Zoho bank transaction that is a money movement of its own (an owner
     * contribution or drawing, a deposit, a transfer between accounts, other
     * income...): one journal entry, the account the money went to debited
     * and the account it came from credited, both through the approved
     * mapping. Without these, every bank balance misses the money the owner
     * put in (seen in a real export: two owner contributions).
     *
     * Posted in Miscellaneous Operations through the one entry builder, as
     * expenses are; the bank or cash account is debited or credited directly.
     *
     * @param  array<string, mixed>  $transaction
     */
    protected function importBankTransaction(array $transaction): Move
    {
        $from = $this->accountIds[(string) $transaction['zoho_from_account_id']] ?? null;
        $to = $this->accountIds[(string) $transaction['zoho_to_account_id']] ?? null;

        if (! $from || ! $to) {
            throw new ZohoImportException('the accounts it moves money between are not both mapped.');
        }

        $journal = Journal::withoutGlobalScopes()
            ->where('company_id', $this->company->id)
            ->where('type', JournalType::GENERAL)
            ->orderBy('id')
            ->first();

        if (! $journal) {
            throw new ZohoImportException('the company has no Miscellaneous Operations journal.');
        }

        $contactId = $transaction['customer_id'] ?? ($transaction['vendor_id'] ?? null);
        $label = trim(str_replace('_', ' ', (string) $transaction['transaction_type']).(filled($transaction['description'] ?? null) ? ' - '.$transaction['description'] : ''));

        $move = app(ExpenseEntry::class)->post(
            companyId: (int) $this->company->id,
            currencyId: (int) $this->company->currency_id,
            journalId: (int) $journal->id,
            date: (string) $transaction['date'],
            lines: [['account_id' => (int) $to, 'amount' => (float) $transaction['amount'], 'name' => $label]],
            paidFromAccountId: (int) $from,
            paidFromLabel: $label,
            partnerId: filled($contactId) ? $this->partnerId((string) $contactId) : null,
            reference: trim('Zoho '.str_replace('_', ' ', (string) $transaction['transaction_type']).(filled($transaction['reference_number'] ?? null) ? ' '.$transaction['reference_number'] : '')),
            creatorId: $this->creatorId,
        );

        return $move;
    }

    // ------------------------------------------------------------------
    // Numbering
    // ------------------------------------------------------------------

    /**
     * Imported documents keep their Zoho numbers and must never move DEKA's
     * own series (user decision, 2026-11-25). A DEKA counter created later
     * starts after the highest number already in its journal
     * (Journal::sequenceDefaults() "initial_from"), so one created after the
     * import would jump to INV-2026-2250884. Every counter the company's
     * journals and quotations use is therefore made now, before the first
     * Zoho number exists. Counters already there are left exactly as they
     * are.
     */
    protected function protectNumbering(): void
    {
        Journal::withoutGlobalScopes()
            ->where('company_id', $this->company->id)
            ->get()
            ->each(fn (Journal $journal) => $journal->ensureSequences());

        if (! $this->quotationsEnabled()) {
            return;
        }

        $exists = DB::table('sequences')
            ->where('code', 'sales.order')
            ->where(fn ($query) => $query->where('company_id', $this->company->id)->orWhereNull('company_id'))
            ->exists();

        if (! $exists) {
            // The defaults Order::updateName() would use, read now.
            SequenceService::ensure('sales.order', (int) $this->company->id, [
                'name'         => 'Sales Order',
                'prefix'       => 'SO-',
                'initial_from' => Order::withoutGlobalScopes(),
            ]);
        }
    }

    /**
     * Only when the mapping's numbering option says "continue Zoho's
     * counter" (the default is DEKA's own series, user decision 2026-11-25):
     * the counters take Zoho's prefix, width and next number. Only ever
     * moves a counter forward, so a re-run changes nothing.
     */
    protected function continueNumbering(): void
    {
        $sequences = $this->mapping['sequences'] ?? [];

        if (($sequences['invoices']['action'] ?? 'keep') === 'apply') {
            $journal = Journal::withoutGlobalScopes()
                ->where('company_id', $this->company->id)
                ->where('type', JournalType::SALE)
                ->orderBy('id')
                ->first();

            if ($journal) {
                $this->advance(
                    SequenceService::ensureFor($journal, '', (int) $this->company->id, $journal->sequenceDefaults('')),
                    $sequences['invoices'],
                );
            }
        }

        if (($sequences['quotations']['action'] ?? 'keep') === 'apply' && $this->quotationsEnabled()) {
            $this->advance(
                SequenceService::ensure('sales.order', (int) $this->company->id, ['name' => 'Sales Order', 'prefix' => 'SO-']),
                $sequences['quotations'],
            );
        }
    }

    /**
     * @param  array<string, mixed>  $series
     */
    protected function advance(Model $sequence, array $series): void
    {
        $next = max((int) $sequence->next_number, (int) $series['next']);

        $sequence->forceFill([
            'prefix'          => (string) $series['prefix'],
            'suffix'          => null,
            'padding'         => (int) $series['padding'],
            'next_number'     => $next,
            'reset_frequency' => 'never',
            'period_key'      => null,
        ])->save();

        $this->count('numbering continued');
    }

    // ------------------------------------------------------------------
    // Bookkeeping
    // ------------------------------------------------------------------

    protected function quotationsEnabled(): bool
    {
        return SalesAccess::enabledForCompany((int) $this->company->id);
    }

    /**
     * Read once per run and kept in memory: the lookup is made for every
     * record, and a re-run checks hundreds of them. A failed document stops
     * the run, so a remembered row whose transaction rolled back is never
     * read again.
     */
    protected function recorded(string $type, string $zohoId): ?int
    {
        $this->records ??= ZohoImportRecord::query()
            ->where('company_id', $this->company->id)
            ->get(['zoho_type', 'zoho_id', 'record_id'])
            ->mapWithKeys(fn (ZohoImportRecord $row) => [$row->zoho_type.'|'.$row->zoho_id => (int) $row->record_id])
            ->all();

        return $this->records[$type.'|'.$zohoId] ?? null;
    }

    protected function remember(string $type, string $zohoId, Model $record): void
    {
        ZohoImportRecord::query()->create([
            'company_id'  => $this->company->id,
            'zoho_type'   => $type,
            'zoho_id'     => $zohoId,
            'record_type' => $record->getMorphClass(),
            'record_id'   => $record->getKey(),
        ]);

        if ($this->records !== null) {
            $this->records[$type.'|'.$zohoId] = (int) $record->getKey();
        }
    }

    protected function count(string $what): void
    {
        $this->counts[$what] = ($this->counts[$what] ?? 0) + 1;
    }

    protected function say(string $message): void
    {
        if ($this->output) {
            ($this->output)($message);
        }
    }
}
