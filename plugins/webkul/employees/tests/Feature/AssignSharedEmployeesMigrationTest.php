<?php

use Illuminate\Support\Facades\DB;
use Webkul\Employee\Models\Employee;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensureERPInstalled();
    TestBootstrapHelper::ensurePluginInstalled('employees');
});

/**
 * A shared employee whose contact now belongs to a company would point, in
 * every other company, at a contact that company cannot read (2026-11-10).
 */
it('gives a shared employee its contact company', function () {
    $company = Company::factory()->create();
    $employee = Employee::factory()->create();

    DB::table('employees_employees')->where('id', $employee->getKey())->update(['company_id' => null]);
    DB::table('partners_partners')->where('id', $employee->partner_id)->update(['company_id' => $company->getKey()]);

    (require __DIR__.'/../../database/migrations/2026_11_10_000003_assign_shared_employees_to_their_contact_company.php')->up();

    expect(DB::table('employees_employees')->where('id', $employee->getKey())->value('company_id'))->toBe($company->getKey());
});
