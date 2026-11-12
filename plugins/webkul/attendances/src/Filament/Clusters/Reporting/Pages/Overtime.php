<?php

namespace Webkul\Attendance\Filament\Clusters\Reporting\Pages;

use BackedEnum;
use Webkul\Attendance\Services\AttendanceReports;

class Overtime extends AttendanceReportPage
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-trending-up';

    protected static ?int $navigationSort = 2;

    protected static function pagePermission(): string
    {
        return 'page_attendance_overtime';
    }

    protected static function lang(): string
    {
        return 'attendances::reports.overtime';
    }

    public function columns(): array
    {
        return ['employee', 'date', 'worked_minutes', 'expected_minutes', 'overtime_minutes'];
    }

    public function rows(): array
    {
        [$from, $until] = $this->range();

        return app(AttendanceReports::class)->overtimeRows($from, $until, $this->visibleEmployeeIds());
    }
}
