<?php

namespace Webkul\Attendance\Filament\Resources\AttendanceResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Webkul\Attendance\Filament\Resources\AttendanceResource;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    public function getTitle(): string
    {
        return __('attendances::filament/resources/attendance.navigation.title');
    }
}
