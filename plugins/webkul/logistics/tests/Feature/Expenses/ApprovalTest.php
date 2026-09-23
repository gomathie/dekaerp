<?php

use Filament\Forms\Components\FileUpload;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Logistics\Enums\ExpensePaidBy;
use Webkul\Logistics\Enums\ExpenseState;
use Webkul\Logistics\Filament\Clusters\Finance\Resources\ExpenseResource\Pages\CreateExpense;
use Webkul\Logistics\Models\Expense;
use Webkul\Logistics\Models\ExpenseCategory;
use Webkul\Logistics\Services\ExpenseApproval;

require_once __DIR__.'/../../Helpers/LogisticsHelper.php';

beforeEach(function () {
    LogisticsHelper::install();

    URL::resolveMissingNamedRoutesUsing(fn (): string => '#');
});

function approvalExpense(int $companyId, array $overrides = []): Expense
{
    return Expense::factory()->create(array_merge([
        'company_id'  => $companyId,
        'category_id' => ExpenseCategory::factory()->create()->id,
    ], $overrides));
}

it('moves an expense through submission and approval', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $expense = approvalExpense($company->id);

    $user = FilamentHelper::actingAsCompanyUser($company, [
        'view_logistics_expense',
        'update_logistics_expense',
        'approve_logistics_expense',
    ]);

    app(ExpenseApproval::class)->submit($expense);

    expect($expense->refresh()->state)->toBe(ExpenseState::SUBMITTED);

    app(ExpenseApproval::class)->approve($expense);

    expect($expense->refresh()->state)->toBe(ExpenseState::APPROVED)
        ->and($expense->approved_by_id)->toBe($user->id)
        ->and($expense->approved_at)->not->toBeNull();
});

it('moves a submitted expense to rejected', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $expense = approvalExpense($company->id, ['state' => ExpenseState::SUBMITTED]);

    FilamentHelper::actingAsCompanyUser($company, ['approve_logistics_expense']);

    app(ExpenseApproval::class)->reject($expense);

    expect($expense->refresh()->state)->toBe(ExpenseState::REJECTED)
        ->and($expense->approved_by_id)->toBeNull()
        ->and($expense->approved_at)->toBeNull();
});

it('does not let a submitter without approve permission approve their own expense', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $expense = approvalExpense($company->id);

    FilamentHelper::actingAsCompanyUser($company, ['update_logistics_expense']);

    app(ExpenseApproval::class)->submit($expense);

    expect(fn () => app(ExpenseApproval::class)->approve($expense))
        ->toThrow(AuthorizationException::class);
});

it('cannot approve an expense outside the active company scope', function () {
    $companyA = LogisticsHelper::enable(LogisticsHelper::company());
    $companyB = LogisticsHelper::enable(LogisticsHelper::company());
    $expense = approvalExpense($companyB->id, ['state' => ExpenseState::SUBMITTED]);

    FilamentHelper::actingAsCompanyUser($companyA, ['approve_logistics_expense']);

    expect(fn () => app(ExpenseApproval::class)->approve($expense))
        ->toThrow(ModelNotFoundException::class);
});

it('requires a receipt only for categories that demand one', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $shipment = LogisticsHelper::shipment($company);
    $required = ExpenseCategory::factory()->create(['requires_receipt' => true]);
    $optional = ExpenseCategory::factory()->create(['requires_receipt' => false]);

    FilamentHelper::actingAsCompanyUser($company, ['create_logistics_expense']);

    Livewire::test(CreateExpense::class)
        ->fillForm([
            'company_id'  => $company->id,
            'date'        => now()->toDateString(),
            'amount'      => 25,
            'category_id' => $required->id,
            'paid_by'     => ExpensePaidBy::COMPANY->value,
            'shipment_id' => $shipment->id,
        ], 'form')
        ->call('create')
        ->assertHasFormErrors(['receipt_path'], 'form');

    Livewire::test(CreateExpense::class)
        ->fillForm([
            'company_id'  => $company->id,
            'date'        => now()->toDateString(),
            'amount'      => 25,
            'category_id' => $optional->id,
            'paid_by'     => ExpensePaidBy::COMPANY->value,
            'shipment_id' => $shipment->id,
        ], 'form')
        ->call('create')
        ->assertHasNoFormErrors([], 'form');

    expect(FileUpload::make('receipt_path')->getAcceptedFileTypes())
        ->toContain('application/pdf', 'image/jpeg', 'image/png', 'image/webp');
});
