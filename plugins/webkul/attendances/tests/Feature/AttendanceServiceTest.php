<?php

use Illuminate\Support\Carbon;
use Webkul\Attendance\Enums\CaptureLocation;
use Webkul\Attendance\Enums\Source;
use Webkul\Attendance\Exceptions\AttendanceNotEnabledException;
use Webkul\Attendance\Exceptions\CorrectionRefusedException;
use Webkul\Attendance\Exceptions\LocationRequiredException;
use Webkul\Attendance\Exceptions\NotYourAttendanceException;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Policies\AttendancePolicy;
use Webkul\Attendance\Services\AttendanceService;
use Webkul\Attendance\Services\CompanyProvisioner;
use Webkul\Employee\Models\Employee;
use Webkul\Security\Models\User;
use Webkul\Support\Services\CompanyContext;

require_once __DIR__.'/../Helpers/AttendanceHelper.php';

beforeEach(function (): void {
    AttendanceHelper::install();
});

function attendanceEmployeeForCompany($company, array $permissions = []): array
{
    $user = CompanyHelper::actingAsCompanyUser($company, $permissions);
    $employee = AttendanceHelper::employee($company, ['user_id' => $user->id]);

    return [$user, $employee];
}

it('returns the open attendance when the user submits clock-in twice', function (): void {
    $company = AttendanceHelper::company();
    [$user] = attendanceEmployeeForCompany($company);
    app(CompanyProvisioner::class)->enable($company);

    $service = app(AttendanceService::class);
    $first = $service->clockIn($user);
    $second = $service->clockIn($user);

    expect($second->id)->toBe($first->id)
        ->and(Attendance::query()->count())->toBe(1);
});

it('refuses to clock in when attendance is disabled for the employee company', function (): void {
    $company = AttendanceHelper::company();
    [$user] = attendanceEmployeeForCompany($company);

    expect(fn () => app(AttendanceService::class)->clockIn($user))
        ->toThrow(AttendanceNotEnabledException::class, "Attendance is not enabled for company {$company->id}.");
});

it('refuses an employee id supplied for another person from the banner', function (): void {
    $company = AttendanceHelper::company();
    [$user] = attendanceEmployeeForCompany($company);
    $other = Employee::factory()->create([
        'company_id' => $company->id,
        'user_id'    => User::factory(),
    ]);
    app(CompanyProvisioner::class)->enable($company);

    expect(fn () => app(AttendanceService::class)->clockIn($user, ['employee_id' => $other->id]))
        ->toThrow(NotYourAttendanceException::class, NotYourAttendanceException::MESSAGE);
});

it('does not fall back to the default company inside an empty forced context', function (): void {
    $company = AttendanceHelper::company();
    [$user] = attendanceEmployeeForCompany($company);
    app(CompanyProvisioner::class)->enable($company);
    app(CompanyContext::class)->forceActive(999999);

    expect(fn () => app(AttendanceService::class)->clockIn($user))
        ->toThrow(CorrectionRefusedException::class, CorrectionRefusedException::NO_EMPLOYEE);
});

it('refuses a correction when the target company is outside the active context', function (): void {
    $activeCompany = AttendanceHelper::company();
    $recordCompany = AttendanceHelper::company();
    [$actor] = attendanceEmployeeForCompany($activeCompany, ['correct_attendance_attendance']);
    $employee = AttendanceHelper::employee($recordCompany);
    $attendance = Attendance::factory()->create([
        'company_id'  => $recordCompany->id,
        'employee_id' => $employee->id,
    ]);
    app(CompanyProvisioner::class)->enable($activeCompany);
    app(CompanyProvisioner::class)->enable($recordCompany);

    expect(app(AttendancePolicy::class)->correct($actor, $attendance))
        ->toBeFalse();

    expect(fn () => app(AttendanceService::class)->correct($actor, $attendance, [
        'note' => 'Corrected by an out-of-company actor.',
    ], 'Wrong company'))
        ->toThrow(CorrectionRefusedException::class, CorrectionRefusedException::NOT_ALLOWED);
});

it('refuses to clock in without a location when the company requires it', function (): void {
    $company = AttendanceHelper::company();
    [$user] = attendanceEmployeeForCompany($company);
    $setting = app(CompanyProvisioner::class)->enable($company);
    $setting->forceFill(['capture_location' => CaptureLocation::Required])->save();

    expect(fn () => app(AttendanceService::class)->clockIn($user))
        ->toThrow(LocationRequiredException::class, LocationRequiredException::MESSAGE);
});

