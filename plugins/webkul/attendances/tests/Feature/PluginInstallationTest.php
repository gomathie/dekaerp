<?php

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Models\CompanySetting;
use Webkul\Attendance\Services\CompanyProvisioner;
use Webkul\Attendance\Support\AttendanceAccess;
use Webkul\Employee\Models\Employee;
use Webkul\Support\Exceptions\CompanyMismatchException;

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

    // With no company given, the row takes its employee's, not the actor's.
    $attendance = Attendance::factory()->create(['employee_id' => $employee->id]);

    expect((int) $attendance->company_id)->toBe((int) $employeeCompany->id)
        ->and((int) $attendance->company_id)->not->toBe((int) $activeCompany->id);

    // A contradicting company is refused, never silently re-parented
    // (InheritsParentCompany; AGENTS.md protected decision). Corrected
    // 2026-11-11: this test used to expect the row to be moved quietly.
    $attendance->update(['check_out_at' => now()]);

    expect(fn () => Attendance::factory()->create([
        'employee_id' => $employee->id,
        'company_id'  => $activeCompany->id,
    ]))->toThrow(CompanyMismatchException::class);
});

it('enforces the single-open database index even when model scopes are bypassed', function (): void {
    $company = AttendanceHelper::company();
    $employee = AttendanceHelper::employee($company);

    Attendance::factory()->create([
        'employee_id' => $employee->id,
        'company_id'  => $company->id,
    ]);

    expect(fn () => DB::table('attendances_attendances')->insert([
        'company_id'  => $company->id,
        'employee_id' => $employee->id,
        'check_in_at' => now()->toDateTimeString(),
        'source_in'   => 'banner',
        'created_at'  => now()->toDateTimeString(),
        'updated_at'  => now()->toDateTimeString(),
    ]))->toThrow(QueryException::class);
});

/**
 * Sentry DEKAERP-14 (2026-11-12): the scheduler ran attendances:auto-check-out
 * every 15 minutes, but the provider never registered the command, so it
 * failed with "There are no commands defined in the attendances namespace" and
 * forgotten check-outs were never closed.
 */
it('registers the auto check-out command the scheduler runs', function (): void {
    expect(array_key_exists('attendances:auto-check-out', Artisan::all()))->toBeTrue();

    $this->artisan('attendances:auto-check-out')->assertSuccessful();
});
