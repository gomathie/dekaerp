<?php

namespace Webkul\Sale\Services\ZohoImport;

/**
 * A Zoho Books export: the API v3 detail objects of one organization, as the
 * export script writes them (one JSON file, one array per module).
 *
 * Read-only. Documents come back oldest first (date, then Zoho's creation
 * time), which is the order the importer records them in: Zoho has no
 * opening balances, so replaying every document from the first one is what
 * reproduces its balances.
 */
class ZohoExport
{
    public const SECTIONS = [
        'organization',
        'chartofaccounts',
        'taxes',
        'bankaccounts',
        'items',
        'contacts',
        'estimates',
        'invoices',
        'customerpayments',
        'expenses',
    ];

    /**
     * @param  array<string, mixed>  $data
     */
    public function __construct(protected array $data) {}

    public static function fromFile(string $path): self
    {
        if (! is_file($path) || ! is_readable($path)) {
            throw new ZohoImportException('The export file cannot be read.');
        }

        return static::fromJson((string) file_get_contents($path));
    }

    public static function fromJson(string $json): self
    {
        $data = json_decode($json, true);

        if (! is_array($data)) {
            throw new ZohoImportException('The export file is not valid JSON.');
        }

        foreach (self::SECTIONS as $section) {
            if (! array_key_exists($section, $data) || ! is_array($data[$section])) {
                throw new ZohoImportException("The export file has no \"{$section}\" section.");
            }
        }

        return new self($data);
    }

    /**
     * The same export with only the documents dated before the cut-off's
     * day. Used for a top-up after the initial load: records dated from
     * the cut-off on are entered in both systems and must not be imported.
     */
    public function before(\DateTimeInterface $cutoff): self
    {
        $day = $cutoff->format('Y-m-d');
        $data = $this->data;

        foreach (['estimates', 'invoices', 'customerpayments', 'expenses', 'banktransactions', 'salesreceipts', 'journals'] as $section) {
            if (! is_array($data[$section] ?? null)) {
                continue;
            }

            $data[$section] = array_values(array_filter(
                $data[$section],
                fn (array $row): bool => (string) ($row['date'] ?? $row['journal_date'] ?? '') < $day,
            ));
        }

        return new self($data);
    }

    public function organizationId(): string
    {
        return (string) ($this->data['organization']['organization_id'] ?? '');
    }

    public function currency(): string
    {
        return (string) ($this->data['organization']['currency_code'] ?? '');
    }

    public function exportedAt(): ?string
    {
        return $this->data['exported_at'] ?? null;
    }

    /**
     * @return array<string, array<string, mixed>> by account_id
     */
    public function accounts(): array
    {
        return $this->keyed('chartofaccounts', 'account_id');
    }

    /**
     * @return array<string, array<string, mixed>> by tax_id
     */
    public function taxes(): array
    {
        return $this->keyed('taxes', 'tax_id');
    }

    /**
     * @return array<string, array<string, mixed>> by account_id
     */
    public function bankAccounts(): array
    {
        return $this->keyed('bankaccounts', 'account_id');
    }

    /**
     * @return array<string, array<string, mixed>> by item_id
     */
    public function items(): array
    {
        return $this->keyed('items', 'item_id');
    }

