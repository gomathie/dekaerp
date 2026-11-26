<?php

namespace Webkul\Sale\Services\ZohoImport;

use Illuminate\Support\Facades\DB;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Support\Models\Company;

/**
 * Zoho Books against DEKA, side by side, after an import (zoho:compare and
 * the Super Admin import page): bank and cash balances, what each customer
 * still owes, invoices by status, sales and tax by month, expenses by
 * account and month, and a trial balance per account.
 *
 * Zoho's side comes from the export: the balances Zoho reports for its bank
 * accounts and customers, and, for the trial balance, every document
 * replayed (Zoho has no opening balances). DEKA's side is read from the
 * posted ledger of the company, filtered by company explicitly: this runs in
 * the console, where the company scope does not apply.
 *
 * Each row is OK or DIFF with the difference. Read-only.
 */
class ZohoComparison
{
    protected const TOLERANCE = 0.005;

    protected Company $company;

    protected ZohoExport $export;

    /** @var array<string, string> zoho account id => DEKA code */
    protected array $codes = [];

    /** @var array<string, int> DEKA code => account id */
    protected array $accountIds = [];

    /**
     * @param  array<string, mixed>  $mapping
     * @return list<array{section: string, label: string, zoho: float, deka: float, difference: float, status: string}>
     */
    public function compare(Company $company, ZohoExport $export, array $mapping, bool $anonymous = false): array
    {
        $this->company = $company;
        $this->export = $export;
        $this->resolveAccounts($mapping);

        return [
            ...$this->bankBalances($mapping),
            ...$this->customerBalances($mapping, $anonymous),
            ...$this->invoicesByStatus(),
            ...$this->salesByMonth(),
            ...$this->expensesByMonth(),
            ...$this->trialBalance(),
        ];
    }

    /**
     * Documents that are in one system and not the other, for the parallel
     * run (user, 2026-11-26): the client enters every new record in both
     * Zoho and DEKA, and this says, in plain words, which ones are missing
     * where.
     *
     * A Zoho document is matched first through the import (its Zoho id),
     * then to a DEKA document of the same kind with the same date, the same
     * customer or vendor and the same amount. Each DEKA document matches
     * once. Read-only.
     *
     * @param  array<string, mixed>  $mapping
     * @return list<array{side: string, kind: string, number: string, date: string, partner: string, amount: float, text: string}>
     */
    public function unmatched(Company $company, ZohoExport $export, array $mapping, bool $anonymous = false): array
    {
        $this->company = $company;
        $this->export = $export;
        $this->resolveAccounts($mapping);

        $partners = $this->partnerMap($mapping);
        $names = $anonymous ? [] : collect($export->contacts())->map(fn (array $contact) => (string) $contact['contact_name'])->all();

        $zoho = [];

        foreach ($export->invoices() as $invoice) {
            if (in_array($invoice['status'], ['draft', 'void'], true)) {
                continue;
            }

            $zoho[] = ['kind' => 'invoice', 'zoho_type' => 'invoice', 'zoho_id' => (string) $invoice['invoice_id'], 'number' => (string) $invoice['invoice_number'], 'date' => (string) $invoice['date'], 'partner_id' => $partners[(string) $invoice['customer_id']] ?? null, 'partner' => $names[(string) $invoice['customer_id']] ?? '', 'amount' => (float) $invoice['total']];
        }

        foreach ($export->payments() as $payment) {
            foreach ($payment['invoices'] as $application) {
                $zoho[] = ['kind' => 'payment', 'zoho_type' => 'payment', 'zoho_id' => $payment['payment_id'].':'.$application['invoice_id'], 'number' => 'payment '.$payment['payment_number'], 'date' => (string) $payment['date'], 'partner_id' => $partners[(string) ($payment['customer_id'] ?? '')] ?? null, 'partner' => $names[(string) ($payment['customer_id'] ?? '')] ?? '', 'amount' => (float) $application['amount_applied']];
            }
        }

        foreach ($export->expenses() as $expense) {
            $zoho[] = ['kind' => 'expense', 'zoho_type' => 'expense', 'zoho_id' => (string) $expense['expense_id'], 'number' => (string) ($expense['account_name'] ?? 'expense'), 'date' => (string) $expense['date'], 'partner_id' => $partners[(string) ($expense['vendor_id'] ?? '')] ?? null, 'partner' => $names[(string) ($expense['vendor_id'] ?? '')] ?? '', 'amount' => (float) $expense['total']];
        }

        $records = DB::table('sales_zoho_import_records')
            ->where('company_id', $company->id)
            ->get(['zoho_type', 'zoho_id', 'record_id'])
            ->mapWithKeys(fn ($row) => [$row->zoho_type.'|'.$row->zoho_id => (int) $row->record_id])
            ->all();

        $deka = $this->dekaDocuments();
        $taken = [];
        $rows = [];

        foreach ($zoho as $document) {
            $recordId = $records[$document['zoho_type'].'|'.$document['zoho_id']] ?? null;
            $key = $recordId ? $this->dekaKey($document['kind'], $recordId) : null;

            if ($key && isset($deka[$key]) && ! isset($taken[$key])) {
                $taken[$key] = true;

                continue;
            }

            $match = collect($deka)->first(fn (array $candidate, string $candidateKey) => ! isset($taken[$candidateKey])
                && $candidate['kind'] === $document['kind']
                && $candidate['date'] === $document['date']
                && abs($candidate['amount'] - $document['amount']) <= self::TOLERANCE
                && ($document['partner_id'] === null || $candidate['partner_id'] === null || (int) $candidate['partner_id'] === (int) $document['partner_id']));

            if ($match) {
                $taken[$match['key']] = true;

                continue;
            }

            $rows[] = $this->unmatchedRow('zoho', $document);
        }

        foreach ($deka as $key => $document) {
            if (! isset($taken[$key])) {
                $rows[] = $this->unmatchedRow('deka', $document + ['partner' => $anonymous ? '' : (string) $document['partner_name']]);
            }
        }

        usort($rows, fn (array $a, array $b) => [$a['date'], $a['side']] <=> [$b['date'], $b['side']]);

        return $rows;
    }

