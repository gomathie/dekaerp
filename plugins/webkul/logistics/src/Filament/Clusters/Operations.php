<?php

namespace Webkul\Logistics\Filament\Clusters;

use Filament\Clusters\Cluster;
use Webkul\Logistics\Support\LogisticsAccess;
use Webkul\Support\Enums\NavigationGroup;

class Operations extends Cluster
{
    protected static ?string $slug = 'logistics/operations';

    protected static ?int $navigationSort = 1;

    /**
     * Hidden for companies that haven't switched Logistics on.
     */
    public static function canAccess(): bool
    {
        return LogisticsAccess::enabledForCurrent();
    }

    public static function getNavigationLabel(): string
    {
        return __('logistics::filament/clusters/operations.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Logistics;
    }
}
