<?php

use Webkul\Employee\Models\Employee;
use Webkul\Partner\Models\Partner;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/CompanyHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('employees');
});

/**
 * 2026-11-11: saving an employee of another company than the one the actor
 * works in recursed until PHP's stack ran out. The saved hook looked up the
 * employee's contact through the company-scoped Partner query, missed it,
 * created a new contact, changed partner_id, saved again, and repeated. It was
 * the "maximum call stack" fatal that blocked the Attendance suite.
 */
it('creates and updates an employee of another company with exactly one contact', function (): void {
    $actingCompany = Company::factory()->create();
    $employeeCompany = Company::factory()->create();

    CompanyHelper::actingAsCompanyUser($actingCompany, []);

    $employee = Employee::factory()->create([
        'company_id' => $employeeCompany->id,
        'user_id'    => null,
    ]);

    $partnerId = $employee->partner_id;

    $employee->update(['name' => 'Renamed while acting elsewhere']);

    $contacts = Partner::withoutGlobalScopes()->where('sub_type', 'employee')->where('name', 'Renamed while acting elsewhere')->get();

    expect($partnerId)->not->toBeNull()
        ->and($employee->refresh()->partner_id)->toBe($partnerId)
        ->and($contacts)->toHaveCount(1)
        ->and($contacts->first()->id)->toBe($partnerId)
        ->and((int) $contacts->first()->company_id)->toBe((int) $employeeCompany->id);
});
