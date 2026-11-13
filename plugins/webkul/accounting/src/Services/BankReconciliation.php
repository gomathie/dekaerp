<?php

namespace Webkul\Accounting\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentStatus;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\BankStatement;
use Webkul\Account\Models\BankStatementLine;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Models\Payment;
use Webkul\Account\Services\MoveWorkflow;
use Webkul\Account\Services\Reconciler;
use Webkul\Accounting\Exceptions\CannotReconcile;
use Webkul\PluginManager\Support\PluginEntitlement;
use Webkul\Support\Services\CompanyContext;

/**
 * Bank reconciliation (accounting plan A3, decision ACC-D3: CSV first).
 *
 * Import a statement, then settle each line in one of two ways:
 *  - match it to the payment it is the bank side of. With the default
 *    "outstanding receipts/payments" set-up, a payment waits in that account
 *    until the bank confirms it; matching posts the bank entry
 *    (bank / outstanding) and reconciles it with the payment through the
 *    existing Reconciler, which is what turns the payment Paid. When the
 *    payment already went straight to the bank account, nothing new is
 *    posted: the line is linked to it.
 *  - book it against an account (bank charges, interest), posting
 *    bank / that account.
 * No new ledger logic: entries go through Move, MoveWorkflow::post() and
 * Reconciler, so the lock date (PeriodLock) and balance checks apply.
 *
 * Every entry point re-checks the page permission, the module switch and
 * the company: the page's checks are not enough, because the super-admin
 * Gate::before skips policies and a Livewire call can carry any id.
 */
class BankReconciliation
{
    public const PERMISSION = 'page_accounting_bank_reconciliation';

    /** Largest statement accepted in one import. */
    public const MAX_ROWS = 5000;

    public const DATE_FORMATS = ['Y-m-d', 'd/m/Y', 'm/d/Y', 'd.m.Y', 'd-m-Y'];

    public function __construct(
        protected Reconciler $reconciler,
        protected MoveWorkflow $workflow,
    ) {}

    /**
     * Import a CSV statement into a bank or cash journal.
     *
     * @param  array{date: int, label: int, amount?: int|null, money_in?: int|null, money_out?: int|null, partner?: int|null, reference?: int|null, date_format: string, has_header?: bool}  $map  zero-based column numbers
     */
    public function import(Journal $journal, string $path, array $map, ?string $name = null): BankStatement
    {
        $journal = $this->bankJournal($journal->getKey());

        $format = in_array($map['date_format'] ?? null, self::DATE_FORMATS, true) ? $map['date_format'] : 'Y-m-d';

        $rows = $this->readRows($path, (bool) ($map['has_header'] ?? true));

        $lines = [];

        foreach ($rows as $number => $row) {
            $lines[] = $this->parseRow($row, $number, $map, $format);
        }

        if ($lines === []) {
            throw CannotReconcile::emptyFile();
        }

        return DB::transaction(function () use ($journal, $lines, $name): BankStatement {
            $dates = collect($lines)->pluck('date');

            $statement = BankStatement::create([
                'company_id'   => $journal->company_id,
                'journal_id'   => $journal->id,
                'name'         => $name ?: __('accounting::bank-reconciliation.statement-name', [
                    'from' => $dates->min()->toDateString(),
                    'to'   => $dates->max()->toDateString(),
                ]),
                'date'         => $dates->max()->toDateString(),
                'balance_end'  => round(collect($lines)->sum('amount'), 4),
                'is_completed' => false,
            ]);

            foreach ($lines as $sort => $line) {
                BankStatementLine::create($line + [
                    'sort'         => $sort + 1,
                    'statement_id' => $statement->id,
                    'journal_id'   => $journal->id,
                    'currency_id'  => $this->currencyId($journal),
                ]);
            }

            return $statement;
        });
    }

