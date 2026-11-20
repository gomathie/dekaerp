<?php

namespace Webkul\Logistics\Filament\Clusters;

use Filament\Clusters\Cluster;
use Webkul\Logistics\Support\LogisticsAccess;
use Webkul\Support\Enums\NavigationGroup;

class Reporting extends Cluster
{
    protected static ?string $slug = 'logistics/reporting';

    protected static ?int $navigationSort = 4;

    /**
     * Hidden for companies that haven't switched Logistics on.
     */
    public static function canAccess(): bool
    {
        return LogisticsAccess::enabledForCurrent();
    }

    public static function getNavigationLabel(): string
    {
        return __('logistics::filament/clusters/reporting.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Logistics;
    }
}
