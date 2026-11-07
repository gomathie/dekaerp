<?php

namespace Webkul\Invoice\Filament\Widgets\Concerns;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Facades\Filament;
use Webkul\PluginManager\Support\PluginEntitlement;

/**
 * Who may see an Invoice dashboard widget.
 *
 * Two departures from Shield's own HasWidgetShield::canView():
 * - the company must hold the Invoices module (SAAS-05). A widget is reachable
 *   on its own through Livewire, so hiding the page is not enough;
 * - a widget Shield cannot name a permission for is refused. Shield's version
 *   falls back to "allowed" there, which would show invoice totals to anyone.
 */
trait GuardsInvoiceDashboardWidget
{
    use HasWidgetShield;

    public static function canView(): bool
    {
        if (! PluginEntitlement::enabledForCurrent('invoices')) {
            return false;
        }

        $permission = static::getWidgetPermission();

        $user = Filament::auth()?->user();

        return $permission !== null && $user !== null && $user->can($permission);
    }
}
