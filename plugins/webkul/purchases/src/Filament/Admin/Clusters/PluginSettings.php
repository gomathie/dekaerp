<?php

namespace Webkul\Purchase\Filament\Admin\Clusters;

use Filament\Clusters\Cluster;
use Webkul\Purchase\Support\PurchasesAccess;
use Webkul\Support\Enums\NavigationGroup;

class PluginSettings extends Cluster
{
    public static function canAccess(): bool
    {
        return PurchasesAccess::enabledForCurrent();
    }

    protected static ?string $slug = 'purchase/settings';

    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return __('purchases::app.navigation.settings.label');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Purchase;
    }
}
