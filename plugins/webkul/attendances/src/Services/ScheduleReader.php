<?php

namespace Webkul\Attendance\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Models\CompanySetting;
use Webkul\Employee\Models\Employee;
use Webkul\Support\Models\Calendar;
use Webkul\Support\Models\CalendarAttendance;

/**
 * Expected hours, lateness and overtime from the employee's working calendar.
 *
 * Computed when a report is read. Nothing here is stored on the attendance,
 * so a later change to the calendar does not rewrite yesterday's clock times.
 * Payroll freezes the numbers only when it consumes a period.
 */
class ScheduleReader
{
    /**
     * The employee's own time zone, then their working calendar's, then the
     * application's.
     *
     * The employee's own setting wins because it is what the employee records
     * themselves say; a calendar is shared by a whole team and only supplies a
     * sensible default. This is the one place the choice is made - clock times
     * are shown and reported through it, never through the server zone.
     */
    public function timezone(Employee $employee): string
    {
        $own = $employee->time_zone;

        if (is_string($own) && in_array($own, timezone_identifiers_list(), true)) {
            return $own;
        }

        $calendar = $this->calendar($employee);
        $timezone = $calendar?->timezone;

        if (is_string($timezone) && in_array($timezone, timezone_identifiers_list(), true)) {
            return $timezone;
        }

        return (string) config('app.timezone');
    }

    /**
     * Minutes late for one attendance, after the company's tolerance.
     */
    public function lateMinutes(Attendance $attendance): int
    {
        $employee = $this->employee($attendance);

        if ($employee === null) {
            return 0;
        }

        $local = $attendance->check_in_at->copy()->timezone($this->timezone($employee));
        $start = $this->earliestStartMinutes($employee, $local);

        if ($start === null) {
            return 0;
        }

        $actual = ($local->hour * 60) + $local->minute;
        $tolerance = CompanySetting::forCompany((int) $attendance->company_id)->late_tolerance_minutes;

        return max(0, $actual - $start - $tolerance);
    }

    /**
     * Minutes worked past the scheduled length, after the company's tolerance.
     * A day with no scheduled hours counts the whole worked time as overtime.
     */
    public function overtimeMinutes(Attendance $attendance): int
    {
        if ($attendance->worked_minutes === null) {
            return 0;
        }

        $employee = $this->employee($attendance);

        if ($employee === null) {
            return 0;
        }

        $local = $attendance->check_in_at->copy()->timezone($this->timezone($employee));
        $expected = $this->expectedMinutes($employee, $local);
        $tolerance = CompanySetting::forCompany((int) $attendance->company_id)->overtime_tolerance_minutes;

        return max(0, $attendance->worked_minutes - $expected - $tolerance);
    }

    public function expectedMinutes(Employee $employee, Carbon $localDay): int
    {
        return (int) $this->intervals($employee, $localDay)->sum(
            fn (CalendarAttendance $row): int => max(0, $this->minutes($row->hour_to) - $this->minutes($row->hour_from)),
        );
    }

    public function earliestStartMinutes(Employee $employee, Carbon $localDay): ?int
    {
        $intervals = $this->intervals($employee, $localDay);

        if ($intervals->isEmpty()) {
            return null;
        }

        return (int) $intervals->min(fn (CalendarAttendance $row): int => $this->minutes($row->hour_from));
    }

    /**
     * True when an approved time-off request covers this local calendar day.
     * Clocking in is still allowed; the attendance is only flagged.
     */
    public function onApprovedLeave(Employee $employee, Carbon $localDay): bool
    {
        if (! Schema::hasTable('time_off_leaves')) {
            return false;
        }

        $day = $localDay->toDateString();

        return DB::table('time_off_leaves')
            ->where('employee_id', $employee->id)
            ->whereIn('state', ['validate_one', 'validate_two', 'validate'])
            ->whereDate('request_date_from', '<=', $day)
            ->whereDate('request_date_to', '>=', $day)
            ->exists();
    }

    public function calendar(Employee $employee): ?Calendar
    {
        if (! $employee->calendar_id) {
            return null;
        }

        return Calendar::withoutGlobalScopes()->find($employee->calendar_id);
    }

    /**
     * @return Collection<int, CalendarAttendance>
     */
    protected function intervals(Employee $employee, Carbon $localDay): Collection
    {
        $calendar = $this->calendar($employee);

        if ($calendar === null) {
            return collect();
        }

        $weekday = strtolower($localDay->format('l'));

        return CalendarAttendance::query()
            ->where('calendar_id', $calendar->id)
            ->whereRaw('LOWER(day_of_week) = ?', [$weekday])
            ->whereNull('display_type')
            ->get();
    }

    protected function minutes(mixed $value): int
    {
        $text = (string) $value;

        if (str_contains($text, ':')) {
            [$hour, $minute] = array_pad(explode(':', $text), 2, '0');

            return ((int) $hour * 60) + (int) $minute;
        }

        return (int) round(((float) $text) * 60);
    }

    protected function employee(Attendance $attendance): ?Employee
    {
        return Employee::withoutGlobalScopes()->find($attendance->employee_id);
    }
}
