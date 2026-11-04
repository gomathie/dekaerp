<?php

namespace Webkul\Inventory\Filament\Clusters;


use Webkul\PluginManager\Support\PluginEntitlement;
use Filament\Clusters\Cluster;
use Webkul\Support\Enums\NavigationGroup;

class Products extends Cluster
{
    protected static ?string $slug = 'inventory/products';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/products.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Inventory;
    }
    public static function canAccess(): bool
    {
        return PluginEntitlement::enabledForCurrent('inventories');
    }
}
