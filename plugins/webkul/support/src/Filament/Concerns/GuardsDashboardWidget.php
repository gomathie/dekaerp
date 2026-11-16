<?php

namespace Webkul\Support\Filament\Concerns;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Facades\Filament;
use Webkul\PluginManager\Support\PluginEntitlement;

/**
 * Who may see a module dashboard widget (docs/completed/dashboards-plan.md §2).
 *
 * Two departures from Shield's own HasWidgetShield::canView():
 * - the company must hold the widget's module (SAAS-05). A widget is reachable
 *   on its own through Livewire, so hiding the dashboard page is not enough;
 * - a widget Shield cannot name a permission for is refused. Shield's version
 *   falls back to "allowed" there, which would show a module's totals to
 *   anyone.
 *
 * Each module names itself once, through a small trait of its own that uses
 * this one, so the rule stays in one place.
 */
trait GuardsDashboardWidget
{
    use HasWidgetShield;

    /**
     * The plugin name in `plugins.name`, e.g. "invoices".
     */
    abstract protected static function dashboardPluginName(): string;

    public static function canView(): bool
    {
        if (! PluginEntitlement::enabledForCurrent(static::dashboardPluginName())) {
            return false;
        }

        $permission = static::getWidgetPermission();

        $user = Filament::auth()?->user();

        return $permission !== null && $user !== null && $user->can($permission);
    }
}
