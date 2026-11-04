<?php

namespace Webkul\Invoice\Filament\Clusters;


use Webkul\PluginManager\Support\PluginEntitlement;
use Filament\Clusters\Cluster;
use Webkul\Support\Enums\NavigationGroup;

class PluginSettings extends Cluster
{
    protected static ?string $slug = 'invoice/settings';

    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return __('invoices::app.navigation.settings.label');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Invoice;
    }
    public static function canAccess(): bool
    {
        return PluginEntitlement::enabledForCurrent('invoices');
    }
}
