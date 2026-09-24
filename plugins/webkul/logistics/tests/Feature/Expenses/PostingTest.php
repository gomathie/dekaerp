<?php

use Illuminate\Auth\Access\AuthorizationException;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Move;
use Webkul\Employee\Models\Employee;
use Webkul\Logistics\Enums\ExpensePaidBy;
use Webkul\Logistics\Enums\ExpenseState;
use Webkul\Logistics\Exceptions\CannotPostBill;
use Webkul\Logistics\Models\CompanySetting;
use Webkul\Logistics\Models\Expense;
use Webkul\Logistics\Models\ExpenseCategory;
use Webkul\Logistics\Services\ExpensePoster;
use Webkul\Partner\Models\Partner;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

require_once __DIR__.'/../../Helpers/LogisticsHelper.php';

beforeEach(function () {
    LogisticsHelper::install();
});

/**
 * A Logistics company that can actually post a bill.
 *
 * Logistics never creates journals or accounts - they belong to Accounting, and
 * CompanyProvisioner::readiness() reports them as missing rather than inventing
 * them. This sets up the chart an onboarded company would already have, and
 * points the Logistics settings at it.
 */
function billingCompany(): Company
{
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    $currency = Currency::query()->find($company->currency_id) ?? Currency::query()->first();

    $journal = Journal::factory()->purchase()->create([
        'company_id'  => $company->id,
        'currency_id' => $currency?->id,
    ]);

    $account = Account::factory()->expense()->create(['currency_id' => $currency?->id]);

    CompanySetting::forCompany($company->id)->forceFill([
        'bill_journal_id'            => $journal->id,
        'default_expense_account_id' => $account->id,
    ])->save();

    return $company;
}

function postableExpense(Company $company, array $overrides = []): Expense
{
    return Expense::factory()->create(array_merge([
        'company_id'  => $company->id,
        'state'       => ExpenseState::APPROVED,
        'category_id' => ExpenseCategory::factory()->create()->id,
        'currency_id' => $company->currency_id,
        'paid_by'     => ExpensePaidBy::COMPANY,
        'amount'      => 100,
    ], $overrides));
}

function vendor(Company $company, string $name): Partner
{
    return Partner::factory()->create(['company_id' => $company->id, 'name' => $name]);
}

/**
 * An employee, without falling into the factories' recursion.
 *
 * Built directly rather than through EmployeeFactory, which cannot complete in
 * this repository for four separate pre-existing reasons:
 *
 *  - department_id defaults to Department::factory(), whose manager_id defaults
 *    back to Employee::factory(); job_id does the same through
 *    JobPositionFactory, twice. Each employee makes a department that makes an
 *    employee, until PHP's stack runs out.
 *  - work_location_id defaults to WorkLocation::factory(), which sets
 *    location_type to a random word while the model casts that column to the
 *    WorkLocation enum (home/office/other), so the model cannot be hydrated.
 *  - departure_reason_id defaults to DepartureReason::factory(), which inserts
 *    a `sequence` column that table does not have.
 *  - employee_properties called fake()->json, which is not a Faker format
 *    (fixed here, since the value was discarded anyway - no such column).
 *
 * Nulling them one at a time just finds the next one, and all of it is
 * irrelevant to billing: what matters is an employee with a contact. These are
 * bugs in the employees and recruitments plugins, reported rather than fixed
 * under WP-8b, since those factories belong to their own suites.
 */
function billableEmployee(Company $company, array $overrides = []): Employee
{
    return Employee::create(array_merge([
        'name'       => fake()->name(),
        'company_id' => $company->id,
    ], $overrides));
}

/**
 * The lines this package created, without accounting's own.
 *
 * `$bill->lines` is every move line, and computeAccountMove() adds a balancing
 * payable line plus any tax lines - accounting's business, not ours to count.
 * The expense account is what marks ours: it is the one thing the poster sets
 * on every line it writes and nothing else on the bill uses.
 */
function expenseLines(Move $bill, Company $company)
{
    return $bill->lines()
        ->where('account_id', CompanySetting::forCompany($company->id)->default_expense_account_id)
        ->get();
}

