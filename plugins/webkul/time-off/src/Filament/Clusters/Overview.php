<?php

namespace Webkul\TimeOff\Filament\Clusters;


use Webkul\PluginManager\Support\PluginEntitlement;
use Filament\Clusters\Cluster;
use Filament\Panel;
use Webkul\Support\Enums\NavigationGroup;

class Overview extends Cluster
{
    protected static ?int $navigationSort = 2;

    public static function getSlug(?Panel $panel = null): string
    {
        return 'time-off/overview';
    }

    public static function getNavigationLabel(): string
    {
        return __('time-off::filament/clusters/overview.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::TimeOff;
    }
    public static function canAccess(): bool
    {
        return PluginEntitlement::enabledForCurrent('time-off');
    }
}
