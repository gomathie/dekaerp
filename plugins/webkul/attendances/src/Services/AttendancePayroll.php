<?php

namespace Webkul\Attendance\Services;

use Illuminate\Support\Carbon;
use Webkul\Attendance\Models\Attendance;
use Webkul\Employee\Models\Employee;

/**
 * What payroll reads. Attendance never writes a payroll table.
 *
 * @phpstan-type Units array{worked_minutes: int, overtime_minutes: int}
 */
class AttendancePayroll
{
    public function __construct(private ScheduleReader $schedule) {}

    /**
     * @return array{worked_minutes: int, overtime_minutes: int}
     */
    public function unitsFor(Employee $employee, Carbon $from, Carbon $to): array
    {
        $rows = Attendance::withoutGlobalScopes()
            ->where('employee_id', $employee->id)
            ->where('company_id', $employee->company_id)
            ->whereNotNull('check_out_at')
            ->where('check_in_at', '>=', $from)
            ->where('check_in_at', '<', $to)
            ->get();

        return [
            'worked_minutes'   => (int) $rows->sum('worked_minutes'),
            'overtime_minutes' => (int) $rows->sum(fn (Attendance $row): int => $this->schedule->overtimeMinutes($row)),
        ];
    }
}
