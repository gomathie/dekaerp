<?php

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Models\Move;
use Webkul\Account\Services\ExpenseRecorder;
use Webkul\Invoice\Filament\Clusters\Vendors;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ExpenseResource;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ExpenseResource\Pages\CreateExpense;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ExpenseResource\Pages\EditExpense;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ExpenseResource\Pages\ListExpenses;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ExpenseResource\Pages\ViewExpense;
use Webkul\Invoice\Models\Expense;
use Webkul\PluginManager\Support\PluginAccess;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../accounts/tests/Helpers/AccountHelper.php';

/**
 * The Expenses screen (plan #10 Z1, mockup approved 2026-11-26): beside
 * Bills, behind its own Shield permissions, and every save goes through
 * ExpenseRecorder.
 */
beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('invoices');
    PluginAccess::flush();

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    $this->company = AccountHelper::company();
    $this->bank = AccountHelper::bankJournal();
    $this->expenseAccount = AccountHelper::account('expense');
});

function expensePermissions(): array
{
    return [
        'view_any_invoice_expense',
        'view_invoice_expense',
        'create_invoice_expense',
        'update_invoice_expense',
        'delete_invoice_expense',
    ];
}

function screenExpense(array $overrides = []): Expense
{
    $expense = app(ExpenseRecorder::class)->create(array_merge([
        'date'               => now()->toDateString(),
        'amount'             => 84.50,
        'currency_id'        => test()->company->currency_id,
        'expense_account_id' => test()->expenseAccount->id,
        'journal_id'         => test()->bank->id,
        'reference'          => 'Printer paper',
    ], $overrides), test()->company->id);

    return Expense::query()->findOrFail($expense->id);
}

it('generates the expense permissions the policy checks', function () {
    FilamentHelper::bootAdminPanel();

    $generated = collect(FilamentShield::getResources())
        ->flatMap(fn (array $resource): array => array_column($resource['permissions'] ?? [], 'key'))
        ->all();

    expect($generated)->toContain(...expensePermissions())
        ->and($generated)->not->toContain('delete_any_invoice_expense');
});

it('sits in the vendors cluster beside bills', function () {
    expect(ExpenseResource::getCluster())->toBe(Vendors::class);
});

it('forbids the expenses list without permission', function () {
    FilamentHelper::actingAs([]);

    Livewire::test(ListExpenses::class)->assertForbidden();
});

it('lists expenses with every column labelled for the phone cards', function () {
    FilamentHelper::actingAs(expensePermissions());

    $expense = screenExpense();

    Livewire::test(ListExpenses::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$expense])
        ->assertCanRenderTableColumn('date')
        ->assertCanRenderTableColumn('expenseAccount.name')
        ->assertCanRenderTableColumn('journal.name')
        ->assertCanRenderTableColumn('amount_total')
        ->assertSee($expense->number)
        ->assertSee('data-label="Expense account"', escape: false)
        ->assertSee('data-label="Paid from"', escape: false);
});

