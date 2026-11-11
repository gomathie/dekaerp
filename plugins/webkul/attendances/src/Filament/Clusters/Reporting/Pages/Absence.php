<?php

namespace Webkul\Attendance\Filament\Clusters\Reporting\Pages;

use BackedEnum;
use Webkul\Attendance\Services\AttendanceReports;

class Absence extends AttendanceReportPage
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-minus';

    protected static ?int $navigationSort = 3;

    protected static function pagePermission(): string
    {
        return 'page_attendance_absence';
    }

    protected static function lang(): string
    {
        return 'attendances::reports.absence';
    }

    public function columns(): array
    {
        return ['employee', 'date', 'expected_minutes'];
    }

    public function rows(): array
    {
        [$from, $until] = $this->range();

        return app(AttendanceReports::class)->absenceRows($from, $until, $this->visibleEmployeeIds());
    }
}