    protected function dekaKey(string $kind, int $id): string
    {
        return $kind.':'.$id;
    }

    /**
     * DEKA's side: posted customer invoices, payments received, and expense
     * entries (an entry that pays out of a bank or cash account, or a
     * vendor bill), whoever made them.
     *
     * @return array<string, array<string, mixed>>
     */
    protected function dekaDocuments(): array
    {
        $documents = [];
        $partnerName = fn ($id) => $id ? (string) DB::table('partners_partners')->where('id', $id)->value('name') : '';

        $invoices = DB::table('accounts_account_moves')
            ->where('company_id', $this->company->id)
            ->where('move_type', MoveType::OUT_INVOICE->value)
            ->where('state', MoveState::POSTED->value)
            ->get(['id', 'name', 'invoice_date', 'partner_id', 'amount_total']);

        foreach ($invoices as $move) {
            $key = $this->dekaKey('invoice', (int) $move->id);
            $documents[$key] = ['key' => $key, 'kind' => 'invoice', 'number' => (string) $move->name, 'date' => substr((string) $move->invoice_date, 0, 10), 'partner_id' => $move->partner_id, 'partner_name' => $partnerName($move->partner_id), 'amount' => (float) $move->amount_total];
        }

        $payments = DB::table('accounts_account_payments')
            ->where('company_id', $this->company->id)
            ->where('payment_type', 'inbound')
            ->whereNotNull('move_id')
            ->whereIn('move_id', DB::table('accounts_account_moves')->where('state', MoveState::POSTED->value)->select('id'))
            ->get(['id', 'name', 'date', 'partner_id', 'amount']);

        foreach ($payments as $payment) {
            $key = $this->dekaKey('payment', (int) $payment->id);
            $documents[$key] = ['key' => $key, 'kind' => 'payment', 'number' => 'payment '.$payment->name, 'date' => substr((string) $payment->date, 0, 10), 'partner_id' => $payment->partner_id, 'partner_name' => $partnerName($payment->partner_id), 'amount' => (float) $payment->amount];
        }

        $cashAccounts = DB::table('accounts_accounts')
            ->join('accounts_account_companies', 'accounts_account_companies.account_id', '=', 'accounts_accounts.id')
            ->where('accounts_account_companies.company_id', $this->company->id)
            ->where('accounts_accounts.account_type', AccountType::ASSET_CASH->value)
            ->pluck('accounts_accounts.id');

        $bankTransactionMoves = DB::table('sales_zoho_import_records')
            ->where('company_id', $this->company->id)
            ->where('zoho_type', 'bank_transaction')
            ->pluck('record_id');

        $expenses = DB::table('accounts_account_moves as move')
            ->where('move.company_id', $this->company->id)
            ->where('move.state', MoveState::POSTED->value)
            ->where(fn ($query) => $query
                ->where(fn ($entry) => $entry->where('move.move_type', MoveType::ENTRY->value)
                    ->whereNull('move.origin_payment_id')
                    ->whereNotIn('move.id', $bankTransactionMoves)
                    ->whereExists(fn ($line) => $line->select(DB::raw(1))
                        ->from('accounts_account_move_lines as line')
                        ->whereColumn('line.move_id', 'move.id')
                        ->whereIn('line.account_id', $cashAccounts)
                        ->where('line.credit', '>', 0)))
                ->orWhere('move.move_type', MoveType::IN_INVOICE->value))
            ->get(['move.id', 'move.name', 'move.date', 'move.partner_id', 'move.amount_total', 'move.move_type']);

        foreach ($expenses as $move) {
            $amount = $move->move_type === MoveType::IN_INVOICE->value
                ? (float) $move->amount_total
                : (float) DB::table('accounts_account_move_lines')->where('move_id', $move->id)->whereIn('account_id', $cashAccounts)->sum('credit');

            $key = $this->dekaKey('expense', (int) $move->id);
            $documents[$key] = ['key' => $key, 'kind' => 'expense', 'number' => (string) $move->name, 'date' => substr((string) $move->date, 0, 10), 'partner_id' => $move->partner_id, 'partner_name' => $partnerName($move->partner_id), 'amount' => $amount];
        }

        return $documents;
    }

