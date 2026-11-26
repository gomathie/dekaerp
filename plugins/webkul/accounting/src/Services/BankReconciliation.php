<?php

namespace Webkul\Accounting\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
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
use Webkul\Account\Exceptions\PeriodLockedException;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\BankStatement;
use Webkul\Account\Models\BankStatementLine;
use Webkul\Account\Models\Expense;
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

    /** Days either side of a statement line's date an expense may be dated. */
    public const EXPENSE_DATE_DAYS = 3;

    public function __construct(
        protected Reconciler $reconciler,
        protected MoveWorkflow $workflow,
    ) {}

    /** The mapped fields remembered per journal (plan #9 R-4), zero-based. */
    public const COLUMN_FIELDS = ['date', 'label', 'amount', 'money_in', 'money_out', 'partner', 'reference', 'balance'];

    /**
     * Import a CSV statement into a bank or cash journal.
     *
     * The closing balance (plan #9 R-4) is, in this order: the one typed from
     * the statement, the balance column on the statement's last day, or,
     * when the file gives neither, the previous statement's closing balance
     * plus this statement's lines. The balance check compares it with the
     * books, so a typed or exported balance is preferred over arithmetic.
     *
     * @param  array{date: int, label: int, amount?: int|null, money_in?: int|null, money_out?: int|null, partner?: int|null, reference?: int|null, balance?: int|null, date_format: string, has_header?: bool}  $map  zero-based column numbers
     */
    public function import(Journal $journal, string $path, array $map, ?string $name = null, ?float $closingBalance = null): BankStatement
    {
        $journal = $this->bankJournal($journal->getKey());

        $format = in_array($map['date_format'] ?? null, self::DATE_FORMATS, true) ? $map['date_format'] : 'Y-m-d';

        $hasHeader = (bool) ($map['has_header'] ?? true);

        [$header, $rows] = $this->readRows($path, $hasHeader);

        $map = $this->followMovedColumns($journal, $map, $header);

        $lines = [];
        $balances = [];
        $numbers = [];

        foreach ($rows as $number => $row) {
            $parsed = $this->parseRow($row, $number, $map, $format);

            $balances[] = $parsed['balance'];
            $numbers[] = $number;

            unset($parsed['balance']);

            $lines[] = $parsed;
        }

        if ($lines === []) {
            throw CannotReconcile::emptyFile();
        }

        if ($closingBalance === null && isset($map['balance'])) {
            $closingBalance = $this->closingFromColumn($lines, $balances, $numbers);
        }

        return DB::transaction(function () use ($journal, $lines, $name, $closingBalance, $map, $header, $hasHeader, $format): BankStatement {
            // Locked so two imports into one account cannot both start from
            // the same previous closing balance.
            $journal = Journal::query()->whereKey($journal->id)->lockForUpdate()->firstOrFail();

            $dates = collect($lines)->pluck('date');
            $movement = round(collect($lines)->sum('amount'), 4);

            $previousClosing = (float) (BankStatement::query()
                ->where('company_id', $journal->company_id)
                ->where('journal_id', $journal->id)
                ->orderByDesc('date')
                ->orderByDesc('id')
                ->value('balance_end_real') ?? 0);

            $closing = $closingBalance !== null ? round($closingBalance, 4) : round($previousClosing + $movement, 4);

            $statement = BankStatement::create([
                'company_id'       => $journal->company_id,
                'journal_id'       => $journal->id,
                'name'             => $name ?: __('accounting::bank-reconciliation.statement-name', [
                    'from' => $dates->min()->toDateString(),
                    'to'   => $dates->max()->toDateString(),
                ]),
                'date'             => $dates->max()->toDateString(),
                'balance_start'    => round($closing - $movement, 4),
                'balance_end'      => $closing,
                'balance_end_real' => $closing,
                'is_completed'     => false,
            ]);

            foreach ($lines as $sort => $line) {
                BankStatementLine::create($line + [
                    'sort'         => $sort + 1,
                    'statement_id' => $statement->id,
                    'journal_id'   => $journal->id,
                    'currency_id'  => $this->currencyId($journal),
                ]);
            }

            $this->rememberColumns($journal, $map, $header, $hasHeader, $format);

            return $statement;
        });
    }

    /**
     * The column mapping the last successful import into this journal used,
     * to pre-fill the next one; null when there is none yet. Read through
     * bankJournal(), so a journal of a company the user is not working in is
     * refused rather than answered.
     *
     * @return array{columns: array<string, int>, headers: array<string, string>, date_format: string, has_header: bool}|null
     */
    public function rememberedColumns(Journal|int $journal): ?array
    {
        $journal = $this->bankJournal($journal instanceof Journal ? $journal->getKey() : $journal);

        $stored = $journal->statement_import_columns;

        if (! is_array($stored) || ! is_array($stored['columns'] ?? null)) {
            return null;
        }

        return [
            'columns'     => array_map('intval', array_intersect_key($stored['columns'], array_flip(self::COLUMN_FIELDS))),
            'headers'     => array_map('strval', array_intersect_key((array) ($stored['headers'] ?? []), array_flip(self::COLUMN_FIELDS))),
            'date_format' => in_array($stored['date_format'] ?? null, self::DATE_FORMATS, true) ? $stored['date_format'] : 'Y-m-d',
            'has_header'  => (bool) ($stored['has_header'] ?? true),
        ];
    }

    /**
     * Balance check per account (plan #9 R-4): for every bank and cash
     * journal of the current company, the closing balance of its latest
     * statement, the balance of its bank account in the posted ledger, the
     * difference, and how many lines are still unmatched. Five queries for
     * any number of journals.
     *
     * State: "none" without a statement; "to-match" while lines are open;
     * "clean" when nothing is open and the two balances agree; "difference"
     * when nothing is open but they still disagree (an opening balance never
     * booked, or a payment posted straight to the bank twice).
     *
     * @return list<array{journal: Journal, currency: ?string, statement: ?float, book: float, difference: ?float, date: ?string, to_match: int, state: string}>
     */
    public function balances(): array
    {
        $this->authorize();

        $companyId = app(CompanyContext::class)->currentId();

        if (! $companyId || ! PluginEntitlement::enabledForCompany('accounting', (int) $companyId)) {
            return [];
        }

        $journals = Journal::query()
            ->with(['currency', 'company.currency'])
            ->where('company_id', $companyId)
            ->whereIn('type', [JournalType::BANK, JournalType::CASH])
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        if ($journals->isEmpty()) {
            return [];
        }

        $journalIds = $journals->modelKeys();

        $latest = BankStatement::query()
            ->where('company_id', $companyId)
            ->whereIn('journal_id', $journalIds)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get(['id', 'journal_id', 'date', 'balance_end_real'])
            ->unique('journal_id')
            ->keyBy('journal_id');

        $open = BankStatementLine::query()
            ->where('company_id', $companyId)
            ->whereIn('journal_id', $journalIds)
            ->where('is_reconciled', false)
            ->groupBy('journal_id')
            ->selectRaw('journal_id, COUNT(*) AS open_lines')
            ->pluck('open_lines', 'journal_id');

        // A raw ledger read, filtered by company explicitly: through the
        // Move model, OwnershipScope would hide other users' entries from an
        // "individual" user, and the book balance would depend on who looks.
        $ledger = DB::table('accounts_account_move_lines')
            ->join('accounts_account_moves', 'accounts_account_moves.id', '=', 'accounts_account_move_lines.move_id')
            ->where('accounts_account_moves.state', MoveState::POSTED->value)
            ->where('accounts_account_moves.company_id', $companyId)
            ->whereIn('accounts_account_move_lines.account_id', $journals->pluck('default_account_id')->filter()->unique()->all())
            ->groupBy('accounts_account_move_lines.account_id', 'accounts_account_move_lines.currency_id')
            ->selectRaw('accounts_account_move_lines.account_id, accounts_account_move_lines.currency_id, COALESCE(SUM(accounts_account_move_lines.balance), 0) AS balance, COALESCE(SUM(accounts_account_move_lines.amount_currency), 0) AS amount_currency')
            ->get()
            ->groupBy('account_id');

        return $journals->map(function (Journal $journal) use ($latest, $open, $ledger): array {
            $companyCurrencyId = $journal->company?->currency_id;
            $foreign = $journal->currency_id && $journal->currency_id !== $companyCurrencyId;

            $rows = $ledger->get($journal->default_account_id, collect());

            // A foreign-currency account is read in its own currency, so it
            // compares with a statement in that currency.
            $book = $foreign
                ? (float) $rows->where('currency_id', $journal->currency_id)->sum('amount_currency')
                : (float) $rows->sum('balance');

            $statement = $latest->get($journal->id);
            $toMatch = (int) ($open[$journal->id] ?? 0);

            $statementBalance = $statement ? round((float) $statement->balance_end_real, 2) : null;
            $difference = $statementBalance !== null ? round($statementBalance - round($book, 2), 2) : null;

            $state = match (true) {
                $statement === null       => 'none',
                $toMatch > 0              => 'to-match',
                abs($difference) < 0.005  => 'clean',
                default                   => 'difference',
            };

            return [
                'journal'    => $journal,
                'currency'   => ($foreign ? $journal->currency : $journal->company?->currency)?->name,
                'statement'  => $statementBalance,
                'book'       => round($book, 2),
                'difference' => $difference,
                'date'       => $statement?->date ? Carbon::parse($statement->date)->toDateString() : null,
                'to_match'   => $toMatch,
                'state'      => $state,
            ];
        })->values()->all();
    }

    /**
     * Each open line that has exactly one suggested payment, with that
     * payment (plan #9 R-4, "Match all suggested"). The suggestion rule is
     * suggestions(), not a second copy: the query below only skips lines
     * that have no payment of that amount in their journal at all, which
     * suggestions() would refuse anyway. A payment that is the only
     * suggestion of two lines is left out for both: which line it belongs
     * to is a decision for a person.
     *
     * Expenses count as candidates too (plan #10, user 2026-11-26): a line is
     * single only when it has exactly one candidate of either kind, and the
     * pair then carries `expense` instead of `payment`.
     *
     * @return Collection<int, array{line: BankStatementLine, payment?: Payment, expense?: Expense}>
     */
    public function singleSuggestions(): Collection
    {
        $this->authorize();

        $lines = BankStatementLine::query()
            ->whereIn('company_id', app(CompanyContext::class)->activeIds())
            ->where('is_reconciled', false)
            ->where(fn ($lines) => $lines
                ->whereExists(fn ($query) => $query
                    ->selectRaw('1')
                    ->from('accounts_account_payments')
                    ->whereColumn('accounts_account_payments.company_id', 'accounts_bank_statement_lines.company_id')
                    ->whereColumn('accounts_account_payments.journal_id', 'accounts_bank_statement_lines.journal_id')
                    ->whereRaw('ABS(accounts_bank_statement_lines.amount) BETWEEN accounts_account_payments.amount - 0.005 AND accounts_account_payments.amount + 0.005'))
                ->orWhereExists(fn ($query) => $query
                    ->selectRaw('1')
                    ->from('accounts_expenses')
                    ->whereNull('accounts_expenses.deleted_at')
                    ->whereColumn('accounts_expenses.company_id', 'accounts_bank_statement_lines.company_id')
                    ->whereColumn('accounts_expenses.journal_id', 'accounts_bank_statement_lines.journal_id')
                    ->whereRaw('ABS(accounts_bank_statement_lines.amount) BETWEEN accounts_expenses.amount_total - 0.005 AND accounts_expenses.amount_total + 0.005')))
            ->orderBy('date')
            ->orderBy('id')
            ->get();

        $pairs = collect();

        foreach ($lines as $line) {
            try {
                $payments = $this->suggestions($line, 2);
                $expenses = $this->expenseSuggestions($line, 2);
            } catch (CannotReconcile) {
                continue;
            }

            if ($payments->count() + $expenses->count() !== 1) {
                continue;
            }

            $pairs->push($payments->isNotEmpty()
                ? ['line' => $line, 'payment' => $payments->first()]
                : ['line' => $line, 'expense' => $expenses->first()]);
        }

        $key = fn (array $pair): string => isset($pair['expense']) ? 'expense:'.$pair['expense']->id : 'payment:'.$pair['payment']->id;

        $shared = $pairs->countBy($key)->filter(fn (int $count): bool => $count > 1);

        return $pairs->reject(fn (array $pair): bool => $shared->has($key($pair)))->values();
    }

    /**
     * Match every line singleSuggestions() returns, each through
     * matchPayment() and so through its company, permission and lock-date
     * guards. One transaction per line, not one for all: a line in a locked
     * period, or one someone matched meanwhile, is left open and counted,
     * and does not undo the matches that were valid.
     *
     * @return array{matched: int, left: int}
     */
    public function matchAllSuggested(): array
    {
        $matched = 0;
        $left = 0;

        foreach ($this->singleSuggestions() as $pair) {
            try {
                isset($pair['expense'])
                    ? $this->matchExpense($pair['line'], $pair['expense'])
                    : $this->matchPayment($pair['line'], $pair['payment']);

                $matched++;
            } catch (CannotReconcile|PeriodLockedException) {
                $left++;
            }
        }

        return ['matched' => $matched, 'left' => $left];
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
     * Expenses this line could be the bank side of (plan #10, user
     * 2026-11-26): money out only, same company and journal, the same amount
     * paid, dated within EXPENSE_DATE_DAYS of the line, posted, and not yet
     * matched to another line. Nearest date first. Same amount tolerance as
     * suggestions(), which stays the payments rule.
     *
     * @return Collection<int, Expense>
     */
    public function expenseSuggestions(BankStatementLine $line, int $limit = 10): Collection
    {
        $line = $this->openLine($line->getKey(), lock: false);

        if ((float) $line->amount >= 0) {
            return collect();
        }

        return $this->matchableExpenses($line)
            ->limit(50)
            ->get()
            ->sortBy(fn (Expense $expense): int => (int) abs($expense->date->diffInDays($line->date)))
            ->take($limit)
            ->values();
    }

    /**
     * Settle the line with the expense it confirms. Nothing is posted: the
     * expense's entry already credited this bank account, and posting the
     * bank side again would count the money twice. The line is linked to
     * that entry, as for a payment that went straight to the bank.
     */
    public function matchExpense(BankStatementLine $line, Expense $expense): BankStatementLine
    {
        return DB::transaction(function () use ($line, $expense): BankStatementLine {
            $line = $this->openLine($line->getKey(), lock: true);

            $this->bankJournal($line->journal_id);

            $expense = (float) $line->amount < 0
                ? $this->matchableExpenses($line)->whereKey($expense->getKey())->lockForUpdate()->first()
                : null;

            if (! $expense) {
                throw CannotReconcile::expenseDoesNotMatch();
            }

            $line->forceFill([
                'move_id'         => $expense->move_id,
                'partner_id'      => $line->partner_id ?? $expense->partner_id,
                'is_reconciled'   => true,
                'amount_residual' => 0,
            ])->save();

            return $line;
        });
    }

    /**
     * The one query for expenses a line may match, used by both the
     * suggestion and the match, so a match never accepts what would not be
     * suggested. Read under the company scope and filtered by the line's
     * company explicitly; the entry is read past Move's ownership scope
     * (the expense row names it).
     */
    protected function matchableExpenses(BankStatementLine $line): Builder
    {
        $amount = abs((float) $line->amount);
        $date = Carbon::parse($line->date);

        return Expense::query()
            ->where('company_id', $line->company_id)
            ->where('journal_id', $line->journal_id)
            ->where('currency_id', $line->currency_id ?: $line->company?->currency_id)
            ->whereBetween('amount_total', [$amount - 0.005, $amount + 0.005])
            ->whereBetween('date', [
                $date->copy()->subDays(self::EXPENSE_DATE_DAYS)->toDateString(),
                $date->copy()->addDays(self::EXPENSE_DATE_DAYS)->toDateString(),
            ])
            ->whereIn('move_id', Move::withoutGlobalScopes()->where('state', MoveState::POSTED)->select('id'))
            ->whereNotIn('move_id', BankStatementLine::query()->withoutGlobalScopes()->whereNotNull('move_id')->select('move_id'))
            ->with('partner');
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
     * When the user kept a remembered column number and this file's header
     * row has that column's remembered header name somewhere else, use the
     * column where the name now is: banks add or reorder columns. Stored
     * names are compared with header cells only, never with data rows, and
     * a number the user changed is left as they typed it.
     *
     * @param  array<string, mixed>  $map
     * @param  list<string>  $header
     * @return array<string, mixed>
     */
    protected function followMovedColumns(Journal $journal, array $map, array $header): array
    {
        $remembered = $this->rememberedColumns($journal);

        if ($remembered === null || $header === [] || ! ($map['has_header'] ?? true)) {
            return $map;
        }

        $positions = [];

        foreach ($header as $index => $cell) {
            $positions[$this->headerKey($cell)] ??= $index;
        }

        foreach ($remembered['headers'] as $field => $name) {
            $key = $this->headerKey($name);

            if ($key !== ''
                && isset($map[$field], $remembered['columns'][$field], $positions[$key])
                && $map[$field] === $remembered['columns'][$field]) {
                $map[$field] = $positions[$key];
            }
        }

        return $map;
    }

    /**
     * Store this import's mapping on the journal for next time. Called inside
     * the import's transaction on the journal row it locked, which
     * bankJournal() already checked belongs to the user's companies. Header
     * names are kept short: they are only ever compared with a header row.
     *
     * @param  array<string, mixed>  $map
     * @param  list<string>  $header
     */
    protected function rememberColumns(Journal $journal, array $map, array $header, bool $hasHeader, string $format): void
    {
        $columns = [];
        $headers = [];

        foreach (self::COLUMN_FIELDS as $field) {
            if (! isset($map[$field])) {
                continue;
            }

            $columns[$field] = (int) $map[$field];

            if ($hasHeader && filled($header[$map[$field]] ?? null)) {
                $headers[$field] = mb_substr($header[$map[$field]], 0, 100);
            }
        }

        // Quietly: only this column changes, and the journal's own saving
        // hooks (sequences, consistency checks) have nothing to do with it.
        $journal->forceFill(['statement_import_columns' => [
            'columns'     => $columns,
            'headers'     => $headers,
            'date_format' => $format,
            'has_header'  => $hasHeader,
        ]])->saveQuietly();
    }

    protected function headerKey(string $value): string
    {
        return mb_strtolower(trim(preg_replace('/\s+/u', ' ', $value) ?? ''));
    }

    /**
     * The balance column on the statement's last day: the last row of that
     * day in file order, or the first when the file lists the newest first.
     *
     * @param  list<array{date: Carbon}>  $lines
     * @param  list<?float>  $balances
     * @param  list<int>  $numbers  the file row numbers, as the user sees them
     */
    protected function closingFromColumn(array $lines, array $balances, array $numbers): float
    {
        $lastDay = collect($lines)->max('date');
        $newestFirst = $lines[0]['date']->gt($lines[count($lines) - 1]['date']);

        $candidates = array_keys(array_filter($lines, fn (array $line): bool => $line['date']->equalTo($lastDay)));

        $index = $newestFirst ? min($candidates) : max($candidates);

        if ($balances[$index] === null) {
            throw CannotReconcile::badRow($numbers[$index], __('accounting::bank-reconciliation.fields.balance'));
        }

        return $balances[$index];
    }

    /**
     * @return array{0: list<string>, 1: array<int, array<int, string>>} the header cells (empty without a header) and row number (1-based, as the user sees it) => cells
     */
    protected function readRows(string $path, bool $hasHeader): array
    {
        $handle = @fopen($path, 'rb');

        if ($handle === false) {
            throw CannotReconcile::emptyFile();
        }

        $rows = [];
        $header = [];
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
                    $header = array_map(fn ($cell): string => trim((string) $cell, " \t\n\r\0\x0B\xEF\xBB\xBF"), $cells);

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

        return [$header, $rows];
    }

    /**
     * @param  array<int, string>  $row
     * @return array{date: Carbon, amount: float, partner_name: ?string, payment_reference: ?string, balance: ?float}
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

        // A running balance is optional on every row (some banks fill it only
        // at day end); one that is there but unreadable is refused, so a
        // wrong column number does not become a wrong closing balance.
        $balanceCell = $cell($map['balance'] ?? null);
        $balance = $balanceCell === null ? null : $this->number($balanceCell);

        if ($balanceCell !== null && $balance === null) {
            throw CannotReconcile::badRow($number, __('accounting::bank-reconciliation.fields.balance'));
        }

        return [
            'date'              => $date,
            'amount'            => round($amount, 4),
            'partner_name'      => ($partner = $cell($map['partner'] ?? null)) !== null ? mb_substr($partner, 0, 255) : null,
            'payment_reference' => ($ref = $cell($map['reference'] ?? null) ?? $label) !== null ? mb_substr($ref, 0, 255) : null,
            'balance'           => $balance,
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