    /**
     * Payments this line could be the bank side of, best first: same
     * journal and company, same direction and amount, not yet matched;
     * ones whose partner name appears in the line come first.
     *
     * @return Collection<int, Payment>
     */
    public function suggestions(BankStatementLine $line, int $limit = 10): Collection
    {
        $line = $this->openLine($line->getKey(), lock: false);

        $amount = abs((float) $line->amount);

        $payments = Payment::query()
            ->with('partner')
            ->where('company_id', $line->company_id)
            ->where('journal_id', $line->journal_id)
            ->where('payment_type', (float) $line->amount >= 0 ? PaymentType::RECEIVE : PaymentType::SEND)
            ->whereIn('state', [PaymentStatus::IN_PROCESS, PaymentStatus::PAID])
            ->whereBetween('amount', [$amount - 0.005, $amount + 0.005])
            ->whereNotIn('id', BankStatementLine::query()->whereNotNull('payment_id')->select('payment_id'))
            ->orderByDesc('date')
            ->limit(50)
            ->get()
            ->filter(fn (Payment $payment): bool => $this->isMatchable($payment, $line));

        $label = mb_strtolower(trim($line->partner_name.' '.$line->payment_reference));

        return $payments
            ->sortByDesc(fn (Payment $payment): int => ($line->partner_id && $payment->partner_id === $line->partner_id)
                || ($payment->partner && $label !== '' && str_contains($label, mb_strtolower((string) $payment->partner->name))) ? 1 : 0)
            ->take($limit)
            ->values();
    }

    /**
     * Settle the line with the payment it confirms.
     */
    public function matchPayment(BankStatementLine $line, Payment $payment): BankStatementLine
    {
        return DB::transaction(function () use ($line, $payment): BankStatementLine {
            $line = $this->openLine($line->getKey(), lock: true);

            $payment = Payment::query()
                ->whereKey($payment->getKey())
                ->where('company_id', $line->company_id)
                ->lockForUpdate()
                ->first();

            if (! $payment
                || BankStatementLine::query()->where('payment_id', $payment->id)->exists()
                || ! $this->isMatchable($payment, $line)
                || abs(abs((float) $line->amount) - (float) $payment->amount) > 0.005
                || $payment->payment_type !== ((float) $line->amount >= 0 ? PaymentType::RECEIVE : PaymentType::SEND)) {
                throw CannotReconcile::paymentDoesNotMatch();
            }

            $journal = $this->bankJournal($line->journal_id);

            $outstanding = $this->openOutstandingLine($payment, $journal);

            $moveId = null;

            // A payment posted straight to the bank account is already in
            // the bank: posting again would count the money twice.
            if ($outstanding) {
                $move = $this->postBankEntry($line, $journal, $payment->outstanding_account_id, $payment->partner_id, $payment->name ?: $payment->memo);

                $counterpart = $move->lines()->where('account_id', $payment->outstanding_account_id)->firstOrFail();

                $this->reconciler->reconcile(new \Illuminate\Database\Eloquent\Collection([$counterpart, $outstanding]));

                $moveId = $move->id;
            }

            $payment->refresh();
            $payment->computeState();
            $payment->computeReconciliationStatus();
            $payment->save();

            $line->forceFill([
                'move_id'         => $moveId,
                'payment_id'      => $payment->id,
                'partner_id'      => $line->partner_id ?? $payment->partner_id,
                'is_reconciled'   => true,
                'amount_residual' => 0,
            ])->save();

            return $line;
        });
    }

    /**
     * Book the line against an account: bank charges, interest, a transfer.
     */
    public function bookToAccount(BankStatementLine $line, Account $account, ?string $label = null): BankStatementLine
    {
        return DB::transaction(function () use ($line, $account, $label): BankStatementLine {
            $line = $this->openLine($line->getKey(), lock: true);

            $journal = $this->bankJournal($line->journal_id);

            $account = Account::query()->whereKey($account->getKey())->first();

            // Receivable and payable lines need a partner and belong to an
            // invoice's payment; booking them here would leave an open item
            // nobody can find. Use "match to a payment" for those.
            if (! $account
                || $account->deprecated
                || $account->id === $journal->default_account_id
                || in_array($account->account_type, [AccountType::ASSET_RECEIVABLE, AccountType::LIABILITY_PAYABLE], true)
                || ($account->companies()->exists() && ! $account->companies()->whereKey($line->company_id)->exists())) {
                throw CannotReconcile::accountNotAllowed();
            }

            $move = $this->postBankEntry($line, $journal, $account->id, null, $label ?: $line->partner_name);

            $line->forceFill([
                'move_id'         => $move->id,
                'is_reconciled'   => true,
                'amount_residual' => 0,
            ])->save();

            return $line;
        });
    }

    /**
     * The bank or cash journals the current user may reconcile.
     *
     * @return Collection<int, Journal>
     */
    public function journals(): Collection
    {
        $this->authorize();

        return Journal::query()
            ->whereIn('type', [JournalType::BANK, JournalType::CASH])
            ->whereIn('company_id', app(CompanyContext::class)->activeIds())
            ->orderBy('name')
            ->get();
    }