    /**
     * @param  array<string, mixed>  $mapping
     * @return array<string, int> zoho contact id => DEKA partner id
     */
    protected function partnerMap(array $mapping): array
    {
        $partners = DB::table('sales_zoho_import_records')
            ->where('company_id', $this->company->id)
            ->where('zoho_type', 'contact')
            ->pluck('record_id', 'zoho_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        foreach ($mapping['contacts'] ?? [] as $zohoId => $entry) {
            if (($entry['action'] ?? null) === 'map' && filled($entry['deka_partner_id'] ?? null)) {
                $partners[(string) $zohoId] = (int) $entry['deka_partner_id'];
            }
        }

        return $partners;
    }

    /**
     * @param  array<string, mixed>  $document
     * @return array{side: string, kind: string, number: string, date: string, partner: string, amount: float, text: string}
     */
    protected function unmatchedRow(string $side, array $document): array
    {
        $where = $side === 'zoho' ? 'In Zoho, not in DEKA' : 'In DEKA, not in Zoho';
        $partner = trim((string) ($document['partner'] ?? ''));

        return [
            'side'    => $side,
            'kind'    => $document['kind'],
            'number'  => $document['number'],
            'date'    => $document['date'],
            'partner' => $partner,
            'amount'  => round((float) $document['amount'], 2),
            'text'    => sprintf('%s: %s %s, %s on %s%s', $where, $document['kind'], $document['number'], number_format((float) $document['amount'], 2), $document['date'], $partner !== '' ? ' ('.$partner.')' : ''),
        ];
    }

    /**
     * @param  array<string, mixed>  $mapping
     */
    protected function resolveAccounts(array $mapping): void
    {
        $this->accountIds = DB::table('accounts_accounts')
            ->join('accounts_account_companies', 'accounts_account_companies.account_id', '=', 'accounts_accounts.id')
            ->where('accounts_account_companies.company_id', $this->company->id)
            ->pluck('accounts_accounts.id', 'accounts_accounts.code')
            ->map(fn ($id) => (int) $id)
            ->all();

        foreach ($mapping['accounts'] ?? [] as $zohoId => $entry) {
            if (filled($entry['code'] ?? null)) {
                $this->codes[(string) $zohoId] = (string) $entry['code'];
            }
        }

        foreach ($mapping['bank_accounts'] ?? [] as $zohoId => $entry) {
            $code = $this->journalAccountCode($entry);

            if ($code) {
                $this->codes[(string) $zohoId] = $code;
            }
        }
    }

    /**
     * @param  array<string, mixed>  $entry
     */
    protected function journalAccountCode(array $entry): ?string
    {
        $query = DB::table('accounts_journals')
            ->join('accounts_accounts', 'accounts_accounts.id', '=', 'accounts_journals.default_account_id')
            ->where('accounts_journals.company_id', $this->company->id)
            ->whereIn('accounts_journals.type', [JournalType::BANK->value, JournalType::CASH->value]);

        $query = filled($entry['journal_code'] ?? null)
            ? $query->where('accounts_journals.code', $entry['journal_code'])
            : $query->where('accounts_journals.name', $entry['journal_name']);

        $code = $query->value('accounts_accounts.code');

        return $code === null ? null : (string) $code;
    }

