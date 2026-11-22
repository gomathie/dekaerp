<?php

namespace Webkul\Invoice\Filament\Clusters;

use Filament\Clusters\Cluster;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\Support\PluginEntitlement;
use Webkul\Support\Enums\NavigationGroup;

class Vendors extends Cluster
{
    protected static ?string $slug = 'invoices/vendors';

    public static function getNavigationLabel(): string
    {
        return static::shownUnderPurchases()
            ? __('invoices::filament/clusters/vendors.navigation.in-purchases')
            : __('invoices::filament/clusters/vendors.navigation.title');
    }

    /**
     * Under Purchase as a "Bills" tab when the company has Purchases (user,
     * 2026-11-22, with invoices moving under Sales). Without Purchases it
     * stays in the Invoices group. Menu placement only; read per request.
     */
    public static function getNavigationGroup(): string|\UnitEnum
    {
        return static::shownUnderPurchases() ? NavigationGroup::Purchase : NavigationGroup::Invoice;
    }

    public static function canAccess(): bool
    {
        return PluginEntitlement::enabledForCurrent('invoices');
    }

    /** Purchases depends on Invoices, so this asks the module switch, not Purchases' code. */
    public static function shownUnderPurchases(): bool
    {
        return Package::isPluginInstalled('purchases') && PluginEntitlement::enabledForCurrent('purchases');
    }
}
