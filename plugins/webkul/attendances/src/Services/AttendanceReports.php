<?php

namespace Webkul\Attendance\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Models\CompanySetting;
use Webkul\Employee\Models\Employee;

/**
 * Lateness, absence and overtime for reporting (AT-6).
 *
 * Everything here is computed at read time from the employee's working
 * calendar, so a later calendar change never rewrites what a report said about
 * a past day. Nothing is stored and nothing is paid: payroll reads this only
 * when it freezes a period.
 */
class AttendanceReports
{
    /**
     * A report over more than a quarter would be an accident, not a request.
     */
    public const MAX_DAYS = 92;

    public function __construct(private ScheduleReader $schedule) {}

    /**
     * @param  array<int, int>  $employeeIds
     * @return array<int, array{employee: string, date: string, check_in: ?string, late_minutes: int}>
     */
    public function lateRows(Carbon $from, Carbon $to, array $employeeIds): array
    {
        return $this->attendances($from, $to, $employeeIds)
            ->map(function (Attendance $attendance): ?array {
                $minutes = $this->schedule->lateMinutes($attendance);

                if ($minutes <= 0) {
                    return null;
                }

                $employee = $this->employee($attendance);
                $local = $this->localCheckIn($attendance, $employee);

                return [
                    'employee'     => (string) $employee?->name,
                    'date'         => $local?->toDateString() ?? '',
                    'check_in'     => $local?->format('H:i'),
                    'late_minutes' => $minutes,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @param  array<int, int>  $employeeIds
     * @return array<int, array{employee: string, date: string, worked_minutes: int, expected_minutes: int, overtime_minutes: int}>
     */
    public function overtimeRows(Carbon $from, Carbon $to, array $employeeIds): array
    {
        return $this->attendances($from, $to, $employeeIds)
            ->map(function (Attendance $attendance): ?array {
                $minutes = $this->schedule->overtimeMinutes($attendance);

                if ($minutes <= 0) {
                    return null;
                }

                $employee = $this->employee($attendance);

                if ($employee === null) {
                    return null;
                }

                $local = $this->localCheckIn($attendance, $employee);

                return [
                    'employee'         => (string) $employee->name,
                    'date'             => $local?->toDateString() ?? '',
                    'worked_minutes'   => (int) $attendance->worked_minutes,
                    'expected_minutes' => $local ? $this->schedule->expectedMinutes($employee, $local) : 0,
                    'overtime_minutes' => $minutes,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Days the employee was scheduled to work and did not attend.
     *
     * An approved leave day is not an absence: it is shown in the banner and
     * must not be reported as an unpaid no-show.
     *
     * @param  array<int, int>  $employeeIds
     * @return array<int, array{employee: string, date: string, expected_minutes: int}>
     */
    public function absenceRows(Carbon $from, Carbon $to, array $employeeIds): array
    {
        $employees = Employee::withoutGlobalScopes()
            ->whereNotNull('calendar_id')
            ->whereIn('id', $employeeIds)
            ->get();

        $rows = [];

        foreach ($employees as $employee) {
            $timezone = $this->schedule->timezone($employee);

            $attended = $this->attendances($from, $to, [$employee->id])
                ->map(fn (Attendance $attendance): string => (string) $this->localCheckIn($attendance, $employee)?->toDateString())
                ->all();

            for ($day = $from->copy(); $day->lte($to); $day->addDay()) {
                $localDay = $day->copy()->timezone($timezone);
                $expected = $this->schedule->expectedMinutes($employee, $localDay);

                if ($expected <= 0 || in_array($localDay->toDateString(), $attended, true)) {
                    continue;
                }

                if ($this->schedule->onApprovedLeave($employee, $localDay)) {
                    continue;
                }

                $rows[] = [
                    'employee'         => (string) $employee->name,
                    'date'             => $localDay->toDateString(),
                    'expected_minutes' => $expected,
                ];
            }
        }

        return $rows;
    }

    /**
     * The company's tolerances, for the report header so the numbers are readable.
     *
     * @return array{late: int, overtime: int}
     */
    public function tolerances(int $companyId): array
    {
        $settings = CompanySetting::forCompany($companyId);

        return [
            'late'     => (int) $settings->late_tolerance_minutes,
            'overtime' => (int) $settings->overtime_tolerance_minutes,
        ];
    }

    /**
     * The employee loaded scope-free: a report must read the same person the
     * schedule reader does, whoever is acting.
     */
    protected function employee(Attendance $attendance): ?Employee
    {
        return Employee::withoutGlobalScopes()->find($attendance->employee_id);
    }

    /**
     * Check-in in the employee's own calendar timezone.
     *
     * `Attendance::checkInLocal()` reads the employee through a company-scoped
     * relation, which a report run by someone outside that company does not
     * see, and it would then fall back to the server zone and could report the
     * wrong day. The schedule reader already resolved the right zone.
     */
    protected function localCheckIn(Attendance $attendance, ?Employee $employee): ?Carbon
    {
        if ($attendance->check_in_at === null) {
            return null;
        }

        if ($employee === null) {
            return $attendance->checkInLocal();
        }

        return $attendance->check_in_at->copy()->timezone($this->schedule->timezone($employee));
    }

    /**
     * Attendance rows in the range for the given employees.
     *
     * The caller passes the ids it may see (already company-scoped), so this
     * read drops the company scope instead of guessing one. Open rows are
     * included: someone who checked in late and has not left yet is late, and
     * someone still clocked in has not been absent. Overtime needs
     * `worked_minutes`, so an open row simply reports none.
     *
     * @param  array<int, int>  $employeeIds
     * @return Collection<int, Attendance>
     */
    protected function attendances(Carbon $from, Carbon $to, array $employeeIds)
    {
        return Attendance::withoutGlobalScopes()
            ->whereIn('employee_id', $employeeIds)
            ->whereDate('check_in_at', '>=', $from->toDateString())
            ->whereDate('check_in_at', '<=', $to->toDateString())
            ->orderBy('check_in_at')
            ->get();
    }
}
