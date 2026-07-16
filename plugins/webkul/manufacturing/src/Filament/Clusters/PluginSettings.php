<?php

namespace Webkul\Manufacturing\Filament\Clusters;

use Filament\Clusters\Cluster;

class PluginSettings extends Cluster
{
    protected static ?string $slug = 'manufacturing/settings';

    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return __('manufacturing::app.navigation.settings.label');
    }

    public static function getNavigationGroup(): string
    {
        return __('manufacturing::filament/clusters/configurations.navigation.group');
    }
}
