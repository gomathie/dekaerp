<?php

namespace Webkul\Attendance\Filament\Clusters\Reporting\Pages;

use BackedEnum;
use Webkul\Attendance\Services\AttendanceReports;

class Lateness extends AttendanceReportPage
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static ?int $navigationSort = 1;

    protected static function pagePermission(): string
    {
        return 'page_attendance_lateness';
    }

    protected static function lang(): string
    {
        return 'attendances::reports.lateness';
    }

    public function columns(): array
    {
        return ['employee', 'date', 'check_in', 'late_minutes'];
    }

    public function rows(): array
    {
        [$from, $until] = $this->range();

        return app(AttendanceReports::class)->lateRows($from, $until, $this->visibleEmployeeIds());
    }
}
