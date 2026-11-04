<?php

namespace Webkul\Inventory\Filament\Clusters;


use Webkul\PluginManager\Support\PluginEntitlement;
use Filament\Clusters\Cluster;
use Webkul\Support\Enums\NavigationGroup;

class Configurations extends Cluster
{
    protected static ?string $slug = 'inventory/configurations';

    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/configurations.navigation.title');
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
