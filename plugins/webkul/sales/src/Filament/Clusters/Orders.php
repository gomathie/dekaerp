<?php

namespace Webkul\Sale\Filament\Clusters;

use Filament\Clusters\Cluster;
use Webkul\Sale\Support\SalesAccess;
use Webkul\Support\Enums\NavigationGroup;

class Orders extends Cluster
{
    public static function canAccess(): bool
    {
        return SalesAccess::enabledForCurrent();
    }

    protected static ?string $slug = 'sale/orders';

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/orders.navigation.title');
    }

    public static function getClusterBreadcrumb(): ?string
    {
        return __('sales::filament/clusters/orders.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Sale;
    }
}