    /**
     * Posted ledger lines of the company.
     */
    protected function ledger()
    {
        return DB::table('accounts_account_move_lines as line')
            ->join('accounts_account_moves as move', 'move.id', '=', 'line.move_id')
            ->where('move.company_id', $this->company->id)
            ->where('move.state', MoveState::POSTED->value);
    }

    protected function row(string $section, string $label, float $zoho, float $deka): array
    {
        $difference = round($deka - $zoho, 2);

        return [
            'section'    => $section,
            'label'      => $label,
            'zoho'       => round($zoho, 2),
            'deka'       => round($deka, 2),
            'difference' => $difference,
            'status'     => abs($difference) <= self::TOLERANCE ? 'OK' : 'DIFF',
        ];
    }

    /**
     * @param  array<string, mixed>  $mapping
     */
    protected function bankBalances(array $mapping): array
    {
        $rows = [];

        foreach ($this->export->bankAccounts() as $zohoId => $bank) {
            $accountId = $this->accountIds[$this->codes[(string) $zohoId] ?? ''] ?? null;

            $deka = $accountId
                ? (float) $this->ledger()->where('line.account_id', $accountId)->sum('line.balance')
                : 0.0;

            $label = ($mapping['bank_accounts'][$zohoId]['journal_name'] ?? $bank['account_name']).' ('.($this->codes[(string) $zohoId] ?? 'unmapped').')';

            // Zoho's ledger balance when the export has it: the bank list's
            // "balance" also counts bank-feed items nobody categorised
            // (HiTrace: 33,355.31 against a ledger of 17,345.31).
            $rows[] = $this->row('Bank and cash balance', $label, $this->export->accountBalance((string) $zohoId) ?? (float) $bank['balance'], $deka);
        }

        return $rows;
    }

    /**
     * @param  array<string, mixed>  $mapping
     */
    protected function customerBalances(array $mapping, bool $anonymous): array
    {
        $partnerIds = DB::table('sales_zoho_import_records')
            ->where('company_id', $this->company->id)
            ->where('zoho_type', 'contact')
            ->pluck('record_id', 'zoho_id')
            ->all();

        foreach ($mapping['contacts'] ?? [] as $zohoId => $entry) {
            if (($entry['action'] ?? null) === 'map') {
                $partnerIds[$zohoId] = $entry['deka_partner_id'];
            }
        }

        $owed = $this->ledger()
            ->join('accounts_accounts as account', 'account.id', '=', 'line.account_id')
            ->where('account.account_type', AccountType::ASSET_RECEIVABLE->value)
            ->groupBy('line.partner_id')
            ->selectRaw('line.partner_id as partner_id, SUM(line.amount_residual) as owed')
            ->pluck('owed', 'partner_id')
            ->all();

        $rows = [];
        $number = 0;
        $zohoTotal = 0.0;
        $dekaTotal = 0.0;

        foreach ($this->export->contacts() as $zohoId => $contact) {
            if (($contact['contact_type'] ?? '') !== 'customer') {
                continue;
            }

            $number++;
            $zoho = (float) ($contact['outstanding_receivable_amount'] ?? 0);
            $deka = (float) ($owed[$partnerIds[$zohoId] ?? 0] ?? 0);
            $zohoTotal += $zoho;
            $dekaTotal += $deka;

            if (abs($zoho) <= self::TOLERANCE && abs($deka) <= self::TOLERANCE) {
                continue;
            }

            $rows[] = $this->row('Customer balance', $anonymous ? 'Customer #'.$number : (string) $contact['contact_name'], $zoho, $deka);
        }

        // Customers created in DEKA during the parallel run have no Zoho
        // contact; what they owe still belongs in the total.
        $linked = array_flip(array_map('intval', array_filter($partnerIds)));

        foreach ($owed as $partnerId => $amount) {
            if (isset($linked[(int) $partnerId]) || abs((float) $amount) <= self::TOLERANCE) {
                continue;
            }

            $number++;
            $dekaTotal += (float) $amount;
            $name = DB::table('partners_partners')->where('id', $partnerId)->value('name');
            $rows[] = $this->row('Customer balance', ($anonymous ? 'Customer #'.$number : (string) $name).' (only in DEKA)', 0.0, (float) $amount);
        }

        $rows[] = $this->row('Customer balance', 'All customers', $zohoTotal, $dekaTotal);

        return $rows;
    }