    /**
     * Post bank / counterpart for the line's amount: money in debits the
     * bank, money out credits it.
     */
    protected function postBankEntry(BankStatementLine $line, Journal $journal, int $counterpartAccountId, ?int $partnerId, ?string $label): Move
    {
        $amount = round((float) $line->amount, 2);
        $name = $label ?: ($line->payment_reference ?: __('accounting::bank-reconciliation.entry-label'));

        $move = Move::create([
            'move_type'   => MoveType::ENTRY,
            'ref'         => $line->payment_reference ?: $line->partner_name,
            'date'        => $line->date?->toDateString() ?? now()->toDateString(),
            'journal_id'  => $journal->id,
            'company_id'  => $line->company_id,
            'partner_id'  => $partnerId,
            'currency_id' => $line->currency_id,
        ]);

        $move->forceFill(['statement_line_id' => $line->id])->saveQuietly();

        foreach ([[$journal->default_account_id, $amount, null], [$counterpartAccountId, -$amount, $partnerId]] as [$accountId, $balance, $linePartnerId]) {
            MoveLine::create([
                'move_id'           => $move->id,
                'name'              => $name,
                'date_maturity'     => $move->date,
                // Set explicitly: a line without a display type is costed
                // through computeAccountId()'s default branch, which swaps
                // the account for the journal's default (AGENTS.md).
                'display_type'      => DisplayType::PRODUCT,
                'account_id'        => $accountId,
                'partner_id'        => $linePartnerId,
                'currency_id'       => $line->currency_id,
                'amount_currency'   => $balance,
                'debit'             => $balance > 0 ? $balance : 0.0,
                'credit'            => $balance < 0 ? -$balance : 0.0,
                'balance'           => $balance,
                'statement_id'      => $line->statement_id,
                'statement_line_id' => $line->id,
            ]);
        }

        AccountFacade::computeAccountMove($move);

        return $this->workflow->post($move->refresh());
    }

    /**
     * The payment's unreconciled line on its outstanding account, or null
     * when the payment went straight to the bank account.
     */
    protected function openOutstandingLine(Payment $payment, Journal $journal): ?MoveLine
    {
        if (! $payment->move_id || ! $payment->outstanding_account_id
            || $payment->outstanding_account_id === $journal->default_account_id
            || $payment->outstandingAccount?->account_type === AccountType::ASSET_CASH) {
            return null;
        }

        return MoveLine::query()
            ->where('move_id', $payment->move_id)
            ->where('account_id', $payment->outstanding_account_id)
            ->where('reconciled', false)
            ->first();
    }

    protected function isMatchable(Payment $payment, BankStatementLine $line): bool
    {
        if ($payment->company_id !== $line->company_id || $payment->journal_id !== $line->journal_id || ! $payment->move_id) {
            return false;
        }

        if ($payment->move()->value('state') !== MoveState::POSTED) {
            return false;
        }

        $journal = Journal::query()->find($line->journal_id);

        // Waiting in an outstanding account, or already paid straight into
        // this bank account (then the match only links the two).
        return $this->openOutstandingLine($payment, $journal) !== null
            || ($payment->state === PaymentStatus::PAID && $this->postsToBank($payment, $journal));
    }

    protected function postsToBank(Payment $payment, Journal $journal): bool
    {
        return $payment->outstanding_account_id === $journal->default_account_id
            || $payment->outstandingAccount?->account_type === AccountType::ASSET_CASH;
    }

    /**
     * Re-read the line under the company scope and the active companies.
     */
    protected function openLine(int $id, bool $lock): BankStatementLine
    {
        $this->authorize();

        $query = BankStatementLine::query()
            ->whereKey($id)
            ->whereIn('company_id', app(CompanyContext::class)->activeIds());

        if ($lock) {
            $query->lockForUpdate();
        }

        $line = $query->first();

        if (! $line) {
            throw CannotReconcile::notAllowed();
        }

        if ($line->is_reconciled) {
            throw CannotReconcile::alreadyReconciled();
        }

        if (! PluginEntitlement::enabledForCompany('accounting', (int) $line->company_id)) {
            throw CannotReconcile::notAllowed();
        }

        return $line;
    }

    protected function bankJournal(int $id): Journal
    {
        $this->authorize();

        $journal = Journal::query()
            ->whereKey($id)
            ->whereIn('company_id', app(CompanyContext::class)->activeIds())
            ->first();

        if (! $journal || ! PluginEntitlement::enabledForCompany('accounting', (int) $journal->company_id)) {
            throw CannotReconcile::notAllowed();
        }

        if (! in_array($journal->type, [JournalType::BANK, JournalType::CASH], true)) {
            throw CannotReconcile::notABankJournal();
        }

        if (! $journal->default_account_id) {
            throw CannotReconcile::missingBankAccount();
        }

        // Lines are posted in the company currency only (first version).
        // A foreign-currency bank needs rates on every line; refused rather
        // than posted at a wrong rate.
        if ($journal->currency_id && $journal->currency_id !== $journal->company?->currency_id) {
            throw CannotReconcile::foreignCurrency();
        }

        return $journal;
    }

