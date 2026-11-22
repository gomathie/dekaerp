<?php

namespace Webkul\Invoice\Filament\Clusters;

use Filament\Clusters\Cluster;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\Support\PluginEntitlement;
use Webkul\Support\Enums\NavigationGroup;

class Customers extends Cluster
{
    protected static ?string $slug = 'invoices/customers';

    public static function getNavigationLabel(): string
    {
        return static::shownUnderSales()
            ? __('invoices::filament/clusters/customers.navigation.in-sales')
            : __('invoices::filament/clusters/customers.navigation.title');
    }

    /**
     * Under Sales as an "Invoices" tab when the company has Sales (user,
     * 2026-11-22): selling and invoicing are one job, and a separate Invoices
     * sidebar entry split it. Without Sales it stays in its own group, so a
     * company that only invoices keeps its entry. Only the place in the menu
     * changes: the URL, permissions and module switch are the same.
     *
     * Read per request through the module switch, never in register():
     * routes are cached once for every company (AGENTS.md).
     */
    public static function getNavigationGroup(): string|\UnitEnum
    {
        return static::shownUnderSales() ? NavigationGroup::Sale : NavigationGroup::Invoice;
    }

    public static function canAccess(): bool
    {
        return PluginEntitlement::enabledForCurrent('invoices');
    }

    /** Sales depends on Invoices, so this asks the module switch, not Sales' code. */
    public static function shownUnderSales(): bool
    {
        return Package::isPluginInstalled('sales') && PluginEntitlement::enabledForCurrent('sales');
    }
}
