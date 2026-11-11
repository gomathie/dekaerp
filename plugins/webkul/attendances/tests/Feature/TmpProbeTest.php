<?php

use Illuminate\Support\Carbon;
use Webkul\Attendance\Enums\CaptureLocation;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Models\CompanySetting;
use Webkul\Attendance\Services\AttendanceService;
use Webkul\Attendance\Services\CompanyProvisioner;
use Webkul\Attendance\Support\AttendanceAccess;
use Webkul\Employee\Models\Employee;
use Webkul\Security\Models\User;
use Webkul\Support\Services\CompanyContext;

require_once __DIR__.'/../Helpers/AttendanceHelper.php';

/**
 * Throwaway probe: replays the service-test scenarios in one process with a
 * marker between steps, so the step that exhausts the PHP stack is
 * identifiable. Delete after use.
 */
it('probes every service scenario in order', function (): void {
    $mark = function (string $label): void {
        fwrite(STDERR, "PROBE {$label}\n");
    };

    $try = function (string $label, callable $scenario) use ($mark): void {
        $mark("{$label} start");

        try {
            $scenario();
            $mark("{$label} done");
        } catch (Throwable $exception) {
            $mark("{$label} threw ".$exception::class.': '.$exception->getMessage());
        }
    };

    AttendanceHelper::install();
    $mark('installed');

    $try('disabled switch', function (): void {
        $company = AttendanceHelper::company();
        $user = CompanyHelper::actingAsCompanyUser($company);
        AttendanceHelper::employee($company, ['user_id' => $user->id]);

        app(AttendanceService::class)->clockIn($user);
    });

    $try('foreign employee id', function (): void {
        $company = AttendanceHelper::company();
        $user = CompanyHelper::actingAsCompanyUser($company);
        AttendanceHelper::employee($company, ['user_id' => $user->id]);
        $other = AttendanceHelper::employee($company, ['user_id' => User::factory()]);
        app(CompanyProvisioner::class)->enable($company);
        AttendanceAccess::flush();

        app(AttendanceService::class)->clockIn($user, ['employee_id' => $other->id]);
    });

    $try('empty forced context', function (): void {
        $company = AttendanceHelper::company();
        $user = CompanyHelper::actingAsCompanyUser($company);
        AttendanceHelper::employee($company, ['user_id' => $user->id]);
        app(CompanyProvisioner::class)->enable($company);
        AttendanceAccess::flush();
        app(CompanyContext::class)->forceActive(999999);

        app(AttendanceService::class)->clockIn($user);
    });

    $try('correction outside the active context', function (): void {
        $active = AttendanceHelper::company();
        $record = AttendanceHelper::company();
        $actor = CompanyHelper::actingAsCompanyUser($active, ['correct_attendance_attendance']);
        AttendanceHelper::employee($active, ['user_id' => $actor->id]);
        $target = AttendanceHelper::employee($record, ['user_id' => User::factory()]);
        $attendance = Attendance::factory()->create([
            'company_id'  => $record->id,
            'employee_id' => $target->id,
        ]);
        app(CompanyProvisioner::class)->enable($active);
        app(CompanyProvisioner::class)->enable($record);
        AttendanceAccess::flush();

        app(AttendanceService::class)->correct($actor, $attendance, ['note' => 'n'], 'Wrong company');
    });

    $try('location required', function (): void {
        $company = AttendanceHelper::company();
        $user = CompanyHelper::actingAsCompanyUser($company);
        AttendanceHelper::employee($company, ['user_id' => $user->id]);
        app(CompanyProvisioner::class)->enable($company)
            ->forceFill(['capture_location' => CaptureLocation::Required])->save();
        AttendanceAccess::flush();

        app(AttendanceService::class)->clockIn($user);
    });

    $try('location evidence while off', function (): void {
        $company = AttendanceHelper::company();
        $user = CompanyHelper::actingAsCompanyUser($company);
        AttendanceHelper::employee($company, ['user_id' => $user->id]);
        app(CompanyProvisioner::class)->enable($company);
        AttendanceAccess::flush();

        app(AttendanceService::class)->clockIn($user, [
            'latitude'  => -15.4,
            'longitude' => 28.3,
            'accuracy'  => 12,
            'ip'        => '203.0.113.8',
        ]);
    });

    $try('utc round trip', function (): void {
        $company = AttendanceHelper::company();
        $user = CompanyHelper::actingAsCompanyUser($company);
        AttendanceHelper::employee($company, ['user_id' => $user->id]);
        $person = Employee::withoutGlobalScopes()->where('user_id', $user->id)->firstOrFail();
        $person->forceFill(['time_zone' => 'Africa/Lusaka'])->saveQuietly();
        app(CompanyProvisioner::class)->enable($company);
        AttendanceAccess::flush();

        $service = app(AttendanceService::class);
        $service->clockIn($user);
        $service->clockOut($user);
    });

    $try('auto checkout past the limit', function (): void {
        $company = AttendanceHelper::company();
        $person = AttendanceHelper::employee($company, ['user_id' => User::factory()]);
        app(CompanyProvisioner::class)->enable($company)
            ->forceFill(['auto_checkout_after_hours' => 8])->save();
        Attendance::factory()->create([
            'company_id'  => $company->id,
            'employee_id' => $person->id,
            'check_in_at' => Carbon::parse('2026-11-11 00:00:00', 'UTC'),
        ]);

        app(AttendanceService::class)->autoCheckOut(Carbon::parse('2026-11-11 09:00:00', 'UTC'));
    });

    $try('auto checkout inside the limit', function (): void {
        $company = AttendanceHelper::company();
        $person = AttendanceHelper::employee($company, ['user_id' => User::factory()]);
        app(CompanyProvisioner::class)->enable($company)
            ->forceFill(['auto_checkout_after_hours' => 12])->save();
        Attendance::factory()->create([
            'company_id'  => $company->id,
            'employee_id' => $person->id,
            'check_in_at' => Carbon::parse('2026-11-11 00:00:00', 'UTC'),
        ]);

        app(AttendanceService::class)->autoCheckOut(Carbon::parse('2026-11-11 11:59:59', 'UTC'));
    });

    expect(CompanySetting::query()->count())->toBeGreaterThan(0);
});
