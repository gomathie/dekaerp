<?php

namespace Webkul\Maintenance\Filament\Clusters;


use Webkul\PluginManager\Support\PluginEntitlement;
use Filament\Clusters\Cluster;
use Webkul\Support\Enums\NavigationGroup;

class Maintenance extends Cluster
{
    protected static ?string $slug = 'maintenance/maintenance';

    protected static ?int $navigationSort = -1;

    public static function getNavigationLabel(): string
    {
        return __('maintenance::filament/clusters/maintenance.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Maintenance;
    }
    public static function canAccess(): bool
    {
        return PluginEntitlement::enabledForCurrent('maintenance');
    }
}
