<?php

namespace Webkul\Sale\Filament\Clusters;

use Filament\Clusters\Cluster;

class PluginSettings extends Cluster
{
    protected static ?string $slug = 'sale/settings';

    protected static ?int $navigationSort = 5;

    public static function getNavigationLabel(): string
    {
        return __('sales::app.navigation.settings.label');
    }

    public static function getNavigationGroup(): string
    {
        return __('sales::filament/clusters/configurations.navigation.group');
    }
}
