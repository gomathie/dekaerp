<?php

namespace Webkul\Maintenance\Filament\Clusters;

use Filament\Clusters\Cluster;

class Maintenance extends Cluster
{
    protected static ?string $slug = 'maintenance';

    protected static ?int $navigationSort = -1;

    public static function getNavigationLabel(): string
    {
        return __('maintenance::filament/clusters/maintenance.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('maintenance::filament/clusters/maintenance.navigation.group');
    }
}
