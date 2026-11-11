<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Models\CompanySetting;
use Webkul\Attendance\Services\CompanyProvisioner;
use Webkul\Attendance\Support\AttendanceAccess;
use Webkul\Employee\Models\Employee;

require_once __DIR__.'/../Helpers/AttendanceHelper.php';

beforeEach(function (): void {
    AttendanceHelper::install();
});

it('installs the attendance tables after its employee dependency', function (): void {
    expect(Schema::hasTable('employees_employees'))->toBeTrue()
        ->and(Schema::hasTable('attendances_company_settings'))->toBeTrue()
        ->and(Schema::hasTable('attendances_attendances'))->toBeTrue()
        ->and(Schema::hasTable('attendances_corrections'))->toBeTrue();
});

it('provisions each new company with attendance disabled by default', function (): void {
    $company = AttendanceHelper::company();

    $setting = CompanySetting::forCompany((int) $company->id);

    expect($setting->exists)->toBeTrue()
        ->and($setting->is_enabled)->toBeFalse()
        ->and(AttendanceAccess::enabledFor((int) $company->id))->toBeFalse();
});

it('keeps an enabled company enabled when its settings are provisioned again', function (): void {
    $company = AttendanceHelper::company();
    $provisioner = app(CompanyProvisioner::class);

    $provisioner->enable($company);
    $setting = $provisioner->provision($company);

    expect($setting->is_enabled)->toBeTrue()
        ->and(AttendanceAccess::enabledFor((int) $company->id))->toBeTrue();
});

it('derives attendance ownership from its employee rather than the active company', function (): void {
    $employeeCompany = AttendanceHelper::company();
    $activeCompany = AttendanceHelper::company();

    CompanyHelper::actingAsCompanyUser($activeCompany);

    $employee = AttendanceHelper::employee($employeeCompany);
    $attendance = Attendance::factory()->create([
        'employee_id' => $employee->id,
        'company_id' => $activeCompany->id,
    ]);

    expect((int) $attendance->company_id)->toBe((int) $employeeCompany->id)
        ->and((int) $attendance->company_id)->not->toBe((int) $activeCompany->id);
});

it('enforces the single-open database index even when model scopes are bypassed', function (): void {
    $company = AttendanceHelper::company();
    $employee = AttendanceHelper::employee($company);

    Attendance::factory()->create([
        'employee_id' => $employee->id,
        'company_id' => $company->id,
    ]);

    expect(fn () => DB::table('attendances_attendances')->insert([
        'company_id' => $company->id,
        'employee_id' => $employee->id,
        'check_in_at' => now()->toDateTimeString(),
        'source_in' => 'banner',
        'created_at' => now()->toDateTimeString(),
        'updated_at' => now()->toDateTimeString(),
    ]))->toThrow(\Illuminate\Database\QueryException::class);
});
