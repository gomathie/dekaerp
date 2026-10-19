<?php

namespace Webkul\Logistics\Filament\Clusters;

use Filament\Clusters\Cluster;
use Webkul\Logistics\Support\LogisticsAccess;
use Webkul\Support\Enums\NavigationGroup;

class Finance extends Cluster
{
    protected static ?string $slug = 'logistics/finance';

    protected static ?int $navigationSort = 3;

    /**
     * Hidden for companies that haven't switched Logistics on.
     */
    public static function canAccess(): bool
    {
        return LogisticsAccess::enabledForCurrent();
    }

    public static function getNavigationLabel(): string
    {
        return __('logistics::filament/clusters/finance.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Logistics;
    }
}
