<?php

namespace Webkul\Sale\Filament\Clusters;

use Filament\Clusters\Cluster;
use Webkul\Sale\Support\SalesAccess;
use Webkul\Support\Enums\NavigationGroup;

class Configuration extends Cluster
{
    public static function canAccess(): bool
    {
        return SalesAccess::enabledForCurrent();
    }

    protected static ?string $slug = 'sale/configurations';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/configurations.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Sale;
    }
}