it('creates and posts an expense from the form', function () {
    FilamentHelper::actingAs(expensePermissions());

    Livewire::test(CreateExpense::class)
        ->fillForm([
            'date'               => now()->toDateString(),
            'amount'             => 120,
            'currency_id'        => $this->company->currency_id,
            'expense_account_id' => $this->expenseAccount->id,
            'journal_id'         => $this->bank->id,
            'reference'          => 'Internet',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $expense = Expense::query()->where('reference', 'Internet')->firstOrFail();
    $move = Move::withoutGlobalScopes()->findOrFail($expense->move_id);

    expect($move->state)->toBe(MoveState::POSTED)
        ->and($move->journal_id)->toBe($this->bank->id)
        ->and((float) $expense->amount_total)->toBe(120.0);
});

it('marks the field the service refuses, such as another company\'s journal', function () {
    FilamentHelper::actingAs(expensePermissions());

    $general = AccountHelper::generalJournal();

    Livewire::test(CreateExpense::class)
        ->fillForm([
            'date'               => now()->toDateString(),
            'amount'             => 10,
            'currency_id'        => $this->company->currency_id,
            'expense_account_id' => $this->expenseAccount->id,
            'journal_id'         => $general->id,
        ])
        ->call('create')
        ->assertHasFormErrors(['journal_id']);

    expect(Expense::query()->count())->toBe(0);
});

it('asks a reason before an edit rewrites the entry', function () {
    FilamentHelper::actingAs(expensePermissions());

    $expense = screenExpense();

    Livewire::test(EditExpense::class, ['record' => $expense->getKey()])
        ->fillForm(['amount' => 90])
        ->call('save')
        ->assertHasFormErrors(['reason' => 'required']);

    Livewire::test(EditExpense::class, ['record' => $expense->getKey()])
        ->fillForm(['amount' => 90, 'reason' => 'Typed the wrong amount'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect((float) $expense->refresh()->amount)->toBe(90.0);
});

it('cancels the entry when deleted with a reason', function () {
    FilamentHelper::actingAs(expensePermissions());

    $expense = screenExpense();

    Livewire::test(ViewExpense::class, ['record' => $expense->getKey()])
        ->callAction('deleteExpense', ['reason' => 'Recorded twice']);

    expect(Move::withoutGlobalScopes()->findOrFail($expense->move_id)->state)->toBe(MoveState::CANCEL)
        ->and(Expense::query()->find($expense->getKey()))->toBeNull();
});

it('hides edit and delete from a user who may only view', function () {
    FilamentHelper::actingAs(['view_any_invoice_expense', 'view_invoice_expense']);

    $expense = screenExpense();

    Livewire::test(ViewExpense::class, ['record' => $expense->getKey()])
        ->assertOk()
        ->assertActionHidden('deleteExpense');

    Livewire::test(EditExpense::class, ['record' => $expense->getKey()])->assertForbidden();
});

/**
 * A script or an oversize file is never stored: the upload layer drops it
 * before the form state (checked in a probe run, 2026-11-26), and the
 * service's content check (ExpenseRecorderTest) is the second guard. So
 * the assertion is on what reaches the disk and the row, not on a message.
 */
it('stores a photo receipt and never a script or an oversize file', function () {
    Storage::fake('public');
    FilamentHelper::actingAs(expensePermissions());

    $data = [
        'date'               => now()->toDateString(),
        'amount'             => 40,
        'currency_id'        => $this->company->currency_id,
        'expense_account_id' => $this->expenseAccount->id,
        'journal_id'         => $this->bank->id,
    ];

    Livewire::test(CreateExpense::class)
        ->fillForm([...$data, 'reference' => 'Script', 'receipt_path' => UploadedFile::fake()->create('script.php', 10, 'application/x-php')])
        ->call('create');

    Livewire::test(CreateExpense::class)
        // A real PDF header, so only its size is wrong.
        ->fillForm([...$data, 'reference' => 'Huge', 'receipt_path' => UploadedFile::fake()->createWithContent('huge.pdf', "%PDF-1.4\n".str_repeat('0', 11 * 1024 * 1024))])
        ->call('create');

    expect(Storage::disk('public')->allFiles())->toBe([])
        ->and(Expense::query()->whereNotNull('receipt_path')->count())->toBe(0);

    Livewire::test(CreateExpense::class)
        ->fillForm([...$data, 'reference' => 'With receipt', 'receipt_path' => UploadedFile::fake()->image('receipt.jpg')])
        ->call('create')
        ->assertHasNoFormErrors();

    $expense = Expense::query()->where('reference', 'With receipt')->firstOrFail();

    expect($expense->receipt_path)->toStartWith(ExpenseRecorder::RECEIPT_DIRECTORY.'/')
        ->and($expense->receipt_name)->toBe('receipt.jpg');

    Storage::disk('public')->assertExists($expense->receipt_path);
});