it('creates one draft bill per vendor, not one per expense', function () {
    $company = billingCompany();
    $shipment = LogisticsHelper::shipment($company);

    $carrier = vendor($company, 'Kumasi Haulage');
    $customs = vendor($company, 'Tema Customs Agent');

    postableExpense($company, ['shipment_id' => $shipment->id, 'payee_id' => $carrier->id, 'amount' => 400]);
    postableExpense($company, ['shipment_id' => $shipment->id, 'payee_id' => $carrier->id, 'amount' => 150]);
    postableExpense($company, ['shipment_id' => $shipment->id, 'payee_id' => $customs->id, 'amount' => 75]);

    CompanyHelper::actingAsCompanyUser($company, ['post_bill_logistics_expense']);

    $bills = app(ExpensePoster::class)->postForShipment($shipment);

    expect($bills)->toHaveCount(2);

    $carrierBill = $bills->firstWhere('partner_id', $carrier->id);

    expect($carrierBill->move_type)->toBe(MoveType::IN_INVOICE)
        // Never posted: D3 says that stays a finance decision.
        ->and($carrierBill->state)->toBe(MoveState::DRAFT)
        ->and(expenseLines($carrierBill, $company))->toHaveCount(2)
        ->and((float) $carrierBill->amount_total)->toBe(550.0)
        ->and(expenseLines($bills->firstWhere('partner_id', $customs->id), $company))->toHaveCount(1);
});

it('bills a reimbursement to the employee’s own contact', function () {
    $company = billingCompany();
    $shipment = LogisticsHelper::shipment($company);

    $contact = vendor($company, 'Kofi Boateng');
    $employee = billableEmployee($company, ['partner_id' => $contact->id]);

    // Paid out of the driver's own pocket, so it belongs in payables to them
    // until they are reimbursed - not to whoever sold the fuel.
    postableExpense($company, [
        'shipment_id' => $shipment->id,
        'paid_by'     => ExpensePaidBy::EMPLOYEE,
        'employee_id' => $employee->id,
        'payee_id'    => vendor($company, 'Shell Spintex')->id,
        'amount'      => 60,
    ]);

    CompanyHelper::actingAsCompanyUser($company, ['post_bill_logistics_expense']);

    $bill = app(ExpensePoster::class)->postForShipment($shipment)->sole();

    expect($bill->partner_id)->toBe($contact->id);
});

it('puts the amount on the company’s expense account', function () {
    $company = billingCompany();
    $shipment = LogisticsHelper::shipment($company);
    $expectedAccountId = CompanySetting::forCompany($company->id)->default_expense_account_id;

    postableExpense($company, [
        'shipment_id' => $shipment->id,
        'payee_id'    => vendor($company, 'Kumasi Haulage')->id,
        'amount'      => 250,
        'category_id' => ExpenseCategory::factory()->create(['name' => 'Tolls'])->id,
        'description' => 'Accra to Kumasi tolls',
    ]);

    CompanyHelper::actingAsCompanyUser($company, ['post_bill_logistics_expense']);

    $bill = app(ExpensePoster::class)->postForShipment($shipment)->sole();

    // Found by the name the poster writes, not by its account - asserting the
    // account on a line selected by account would prove nothing. $bill->lines
    // also holds accounting's own balancing line, which is why first() is not
    // good enough here.
    $line = $bill->lines()->where('name', 'Tolls - Accra to Kumasi tolls')->sole();

    // An expense carries no product, so without an explicit account the line
    // would have none and accounting would compute it silently wrong.
    expect($line->account_id)->toBe($expectedAccountId)
        ->and((float) $line->price_unit)->toBe(250.0)
        ->and((float) $bill->amount_total)->toBe(250.0);
});

it('marks the expenses billed and refuses to bill them twice', function () {
    $company = billingCompany();
    $shipment = LogisticsHelper::shipment($company);

    $expense = postableExpense($company, [
        'shipment_id' => $shipment->id,
        'payee_id'    => vendor($company, 'Kumasi Haulage')->id,
    ]);

    CompanyHelper::actingAsCompanyUser($company, ['post_bill_logistics_expense']);

    $bill = app(ExpensePoster::class)->postForShipment($shipment)->sole();

    expect($expense->refresh()->state)->toBe(ExpenseState::BILLED)
        ->and($expense->bill_move_id)->toBe($bill->id);

    // A second run has nothing left to bill rather than making another bill.
    expect(fn () => app(ExpensePoster::class)->postForShipment($shipment))
        ->toThrow(CannotPostBill::class)
        ->and(Move::withoutGlobalScopes()->where('move_type', MoveType::IN_INVOICE)->count())->toBe(1);
});

