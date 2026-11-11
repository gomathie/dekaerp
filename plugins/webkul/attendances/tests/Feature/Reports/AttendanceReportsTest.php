<?php

use Illuminate\Support\Carbon;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Models\CompanySetting;
use Webkul\Attendance\Services\AttendanceReports;
use Webkul\Support\Models\Calendar;
use Webkul\Support\Models\CalendarAttendance;

require_once __DIR__.'/../../Helpers/AttendanceHelper.php';

beforeEach(function (): void {
    AttendanceHelper::install();
});

/**
 * A Monday-to-Friday calendar working 08:00 to 17:00 in the given zone.
 */
function attendanceCalendar(string $timezone = 'Africa/Lusaka'): Calendar
{
    $calendar = Calendar::factory()->create(['timezone' => $timezone]);

    foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday'] as $day) {
        CalendarAttendance::factory()->create([
            'calendar_id' => $calendar->id,
            'day_of_week' => $day,
            'hour_from'   => '08:00',
            'hour_to'     => '17:00',
        ]);
    }

    return $calendar;
}

it('measures lateness in the schedule timezone, not the server one', function (): void {
    $company = AttendanceHelper::enable(AttendanceHelper::company());
    $calendar = attendanceCalendar();
    $employee = AttendanceHelper::employee($company, ['calendar_id' => $calendar->id]);

    Attendance::factory()->create([
        'company_id'     => $company->id,
        'employee_id'    => $employee->id,
        'check_in_at'    => Carbon::parse('2026-11-09 08:30:00', 'UTC'),
        'check_out_at'   => Carbon::parse('2026-11-09 15:00:00', 'UTC'),
        'worked_minutes' => 390,
    ]);

    $rows = app(AttendanceReports::class)->lateRows(
        Carbon::parse('2026-11-09'),
        Carbon::parse('2026-11-09'),
        [$employee->id],
    );

    // Africa/Lusaka is UTC+2: 08:30 UTC is 10:30 local against an 08:00 start.
    expect($rows)->toHaveCount(1)
        ->and($rows[0]['late_minutes'])->toBe(150);
});

it('counts someone still clocked in as late, and not as absent', function (): void {
    $company = AttendanceHelper::enable(AttendanceHelper::company());
    $calendar = attendanceCalendar();
    $employee = AttendanceHelper::employee($company, ['calendar_id' => $calendar->id]);

    Attendance::factory()->create([
        'company_id'   => $company->id,
        'employee_id'  => $employee->id,
        'check_in_at'  => Carbon::parse('2026-11-09 08:30:00', 'UTC'),
        'check_out_at' => null,
    ]);

    $reports = app(AttendanceReports::class);
    $from = Carbon::parse('2026-11-09');
    $to = Carbon::parse('2026-11-09');

    expect($reports->lateRows($from, $to, [$employee->id]))->toHaveCount(1)
        ->and($reports->absenceRows($from, $to, [$employee->id]))->toBe([])
        ->and($reports->overtimeRows($from, $to, [$employee->id]))->toBe([]);
});

it('reports no lateness when the arrival is inside the company tolerance', function (): void {
    $company = AttendanceHelper::enable(AttendanceHelper::company());

    CompanySetting::forCompany((int) $company->id)
        ->forceFill(['late_tolerance_minutes' => 60])
        ->save();

    $calendar = attendanceCalendar();
    $employee = AttendanceHelper::employee($company, ['calendar_id' => $calendar->id]);

    Attendance::factory()->create([
        'company_id'     => $company->id,
        'employee_id'    => $employee->id,
        'check_in_at'    => Carbon::parse('2026-11-09 06:30:00', 'UTC'),
        'check_out_at'   => Carbon::parse('2026-11-09 15:00:00', 'UTC'),
        'worked_minutes' => 510,
    ]);

    expect(app(AttendanceReports::class)->lateRows(
        Carbon::parse('2026-11-09'),
        Carbon::parse('2026-11-09'),
        [$employee->id],
    ))->toBe([]);
});

it('reports a scheduled working day with no attendance as an absence', function (): void {
    $company = AttendanceHelper::enable(AttendanceHelper::company());
    $calendar = attendanceCalendar();
    $employee = AttendanceHelper::employee($company, ['calendar_id' => $calendar->id]);

    // 2026-11-09 is a Monday, a scheduled working day.
    $rows = app(AttendanceReports::class)->absenceRows(
        Carbon::parse('2026-11-09'),
        Carbon::parse('2026-11-09'),
        [$employee->id],
    );

    expect($rows)->toHaveCount(1)
        ->and($rows[0]['employee'])->toBe($employee->name);
});

it('does not report a day with no scheduled hours as an absence', function (): void {
    $company = AttendanceHelper::enable(AttendanceHelper::company());
    $calendar = attendanceCalendar();
    $employee = AttendanceHelper::employee($company, ['calendar_id' => $calendar->id]);

    // 2026-11-07 is a Saturday: the calendar schedules nothing.
    $rows = app(AttendanceReports::class)->absenceRows(
        Carbon::parse('2026-11-07'),
        Carbon::parse('2026-11-07'),
        [$employee->id],
    );

    expect($rows)->toBe([]);
});

it('does not report overtime while the person works their scheduled length', function (): void {
    $company = AttendanceHelper::enable(AttendanceHelper::company());
    $calendar = attendanceCalendar();
    $employee = AttendanceHelper::employee($company, ['calendar_id' => $calendar->id]);

    Attendance::factory()->create([
        'company_id'     => $company->id,
        'employee_id'    => $employee->id,
        'check_in_at'    => Carbon::parse('2026-11-09 06:00:00', 'UTC'),
        'check_out_at'   => Carbon::parse('2026-11-09 15:00:00', 'UTC'),
        'worked_minutes' => 540,
    ]);

    expect(app(AttendanceReports::class)->overtimeRows(
        Carbon::parse('2026-11-09'),
        Carbon::parse('2026-11-09'),
        [$employee->id],
    ))->toBe([]);
});
