<?php

namespace Webkul\Invoice\Filament\Clusters;

use Filament\Clusters\Cluster;

class PluginSettings extends Cluster
{
    protected static ?string $slug = 'invoice/settings';

    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return __('invoices::app.navigation.settings.label');
    }

    public static function getNavigationGroup(): string
    {
        return __('invoices::filament/clusters/configurations.navigation.group');
    }
}