    protected function currencyId(Journal $journal): ?int
    {
        return $journal->currency_id ?: $journal->company?->currency_id;
    }

    protected function authorize(): void
    {
        if (! auth()->user()?->can(self::PERMISSION)) {
            throw CannotReconcile::notAllowed();
        }
    }

    /**
     * @return array<int, array<int, string>> row number (1-based, as the user sees it) => cells
     */
    protected function readRows(string $path, bool $hasHeader): array
    {
        $handle = @fopen($path, 'rb');

        if ($handle === false) {
            throw CannotReconcile::emptyFile();
        }

        $rows = [];
        $number = 0;

        try {
            $first = fgets($handle);

            if ($first === false) {
                throw CannotReconcile::emptyFile();
            }

            // Banks export with commas or semicolons; take whichever the
            // first line uses more.
            $delimiter = substr_count($first, ';') > substr_count($first, ',') ? ';' : ',';

            rewind($handle);

            while (($cells = fgetcsv($handle, 0, $delimiter, '"', '')) !== false) {
                $number++;

                if ($number === 1 && $hasHeader) {
                    continue;
                }

                if ($cells === [null] || trim(implode('', $cells)) === '') {
                    continue;
                }

                if (count($rows) >= self::MAX_ROWS) {
                    throw CannotReconcile::tooManyRows(self::MAX_ROWS);
                }

                $rows[$number] = array_map(fn ($cell): string => trim((string) $cell, " \t\n\r\0\x0B\xEF\xBB\xBF"), $cells);
            }
        } finally {
            fclose($handle);
        }

        return $rows;
    }

    /**
     * @param  array<int, string>  $row
     * @return array{date: Carbon, amount: float, partner_name: ?string, payment_reference: ?string}
     */
    protected function parseRow(array $row, int $number, array $map, string $format): array
    {
        $cell = fn (?int $index): ?string => $index === null ? null : (($row[$index] ?? '') !== '' ? $row[$index] : null);

        try {
            $date = Carbon::createFromFormat('!'.$format, (string) $cell($map['date']));
        } catch (Throwable) {
            $date = false;
        }

        if (! $date) {
            throw CannotReconcile::badRow($number, __('accounting::bank-reconciliation.fields.date'));
        }

        if (isset($map['amount'])) {
            $amount = $this->number($cell($map['amount']));
        } else {
            $in = $this->number($cell($map['money_in'] ?? null) ?? '0');
            $out = $this->number($cell($map['money_out'] ?? null) ?? '0');
            $amount = $in === null || $out === null ? null : abs($in) - abs($out);
        }

        if ($amount === null || abs($amount) < 0.005) {
            throw CannotReconcile::badRow($number, __('accounting::bank-reconciliation.fields.amount'));
        }

        $label = $cell($map['label']);

        return [
            'date'              => $date,
            'amount'            => round($amount, 4),
            'partner_name'      => ($partner = $cell($map['partner'] ?? null)) !== null ? mb_substr($partner, 0, 255) : null,
            'payment_reference' => ($ref = $cell($map['reference'] ?? null) ?? $label) !== null ? mb_substr($ref, 0, 255) : null,
        ];
    }

    /**
     * "1,234.50", "1.234,50", "-50", "(50.00)" -> float; anything else null.
     */
    protected function number(?string $value): ?float
    {
        if ($value === null) {
            return null;
        }

        $value = str_replace([' ', "\u{00A0}"], '', $value);
        $negative = str_starts_with($value, '(') && str_ends_with($value, ')');
        $value = trim($value, '()');

        if (preg_match('/^-?\d{1,3}(,\d{3})+(\.\d+)?$/', $value)) {
            $value = str_replace(',', '', $value);
        } elseif (preg_match('/^-?\d{1,3}(\.\d{3})*(,\d+)?$/', $value) && str_contains($value, ',')) {
            $value = str_replace(['.', ','], ['', '.'], $value);
        } else {
            $value = str_replace(',', '', $value);
        }

        if (! is_numeric($value)) {
            return null;
        }

        return $negative ? -abs((float) $value) : (float) $value;
    }
}
