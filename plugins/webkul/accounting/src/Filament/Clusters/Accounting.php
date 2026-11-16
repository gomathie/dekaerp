<?php

namespace Webkul\Accounting\Filament\Clusters;


use Webkul\PluginManager\Support\PluginEntitlement;
use Filament\Clusters\Cluster;
use Webkul\Support\Enums\NavigationGroup;

class Accounting extends Cluster
{
    protected static ?string $slug = 'accounting/accounting';

    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return __('accounting::filament/clusters/accounting.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Accounting;
    }
    public static function canAccess(): bool
    {
        return PluginEntitlement::enabledForCurrent('accounting');
    }
}