    /**
     * @return array<string, array<string, mixed>> by contact_id
     */
    public function contacts(): array
    {
        return $this->keyed('contacts', 'contact_id');
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function estimates(): array
    {
        return $this->chronological('estimates');
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function invoices(): array
    {
        if ($this->invoices !== null) {
            return $this->invoices;
        }

        $rows = [...array_values($this->data['invoices']), ...array_map(fn (array $receipt): array => static::receiptInvoice($receipt), $this->salesReceipts())];

        usort($rows, fn (array $a, array $b): int => [(string) ($a['date'] ?? ''), (string) ($a['created_time'] ?? '')]
            <=> [(string) ($b['date'] ?? ''), (string) ($b['created_time'] ?? '')]);

        return $this->invoices = $rows;
    }

    /** @var list<array<string, mixed>>|null */
    protected ?array $invoices = null;

    /**
     * Zoho sales receipts (a sale paid on the spot). Optional section.
     *
     * DEKA has no separate receipt document: a receipt is recorded as what it
     * is in the books, a customer invoice under the receipt's number, paid at
     * once into the account the money went to. So invoices() and payments()
     * both include them, and every total, balance and comparison sees them.
     *
     * @return list<array<string, mixed>>
     */
    public function salesReceipts(): array
    {
        return is_array($this->data['salesreceipts'] ?? null) ? array_values($this->data['salesreceipts']) : [];
    }

    public static function receiptKey(array $receipt): string
    {
        return 'receipt:'.($receipt['sales_receipt_id'] ?? $receipt['receipt_id'] ?? '');
    }

    /**
     * @param  array<string, mixed>  $receipt
     * @return array<string, mixed>
     */
    public static function receiptInvoice(array $receipt): array
    {
        return [
            'invoice_id'       => static::receiptKey($receipt),
            'invoice_number'   => (string) ($receipt['receipt_number'] ?? $receipt['sales_receipt_number'] ?? ''),
            'status'           => 'paid',
            'due_date'         => $receipt['date'],
            'balance'          => 0,
            'is_inclusive_tax' => (bool) ($receipt['is_inclusive_tax'] ?? false),
            'tax_rounding'     => $receipt['tax_rounding'] ?? 'entity_level',
        ] + $receipt;
    }

    /**
     * @param  array<string, mixed>  $receipt
     * @return array<string, mixed>
     */
    public static function receiptPayment(array $receipt): array
    {
        $invoice = static::receiptInvoice($receipt);

        return [
            'payment_id'          => $invoice['invoice_id'],
            'payment_number'      => $invoice['invoice_number'],
            'date'                => $receipt['date'],
            'amount'              => (float) $receipt['total'],
            'unused_amount'       => 0,
            'bank_charges'        => 0,
            'payment_mode'        => (string) ($receipt['payment_mode'] ?? ''),
            'account_id'          => (string) ($receipt['deposit_to_account_id'] ?? $receipt['account_id'] ?? ''),
            'customer_id'         => $receipt['customer_id'] ?? null,
            'currency_code'       => $receipt['currency_code'] ?? null,
            'created_time'        => $receipt['created_time'] ?? null,
            'reference_number'    => $receipt['reference_number'] ?? '',
            'tax_amount_withheld' => 0,
            'invoices'            => [[
                'invoice_id'          => $invoice['invoice_id'],
                'invoice_number'      => $invoice['invoice_number'],
                'amount_applied'      => (float) $receipt['total'],
                'tax_amount_withheld' => 0,
            ]],
        ];
    }

    /**
     * Zoho's own balance of an account, when the export carries it
     * ("account_balances": account id => balance, read from the chart of
     * accounts). The bank accounts list's "balance" can include bank-feed
     * items not yet categorised, so it is used only when nothing better is
     * there.
     */
    public function accountBalance(string $accountId): ?float
    {
        $balances = $this->data['account_balances'] ?? null;

        return is_array($balances) && array_key_exists($accountId, $balances) ? (float) $balances[$accountId] : null;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function payments(): array
    {
        $rows = [...array_values($this->data['customerpayments']), ...array_map(fn (array $receipt): array => static::receiptPayment($receipt), $this->salesReceipts())];

        usort($rows, fn (array $a, array $b): int => [(string) ($a['date'] ?? ''), (string) ($a['created_time'] ?? '')]
            <=> [(string) ($b['date'] ?? ''), (string) ($b['created_time'] ?? '')]);

        return $rows;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function expenses(): array
    {
        return $this->chronological('expenses');
    }

    /**
     * Zoho bank transaction types that are the bank side of a document the
     * import already records (a customer payment, an expense...). Recording
     * them again would count the same money twice.
     */
    public const DOCUMENT_TRANSACTION_TYPES = [
        'customer_payment',
        'vendor_payment',
        'expense',
        'expense_refund',
        'invoice',
        'bill',
        'creditnote_refund',
        'vendorcredit_refund',
        'salesorder_payment',
        'retainer_payment',
        'payment_refund',
        'sales_receipt',
    ];

    /**
     * Bank transactions that are money movements of their own (deposits,
     * owner contributions and drawings, transfers between accounts, other
     * income...), oldest first. Optional: an export without the section has
     * none. Transactions that are the bank side of an exported payment or
     * expense are left out, by type and by id.
     *
     * Each comes back with the Zoho account the money came from ("from") and
     * went to ("to"), read from the fields Zoho fills for its type.
     *
     * @return list<array<string, mixed>>
     */
    public function bankTransactions(): array
    {
        if (! is_array($this->data['banktransactions'] ?? null)) {
            return [];
        }

        $documentIds = array_flip(array_map('strval', [
            ...array_column($this->payments(), 'payment_id'),
            ...array_column($this->expenses(), 'expense_id'),
            ...array_map(fn (array $receipt): string => (string) ($receipt['sales_receipt_id'] ?? $receipt['receipt_id'] ?? ''), $this->salesReceipts()),
        ]));

        $rows = [];

        foreach ($this->chronological('banktransactions') as $transaction) {
            $type = (string) ($transaction['transaction_type'] ?? '');

            if (in_array($type, self::DOCUMENT_TRANSACTION_TYPES, true) || isset($documentIds[(string) ($transaction['transaction_id'] ?? '')])) {
                continue;
            }

            [$from, $to] = static::transactionAccounts($transaction);

            $rows[] = $transaction + ['zoho_from_account_id' => $from, 'zoho_to_account_id' => $to];
        }

        return $rows;
    }

    /**
     * Which account a bank transaction took the money from and gave it to.
     * Zoho's detail object names both (from_account_id, to_account_id); a
     * list row names the bank account, its counterpart and the direction.
     *
     * @param  array<string, mixed>  $transaction
     * @return array{0: ?string, 1: ?string}
     */
    public static function transactionAccounts(array $transaction): array
    {
        $from = (string) ($transaction['from_account_id'] ?? '');
        $to = (string) ($transaction['to_account_id'] ?? '');

        if ($from !== '' && $to !== '') {
            return [$from, $to];
        }

        $bank = (string) ($transaction['account_id'] ?? '');
        $other = (string) ($transaction['offset_account_id'] ?? '');

        if ($bank === '' || $other === '') {
            return [null, null];
        }

        // "debit": money into the bank account.
        return ($transaction['debit_or_credit'] ?? '') === 'debit' ? [$other, $bank] : [$bank, $other];
    }

    /**
     * Manual journals, oldest first. Optional, like bank transactions.
     *
     * @return list<array<string, mixed>>
     */
    public function journals(): array
    {
        if (! is_array($this->data['journals'] ?? null)) {
            return [];
        }

        $rows = array_map(fn (array $journal): array => $journal + ['date' => $journal['journal_date'] ?? ($journal['date'] ?? null)], array_values($this->data['journals']));

        usort($rows, fn (array $a, array $b): int => [(string) $a['date'], (string) ($a['created_time'] ?? '')]
            <=> [(string) $b['date'], (string) ($b['created_time'] ?? '')]);

        return $rows;
    }

    /**
     * Every tax a document line carries, once per Zoho tax and per price mode:
     * the same Zoho tax on a tax-inclusive and on a tax-exclusive document
     * needs two DEKA taxes, because DEKA keeps "price includes tax" on the tax.
     *
     * @return array<string, array{key: string, zoho_tax_id: string, zoho_name: string, rate: float, inclusive: bool, components: list<array{zoho_tax_id: string, name: string, rate: float}>, used: int}>
     */
    public function lineTaxes(): array
    {
        $known = $this->taxes();
        $found = [];

        foreach ([...$this->invoices(), ...$this->estimates()] as $document) {
            $inclusive = (bool) ($document['is_inclusive_tax'] ?? false);

            foreach ($document['line_items'] ?? [] as $line) {
                $taxId = (string) ($line['tax_id'] ?? '');

                if ($taxId === '') {
                    continue;
                }

                $key = static::taxKey($taxId, $inclusive);

                if (isset($found[$key])) {
                    $found[$key]['used']++;

                    continue;
                }

                $components = [];

                foreach ($line['line_item_taxes'] ?? [] as $component) {
                    $componentId = (string) $component['tax_id'];

                    $components[] = [
                        'zoho_tax_id' => $componentId,
                        'name'        => (string) ($known[$componentId]['tax_name'] ?? preg_replace('/\s*\([\d.]+%\)$/', '', (string) $component['tax_name'])),
                        // A sales receipt's line taxes carry no percentage:
                        // the tax itself, then the "(15%)" in its name, say it.
                        'rate'        => (float) ($component['tax_percentage'] ?? $known[$componentId]['tax_percentage'] ?? (preg_match('/\(([\d.]+)%\)/', (string) $component['tax_name'], $rate) ? $rate[1] : 0)),
                    ];
                }

                if ($components === []) {
                    $components[] = [
                        'zoho_tax_id' => $taxId,
                        'name'        => (string) $line['tax_name'],
                        'rate'        => (float) $line['tax_percentage'],
                    ];
                }

                $found[$key] = [
                    'key'         => $key,
                    'zoho_tax_id' => $taxId,
                    'zoho_name'   => (string) ($line['tax_name'] ?? ($known[$taxId]['tax_name'] ?? $taxId)),
                    'rate'        => (float) ($line['tax_percentage'] ?? 0),
                    'inclusive'   => $inclusive,
                    'components'  => $components,
                    'used'        => 1,
                ];
            }
        }

        ksort($found);

        return $found;
    }

    public static function taxKey(string $zohoTaxId, bool $inclusive): string
    {
        return $zohoTaxId.($inclusive ? ':inclusive' : ':exclusive');
    }

    /**
     * How many document lines (invoice and estimate lines, expense lines,
     * payment deposits and withheld tax) use each Zoho account.
     *
     * @return array<string, int>
     */
    public function accountUsage(): array
    {
        $usage = [];
        $count = function (?string $accountId) use (&$usage): void {
            if ($accountId === null || $accountId === '') {
                return;
            }

            $usage[$accountId] = ($usage[$accountId] ?? 0) + 1;
        };

        foreach ([...$this->invoices(), ...$this->estimates()] as $document) {
            foreach ($document['line_items'] ?? [] as $line) {
                $count($line['account_id'] ?? null);
            }
        }

        foreach ($this->invoices() as $invoice) {
            if (($invoice['status'] ?? '') !== 'draft' && ($invoice['status'] ?? '') !== 'void') {
                $count($this->receivableAccountId());
            }
        }

        foreach ($this->payments() as $payment) {
            $count($payment['account_id'] ?? null);

            if ((float) ($payment['tax_amount_withheld'] ?? 0) !== 0.0) {
                $count($payment['tax_account_id'] ?? null);
            }
        }

        foreach ($this->expenses() as $expense) {
            foreach ($expense['line_items'] ?? [] as $line) {
                $count($line['account_id'] ?? null);
            }

            $count($expense['paid_through_account_id'] ?? null);
        }

        foreach ($this->bankTransactions() as $transaction) {
            $count($transaction['zoho_from_account_id']);
            $count($transaction['zoho_to_account_id']);
        }

        foreach ($this->journals() as $journal) {
            foreach ($journal['line_items'] ?? [] as $line) {
                $count($line['account_id'] ?? null);
            }
        }

        foreach ($this->lineTaxes() as $tax) {
            foreach ($tax['components'] as $component) {
                $account = $this->taxes()[$component['zoho_tax_id']]['tax_account_id'] ?? null;

                $count($account);
            }
        }

        return $usage;
    }

    /**
     * Zoho's own "Accounts Receivable" account, which invoices post to.
     */
    public function receivableAccountId(): ?string
    {
        foreach ($this->accounts() as $id => $account) {
            if (($account['account_type'] ?? '') === 'accounts_receivable') {
                return (string) $id;
            }
        }

        return null;
    }

    /**
     * @return array<string, int>
     */
    public function counts(): array
    {
        return [
            'accounts'   => count($this->data['chartofaccounts']),
            'taxes'      => count($this->data['taxes']),
            'banks'      => count($this->data['bankaccounts']),
            'items'      => count($this->data['items']),
            'contacts'   => count($this->data['contacts']),
            'estimates'  => count($this->data['estimates']),
            'invoices'   => count($this->data['invoices']),
            'payments'   => count($this->data['customerpayments']),
            'expenses'   => count($this->data['expenses']),
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    protected function keyed(string $section, string $key): array
    {
        $rows = [];

        foreach ($this->data[$section] as $row) {
            $rows[(string) $row[$key]] = $row;
        }

        return $rows;
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function chronological(string $section): array
    {
        $rows = array_values($this->data[$section]);

        usort($rows, fn (array $a, array $b): int => [(string) ($a['date'] ?? ''), (string) ($a['created_time'] ?? '')]
            <=> [(string) ($b['date'] ?? ''), (string) ($b['created_time'] ?? '')]);

        return $rows;
    }
}