it('does not retain submitted location evidence while capture is off', function (): void {
    $company = AttendanceHelper::company();
    [$user] = attendanceEmployeeForCompany($company);
    app(CompanyProvisioner::class)->enable($company);

    $attendance = app(AttendanceService::class)->clockIn($user, [
        'latitude'  => -15.4,
        'longitude' => 28.3,
        'accuracy'  => 12,
        'ip'        => '203.0.113.8',
    ]);

    expect($attendance->latitude_in)->toBeNull()
        ->and($attendance->longitude_in)->toBeNull()
        ->and($attendance->accuracy_in)->toBeNull()
        ->and($attendance->ip_in)->toBeNull();
});

it('stores open and closed times in UTC and records the worked minutes on check-out', function (): void {
    $company = AttendanceHelper::company();
    [$user] = attendanceEmployeeForCompany($company);
    $employee = Employee::withoutGlobalScopes()->where('user_id', $user->id)->firstOrFail();
    $employee->forceFill(['time_zone' => 'Africa/Lusaka'])->saveQuietly();
    app(CompanyProvisioner::class)->enable($company);

    config(['app.timezone' => 'Africa/Accra']);
    $this->travelTo(Carbon::parse('2026-11-11 08:00:00', 'UTC'));

    $service = app(AttendanceService::class);
    $attendance = $service->clockIn($user);

    $this->travelTo(Carbon::parse('2026-11-11 12:15:00', 'UTC'));
    $closed = $service->clockOut($user);

    expect($attendance->check_in_at->copy()->utc()->format('H:i'))->toBe('08:00')
        ->and($closed->check_out_at->copy()->utc()->format('H:i'))->toBe('12:15')
        ->and($closed->worked_minutes)->toBe(255)
        ->and($closed->source_out)->toBe(Source::Banner)
        ->and($closed->checkInLocal()->format('H:i'))->toBe('10:00');
});

/**
 * 2026-11-11: a clock-out in the same second as the clock-in was later in
 * memory (microseconds) but equal once stored, and the database check
 * attendances_check_out_after_check_in refused it.
 */
it('closes an attendance clocked out within the same second as the clock-in', function (): void {
    $company = AttendanceHelper::company();
    [$user] = attendanceEmployeeForCompany($company);
    app(CompanyProvisioner::class)->enable($company);

    $this->travelTo(Carbon::parse('2026-11-11 09:00:00.250000', 'UTC'));
    $service = app(AttendanceService::class);
    $service->clockIn($user);

    $this->travelTo(Carbon::parse('2026-11-11 09:00:00.900000', 'UTC'));
    $closed = $service->clockOut($user);

    expect($closed->check_out_at->copy()->utc()->format('H:i:s'))->toBe('09:00:01')
        ->and($closed->check_out_at->gt($closed->check_in_at))->toBeTrue();
});

it('closes overdue rows at each company limit and flags them for review', function (): void {
    $company = AttendanceHelper::company();
    $employee = AttendanceHelper::employee($company);
    $setting = app(CompanyProvisioner::class)->enable($company);
    $setting->forceFill(['auto_checkout_after_hours' => 8])->save();
    $startedAt = Carbon::parse('2026-11-11 00:00:00', 'UTC');
    $attendance = Attendance::factory()->create([
        'company_id'  => $company->id,
        'employee_id' => $employee->id,
        'check_in_at' => $startedAt,
    ]);

    expect(app(AttendanceService::class)->autoCheckOut($startedAt->copy()->addHours(9)))->toBe(1);

    $attendance->refresh();

    expect($attendance->check_out_at->copy()->utc()->equalTo($startedAt->copy()->addHours(8)))->toBeTrue()
        ->and($attendance->worked_minutes)->toBe(480)
        ->and($attendance->source_out)->toBe(Source::System)
        ->and($attendance->needs_review)->toBeTrue();
});

it('does not auto-check-out an attendance that is still within its company limit', function (): void {
    $company = AttendanceHelper::company();
    $employee = AttendanceHelper::employee($company);
    $setting = app(CompanyProvisioner::class)->enable($company);
    $setting->forceFill(['auto_checkout_after_hours' => 12])->save();
    $attendance = Attendance::factory()->create([
        'company_id'  => $company->id,
        'employee_id' => $employee->id,
        'check_in_at' => Carbon::parse('2026-11-11 00:00:00', 'UTC'),
    ]);

    expect(app(AttendanceService::class)->autoCheckOut(Carbon::parse('2026-11-11 11:59:59', 'UTC')))->toBe(0)
        ->and($attendance->refresh()->check_out_at)->toBeNull();
});
