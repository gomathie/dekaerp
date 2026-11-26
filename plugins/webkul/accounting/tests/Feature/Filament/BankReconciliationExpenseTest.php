<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Webkul\Account\Models\BankStatement;
use Webkul\Account\Models\BankStatementLine;
use Webkul\Account\Models\Expense;
use Webkul\Account\Models\Journal;
use Webkul\Account\Services\ExpenseRecorder;
use Webkul\Accounting\Exceptions\CannotReconcile;
use Webkul\Accounting\Services\BankReconciliation;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\Support\PluginAccess;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../accounts/tests/Helpers/AccountHelper.php';

/**
 * "Match to an expense" in bank reconciliation (plan #10, user decision C,
 * 2026-11-26): a money-out line is suggested the expense paid from the same
 * account for the same amount within a few days, and matching it links the
 * two without posting anything, so the money is counted once.
 */
beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounting');
    TestBootstrapHelper::ensurePluginInstalled('invoices');

    DB::table('plugins')->whereIn('name', ['accounts', 'accounting', 'invoices'])->update([
        'is_installed' => true,
        'is_active'    => true,
        'updated_at'   => now(),
    ]);

    Package::$plugins = Plugin::all()->keyBy('name');
    PluginAccess::flush();

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    FilamentHelper::actingAs([BankReconciliation::PERMISSION]);

    $this->service = app(BankReconciliation::class);
    $this->company = AccountHelper::company();
    $this->bank = AccountHelper::bankJournal();
    $this->expenseAccount = AccountHelper::account('expense');
});

function reconExpense(float $amount, ?string $date = null, ?Journal $journal = null): Expense
{
    return app(ExpenseRecorder::class)->create([
        'date'               => $date ?? now()->toDateString(),
        'amount'             => $amount,
        'currency_id'        => test()->company->currency_id,
        'expense_account_id' => test()->expenseAccount->id,
        'journal_id'         => ($journal ?? test()->bank)->id,
    ], test()->company->id);
}

function reconLine(float $amount, ?string $date = null, ?Journal $journal = null): BankStatementLine
{
    $journal ??= test()->bank;

    $statement = BankStatement::create([
        'company_id' => $journal->company_id,
        'journal_id' => $journal->id,
        'name'       => 'Test statement',
        'date'       => now()->toDateString(),
    ]);

    return BankStatementLine::create([
        'statement_id'      => $statement->id,
        'journal_id'        => $journal->id,
        'currency_id'       => $journal->currency_id ?: $journal->company->currency_id,
        'date'              => $date ?? now()->toDateString(),
        'amount'            => $amount,
        'payment_reference' => 'Card payment',
    ]);
}

function bankLedgerBalance(Journal $journal): float
{
    return (float) DB::table('accounts_account_move_lines')
        ->join('accounts_account_moves', 'accounts_account_moves.id', '=', 'accounts_account_move_lines.move_id')
        ->where('accounts_account_moves.state', 'posted')
        ->where('accounts_account_move_lines.account_id', $journal->default_account_id)
        ->sum('accounts_account_move_lines.balance');
}

it('suggests the expense paid from the same account for the same amount within a few days', function () {
    $expense = reconExpense(350, now()->subDays(2)->toDateString());
    reconExpense(350, now()->subDays(10)->toDateString());
    reconExpense(351);

    $line = reconLine(-350);

    expect($this->service->expenseSuggestions($line)->pluck('id')->all())->toBe([$expense->id]);
});

it('never suggests an expense for money coming in', function () {
    reconExpense(350);

    expect($this->service->expenseSuggestions(reconLine(350)))->toHaveCount(0);
});

it('matches a line to its expense without posting anything, so the money counts once', function () {
    $expense = reconExpense(350);
    $line = reconLine(-350);

    $before = bankLedgerBalance($this->bank);
    $moves = DB::table('accounts_account_moves')->count();

    $this->service->matchExpense($line, $expense);

    $line->refresh();

    expect($line->is_reconciled)->toBeTrue()
        ->and($line->move_id)->toBe($expense->move_id)
        ->and(DB::table('accounts_account_moves')->count())->toBe($moves)
        ->and(bankLedgerBalance($this->bank))->toBe($before)
        ->and($before)->toBe(-350.0);
});

it('refuses an expense of another amount, journal or date, or one already matched', function () {
    $expense = reconExpense(350);
    $other = AccountHelper::bankJournal();

    $message = 'That expense does not match this line: it must be paid from the same bank account, for the same amount, within a few days of the line, and not already matched.';

    expect(fn () => $this->service->matchExpense(reconLine(-300), $expense))->toThrow(CannotReconcile::class, $message)
        ->and(fn () => $this->service->matchExpense(reconLine(-350, null, $other), $expense))->toThrow(CannotReconcile::class, $message)
        ->and(fn () => $this->service->matchExpense(reconLine(-350, now()->addDays(10)->toDateString()), $expense))->toThrow(CannotReconcile::class, $message);

    $this->service->matchExpense(reconLine(-350), $expense);

    expect(fn () => $this->service->matchExpense(reconLine(-350), $expense))->toThrow(CannotReconcile::class, $message);
});

it('matches a single suggested expense with "match all suggested"', function () {
    $expense = reconExpense(84.50);
    $line = reconLine(-84.50);

    $pairs = $this->service->singleSuggestions();

    expect($pairs->map(fn (array $pair): array => [$pair['line']->id, $pair['expense']->id ?? null])->all())->toBe([[$line->id, $expense->id]]);

    expect($this->service->matchAllSuggested())->toBe(['matched' => 1, 'left' => 0])
        ->and($line->refresh()->move_id)->toBe($expense->move_id);
});

it('leaves out an expense that is the only suggestion of two lines', function () {
    reconExpense(120);
    reconLine(-120);
    reconLine(-120);

    expect($this->service->singleSuggestions())->toHaveCount(0);
});