    protected function invoicesByStatus(): array
    {
        $zoho = [];

        foreach ($this->export->invoices() as $invoice) {
            $status = match ($invoice['status']) {
                'paid'  => 'paid',
                'draft' => 'draft',
                'void'  => 'void',
                default => (float) $invoice['balance'] < (float) $invoice['total'] ? 'partly paid' : 'not paid',
            };

            $zoho[$status]['count'] = ($zoho[$status]['count'] ?? 0) + 1;
            $zoho[$status]['total'] = ($zoho[$status]['total'] ?? 0) + (float) $invoice['total'];
        }

        $deka = [];

        $moves = DB::table('accounts_account_moves')
            ->where('company_id', $this->company->id)
            ->where('move_type', MoveType::OUT_INVOICE->value)
            ->get(['state', 'payment_state', 'amount_total', 'amount_residual']);

        foreach ($moves as $move) {
            $status = match (true) {
                $move->state === MoveState::DRAFT->value                       => 'draft',
                $move->state === MoveState::CANCEL->value                      => 'void',
                $move->payment_state === PaymentState::PAID->value             => 'paid',
                (float) $move->amount_residual < (float) $move->amount_total   => 'partly paid',
                default                                                        => 'not paid',
            };

            $deka[$status]['count'] = ($deka[$status]['count'] ?? 0) + 1;
            $deka[$status]['total'] = ($deka[$status]['total'] ?? 0) + (float) $move->amount_total;
        }

        $rows = [];

        foreach (['paid', 'partly paid', 'not paid', 'draft', 'void'] as $status) {
            $rows[] = $this->row('Invoices by status', $status.': count', (float) ($zoho[$status]['count'] ?? 0), (float) ($deka[$status]['count'] ?? 0));
            $rows[] = $this->row('Invoices by status', $status.': total', (float) ($zoho[$status]['total'] ?? 0), (float) ($deka[$status]['total'] ?? 0));
        }

        return $rows;
    }

    protected function salesByMonth(): array
    {
        $zoho = [];

        foreach ($this->export->invoices() as $invoice) {
            if (in_array($invoice['status'], ['draft', 'void'], true)) {
                continue;
            }

            $month = substr((string) $invoice['date'], 0, 7);
            $zoho[$month]['untaxed'] = ($zoho[$month]['untaxed'] ?? 0) + (float) $invoice['sub_total'];
            $zoho[$month]['tax'] = ($zoho[$month]['tax'] ?? 0) + (float) $invoice['tax_total'];
        }

        $deka = [];

        $moves = DB::table('accounts_account_moves')
            ->where('company_id', $this->company->id)
            ->where('move_type', MoveType::OUT_INVOICE->value)
            ->where('state', MoveState::POSTED->value)
            ->get(['invoice_date', 'amount_untaxed', 'amount_tax']);

        foreach ($moves as $move) {
            $month = substr((string) $move->invoice_date, 0, 7);
            $deka[$month]['untaxed'] = ($deka[$month]['untaxed'] ?? 0) + (float) $move->amount_untaxed;
            $deka[$month]['tax'] = ($deka[$month]['tax'] ?? 0) + (float) $move->amount_tax;
        }

        $months = array_unique([...array_keys($zoho), ...array_keys($deka)]);
        sort($months);

        $rows = [];

        foreach ($months as $month) {
            $rows[] = $this->row('Sales by month', $month.' untaxed', (float) ($zoho[$month]['untaxed'] ?? 0), (float) ($deka[$month]['untaxed'] ?? 0));
            $rows[] = $this->row('Sales by month', $month.' tax', (float) ($zoho[$month]['tax'] ?? 0), (float) ($deka[$month]['tax'] ?? 0));
        }

        return $rows;
    }

