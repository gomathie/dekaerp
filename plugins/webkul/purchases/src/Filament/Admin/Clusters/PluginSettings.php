<?php

namespace Webkul\Purchase\Filament\Admin\Clusters;

use Filament\Clusters\Cluster;

class PluginSettings extends Cluster
{
    protected static ?string $slug = 'purchase/settings';

    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return __('purchases::app.navigation.settings.label');
    }

    public static function getNavigationGroup(): string
    {
        return __('purchases::filament/admin/clusters/configurations.navigation.group');
    }
}
