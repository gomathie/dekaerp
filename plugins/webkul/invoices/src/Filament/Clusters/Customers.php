<?php

namespace Webkul\Invoice\Filament\Clusters;

use Filament\Clusters\Cluster;
use Webkul\PluginManager\Support\PluginEntitlement;
use Webkul\Support\Enums\NavigationGroup;

class Customers extends Cluster
{
    protected static ?string $slug = 'invoices/customers';

    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/clusters/customers.navigation.title');
    }

    /**
     * Always in the Invoices group (user, 2026-11-23, hotfix v1.16.6).
     * *Superseded:* v1.16.1 moved it under Sales as an "Invoices" tab when the
     * company had Sales. That left only Configuration in the Invoices group,
     * so the sidebar "Invoices" link opened Bank Accounts instead of the
     * invoices. Do not move it back under Sales without asking the user.
     */
    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Invoice;
    }

    /**
     * First in the Invoices group: the sidebar "Invoices" link opens the
     * group's first entry, and that must be the invoices, not Configuration.
     */
    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function canAccess(): bool
    {
        return PluginEntitlement::enabledForCurrent('invoices');
    }
}
