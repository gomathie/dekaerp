<?php

namespace Webkul\Invoice\Filament\Clusters;

use Filament\Clusters\Cluster;
use Webkul\PluginManager\Support\PluginEntitlement;
use Webkul\Support\Enums\NavigationGroup;

class Configuration extends Cluster
{
    protected static ?string $slug = 'invoices/configurations';

    // Last in the Invoices group (v1.16.6): at 0 it came before the invoices,
    // and the sidebar "Invoices" link opened Bank Accounts.
    protected static ?int $navigationSort = 10;

    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/clusters/configurations.navigation.title');
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
