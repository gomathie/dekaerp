<?php

namespace Webkul\Logistics\Filament\Clusters;

use Filament\Clusters\Cluster;
use Webkul\Support\Enums\NavigationGroup;

class Configurations extends Cluster
{
    protected static ?string $slug = 'logistics/configurations';

    protected static ?int $navigationSort = 5;

    public static function getNavigationLabel(): string
    {
        return __('logistics::filament/clusters/configurations.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Logistics;
    }
}
