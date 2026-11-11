<?php

use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Policies\AttendancePolicy;
use Webkul\Attendance\Services\AttendanceService;
use Webkul\Attendance\Services\CompanyProvisioner;

require_once __DIR__.'/../Helpers/AttendanceHelper.php';

/** Temporary probe for the stack fatal; delete after diagnosis. */
it('probes the correction path step by step', function (): void {
    $mark = fn (string $step) => fwrite(STDERR, "\nPROBE ".$step."\n");

    AttendanceHelper::install();
    $mark('1 companies');
    $activeCompany = AttendanceHelper::company();
    $recordCompany = AttendanceHelper::company();
    $mark('2 acting user');
    $actor = CompanyHelper::actingAsCompanyUser($activeCompany, ['correct_attendance_attendance']);
    $mark('3 actor employee');
    AttendanceHelper::employee($activeCompany, ['user_id' => $actor->id]);
    $mark('4 record employee');
    $employee = AttendanceHelper::employee($recordCompany);
    $mark('5 attendance');
    try {
        $attendance = Attendance::factory()->create(['company_id' => $recordCompany->id, 'employee_id' => $employee->id]);
    } catch (Throwable $e) {
        $frames = collect($e->getTrace())
            ->map(fn (array $f): string => ($f['class'] ?? '').($f['type'] ?? '').($f['function'] ?? '').' @'.basename($f['file'] ?? '?').':'.($f['line'] ?? '?'));
        $mark('5x '.$e::class.': '.substr($e->getMessage(), 0, 200));
        $mark("TOP FRAMES\n".$frames->take(40)->implode("\n"));
        $mark("MOST REPEATED\n".$frames->countBy()->sortDesc()->take(15)->map(fn ($n, $k) => $n.'  '.$k)->implode("\n"));

        throw $e;
    }
    $mark('6 enable');
    app(CompanyProvisioner::class)->enable($activeCompany);
    app(CompanyProvisioner::class)->enable($recordCompany);
    $mark('7 can');
    $actor->can('correct_attendance_attendance');
    $mark('8 policy');
    app(AttendancePolicy::class)->correct($actor, $attendance);
    $mark('9 service');

    try {
        app(AttendanceService::class)->correct($actor, $attendance, ['note' => 'x'], 'Wrong company');
    } catch (Throwable $e) {
        $mark('9b '.$e::class.': '.$e->getMessage());
    }

    $mark('10 done');
    expect(true)->toBeTrue();
});