    /**
     * Expenses only: DEKA's side is the journal entries' debits net of
     * credits on the mapped account, so invoice tax on the same account
     * (Zoho's Tax Payable) is not mixed in.
     */
    protected function expensesByMonth(): array
    {
        $zoho = [];

        foreach ($this->export->expenses() as $expense) {
            $month = substr((string) $expense['date'], 0, 7);

            foreach ($expense['line_items'] as $line) {
                $code = $this->codes[(string) $line['account_id']] ?? 'unmapped';
                $zoho[$code.' '.$month] = ($zoho[$code.' '.$month] ?? 0) + (float) $line['amount'];
            }
        }

        $deka = [];
        $codesById = array_flip($this->accountIds);

        $lines = $this->ledger()
            ->where('move.move_type', MoveType::ENTRY->value)
            ->whereNull('move.origin_payment_id')
            ->whereIn('line.account_id', array_values(array_intersect_key($this->accountIds, array_flip(array_map(fn ($key) => explode(' ', $key)[0], array_keys($zoho))))))
            ->where('line.debit', '>', 0)
            ->get(['line.account_id', 'line.date', 'line.debit']);

        foreach ($lines as $line) {
            $key = ($codesById[(int) $line->account_id] ?? '?').' '.substr((string) $line->date, 0, 7);
            $deka[$key] = ($deka[$key] ?? 0) + (float) $line->debit;
        }

        $keys = array_unique([...array_keys($zoho), ...array_keys($deka)]);
        sort($keys);

        return array_map(fn (string $key) => $this->row('Expenses by account and month', $key, (float) ($zoho[$key] ?? 0), (float) ($deka[$key] ?? 0)), $keys);
    }

    /**
     * Zoho's ledger rebuilt from its documents, per DEKA account code,
     * against DEKA's posted ledger.
     */
    protected function trialBalance(): array
    {
        $zoho = [];
        $add = function (?string $zohoAccountId, float $amount) use (&$zoho): void {
            $code = $this->codes[(string) $zohoAccountId] ?? 'unmapped';
            $zoho[$code] = ($zoho[$code] ?? 0) + $amount;
        };

        $receivable = $this->export->receivableAccountId();
        $taxAccounts = array_column($this->export->taxes(), 'tax_account_id', 'tax_id');

        foreach ($this->export->invoices() as $invoice) {
            if (in_array($invoice['status'], ['draft', 'void'], true)) {
                continue;
            }

            $add($receivable, (float) $invoice['total']);

            $lines = 0.0;

            foreach ($invoice['line_items'] as $index => $line) {
                $amount = (float) $line['item_total'];

                // Line totals carry Zoho's own rounding; the last line takes
                // the remainder so the document balances as Zoho's does.
                if ($index === array_key_last($invoice['line_items'])) {
                    $amount = (float) $invoice['sub_total'] - $lines;
                }

                $lines += $amount;
                $add($line['account_id'], -$amount);
            }

            $component = $invoice['line_items'][0]['line_item_taxes'][0]['tax_id'] ?? null;

            if ((float) $invoice['tax_total'] !== 0.0) {
                $add($taxAccounts[$component] ?? null, -(float) $invoice['tax_total']);
            }
        }

        foreach ($this->export->payments() as $payment) {
            foreach ($payment['invoices'] as $application) {
                $withheld = (float) ($application['tax_amount_withheld'] ?? 0);

                $add($payment['account_id'], (float) $application['amount_applied']);
                $add($payment['tax_account_id'] ?? null, $withheld);
                $add($receivable, -((float) $application['amount_applied'] + $withheld));
            }
        }

        foreach ($this->export->expenses() as $expense) {
            foreach ($expense['line_items'] as $line) {
                $add($line['account_id'], (float) $line['amount']);
            }

            $add($expense['paid_through_account_id'], -(float) $expense['total']);
        }

        foreach ($this->export->bankTransactions() as $transaction) {
            $add($transaction['zoho_to_account_id'], (float) $transaction['amount']);
            $add($transaction['zoho_from_account_id'], -(float) $transaction['amount']);
        }

        $deka = $this->ledger()
            ->join('accounts_accounts as account', 'account.id', '=', 'line.account_id')
            ->groupBy('account.code')
            ->selectRaw('account.code as code, SUM(line.balance) as balance')
            ->pluck('balance', 'code')
            ->map(fn ($balance) => (float) $balance)
            ->all();

        $codes = array_unique([...array_keys(array_filter($zoho, fn ($v) => abs($v) > self::TOLERANCE)), ...array_keys(array_filter($deka, fn ($v) => abs($v) > self::TOLERANCE))]);
        sort($codes);

        $rows = array_map(fn ($code) => $this->row('Trial balance', (string) $code, (float) ($zoho[$code] ?? 0), (float) ($deka[$code] ?? 0)), $codes);

        $sum = fn (array $balances, int $sign): float => array_sum(array_filter($balances, fn ($v) => $sign * $v > 0));

        $rows[] = $this->row('Trial balance', 'Total debits', $sum($zoho, 1), $sum($deka, 1));
        $rows[] = $this->row('Trial balance', 'Total credits', -$sum($zoho, -1), -$sum($deka, -1));

        return $rows;
    }
}
