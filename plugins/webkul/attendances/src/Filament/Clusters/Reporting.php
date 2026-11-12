<?php

namespace Webkul\Attendance\Filament\Clusters;

use Filament\Clusters\Cluster;
use Webkul\Attendance\Support\AttendanceAccess;
use Webkul\Support\Enums\NavigationGroup;

class Reporting extends Cluster
{
    protected static ?string $slug = 'attendances/reporting';

    protected static ?int $navigationSort = 2;

    /**
     * Hidden for companies that have not switched attendance on.
     */
    public static function canAccess(): bool
    {
        return AttendanceAccess::enabledForCurrent();
    }

    public static function getNavigationLabel(): string
    {
        return __('attendances::filament/clusters/reporting.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Employee;
    }
}
