<?php

namespace Webkul\Sale\Filament\Clusters;

use Filament\Clusters\Cluster;
use Webkul\Sale\Support\SalesAccess;
use Webkul\Support\Enums\NavigationGroup;

class PluginSettings extends Cluster
{
    public static function canAccess(): bool
    {
        return SalesAccess::enabledForCurrent();
    }

    protected static ?string $slug = 'sale/settings';

    protected static ?int $navigationSort = 5;

    public static function getNavigationLabel(): string
    {
        return __('sales::app.navigation.settings.label');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Sale;
    }
}
