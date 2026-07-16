<?php

namespace Webkul\Accounting\Filament\Clusters;

use Filament\Clusters\Cluster;

class PluginSettings extends Cluster
{
    protected static ?string $slug = 'accounting/settings';

    protected static ?int $navigationSort = 7;

    public static function getNavigationLabel(): string
    {
        return __('accounting::app.navigation.settings.label');
    }

    public static function getNavigationGroup(): string
    {
        return __('accounting::filament/clusters/configurations.navigation.group');
    }
}
