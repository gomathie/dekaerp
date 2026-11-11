<?php

use Webkul\Attendance\Models\CompanySetting;
use Webkul\Attendance\Support\AttendanceAccess;
use Webkul\Employee\Models\Employee;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/CompanyScopeHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/SecurityHelper.php';

class AttendanceHelper
{
    public static function install(): void
    {
        TestBootstrapHelper::ensurePluginInstalled('employees');
        TestBootstrapHelper::ensurePluginInstalled('attendances');

        AttendanceAccess::flush();
    }

    public static function company(array $overrides = []): Company
    {
        return CompanyHelper::company($overrides);
    }

    public static function employee(Company $company, array $overrides = []): Employee
    {
        return Employee::factory()->create([
            'company_id' => $company->id,
            ...$overrides,
        ]);
    }

    public static function enable(Company $company): Company
    {
        app(\Webkul\Attendance\Services\CompanyProvisioner::class)->enable($company);

        AttendanceAccess::flush();

        return $company;
    }
}
