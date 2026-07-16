<?php

namespace Webkul\Inventory\Filament\Clusters;

use Filament\Clusters\Cluster;

class PluginSettings extends Cluster
{
    protected static ?string $slug = 'inventory/settings';

    protected static ?int $navigationSort = 5;

    public static function getNavigationLabel(): string
    {
        return __('inventories::app.navigation.settings.label');
    }

    public static function getNavigationGroup(): string
    {
        return __('inventories::filament/clusters/configurations.navigation.group');
    }
}
