<?php

namespace Webkul\TimeOff\Filament\Clusters;


use Webkul\PluginManager\Support\PluginEntitlement;
use Filament\Clusters\Cluster;
use Filament\Panel;
use Webkul\Support\Enums\NavigationGroup;

class MyTime extends Cluster
{
    protected static ?int $navigationSort = 1;

    public static function getSlug(?Panel $panel = null): string
    {
        return 'time-off/dashboard';
    }

    public static function getNavigationLabel(): string
    {
        return __('time-off::filament/clusters/my-time.navigation.title');
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