it('bills only approved expenses', function () {
    $company = billingCompany();
    $shipment = LogisticsHelper::shipment($company);
    $payee = vendor($company, 'Kumasi Haulage');

    postableExpense($company, ['shipment_id' => $shipment->id, 'payee_id' => $payee->id, 'state' => ExpenseState::DRAFT]);
    postableExpense($company, ['shipment_id' => $shipment->id, 'payee_id' => $payee->id, 'state' => ExpenseState::SUBMITTED]);

    CompanyHelper::actingAsCompanyUser($company, ['post_bill_logistics_expense']);

    // Unapproved costs are not costs the company has agreed to pay.
    expect(fn () => app(ExpensePoster::class)->postForShipment($shipment))
        ->toThrow(CannotPostBill::class);
});

it('refuses without the post_bill permission', function () {
    $company = billingCompany();
    $shipment = LogisticsHelper::shipment($company);

    postableExpense($company, [
        'shipment_id' => $shipment->id,
        'payee_id'    => vendor($company, 'Kumasi Haulage')->id,
    ]);

    CompanyHelper::actingAsCompanyUser($company, ['view_any_logistics_expense']);

    expect(fn () => app(ExpensePoster::class)->postForShipment($shipment))
        ->toThrow(AuthorizationException::class)
        ->and(Move::withoutGlobalScopes()->where('move_type', MoveType::IN_INVOICE)->count())->toBe(0);
});

it('refuses to bill an expense with no payee, creating nothing', function () {
    $company = billingCompany();
    $shipment = LogisticsHelper::shipment($company);

    postableExpense($company, ['shipment_id' => $shipment->id, 'payee_id' => vendor($company, 'Kumasi Haulage')->id]);
    postableExpense($company, ['shipment_id' => $shipment->id, 'payee_id' => null]);

    CompanyHelper::actingAsCompanyUser($company, ['post_bill_logistics_expense']);

    expect(fn () => app(ExpensePoster::class)->postForShipment($shipment))
        ->toThrow(CannotPostBill::class)
        // The partner is resolved before anything is written, so one bad
        // expense does not leave a half-made bill for the good one.
        ->and(Move::withoutGlobalScopes()->where('move_type', MoveType::IN_INVOICE)->count())->toBe(0);
});

it('refuses a reimbursement whose employee contact has gone', function () {
    $company = billingCompany();
    $shipment = LogisticsHelper::shipment($company);

    $employee = billableEmployee($company);

    // Employee::saved() creates a contact for anyone who lacks one, so an
    // employee with no contact at all cannot be made through the model - it
    // recurses until the stack runs out. The case that does happen is a
    // contact that was deleted afterwards, leaving the id pointing nowhere,
    // which would fail on the foreign key half way through posting. saveQuietly
    // to avoid re-triggering the hook that would just make another contact.
    Partner::withoutGlobalScopes()->whereKey($employee->partner_id)->forceDelete();


    postableExpense($company, [
        'shipment_id' => $shipment->id,
        'paid_by'     => ExpensePaidBy::EMPLOYEE,
        'employee_id' => $employee->id,
    ]);

    CompanyHelper::actingAsCompanyUser($company, ['post_bill_logistics_expense']);

    expect(fn () => app(ExpensePoster::class)->postForShipment($shipment))
        ->toThrow(CannotPostBill::class);
});

it('refuses when the company has no bill journal set', function () {
    $company = billingCompany();
    $shipment = LogisticsHelper::shipment($company);

    CompanySetting::forCompany($company->id)->forceFill(['bill_journal_id' => null])->save();

    postableExpense($company, [
        'shipment_id' => $shipment->id,
        'payee_id'    => vendor($company, 'Kumasi Haulage')->id,
    ]);

    CompanyHelper::actingAsCompanyUser($company, ['post_bill_logistics_expense']);

    expect(fn () => app(ExpensePoster::class)->postForShipment($shipment))
        ->toThrow(CannotPostBill::class);
});

it('never bills another company’s expenses', function () {
    $a = billingCompany();
    $b = billingCompany();

    $shipment = LogisticsHelper::shipment($b);

    postableExpense($b, [
        'shipment_id' => $shipment->id,
        'payee_id'    => vendor($b, 'Kumasi Haulage')->id,
    ]);

    CompanyHelper::actingAsCompanyUser($a, ['post_bill_logistics_expense']);

    // The company scope hides the shipment, so it cannot even be read.
    expect(fn () => app(ExpensePoster::class)->postForShipment($shipment))
        ->toThrow(Illuminate\Database\Eloquent\ModelNotFoundException::class)
        ->and(Move::withoutGlobalScopes()->where('move_type', MoveType::IN_INVOICE)->count())->toBe(0);
});
