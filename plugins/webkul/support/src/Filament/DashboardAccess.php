<?php

namespace Webkul\Support\Filament;

use Filament\Facades\Filament;

/**
 * The two per-user dashboard switches (user, 2026-11-20), in one place.
 *
 * "See dashboard" off hides every dashboard page and the Overview. "See
 * money figures" off hides the money dashboards (Invoices, Sales,
 * Purchases, Accounting) and their Overview sections, which also removes
 * the "Money in and out" chart. Lists and records are untouched: role
 * permissions decide those. Time off and the maintenance calendar are not
 * dashboards for this purpose; people use them to work.
 *
 * Columns default to true, so a user without a value sees everything, as
 * before. Fails closed only on an explicit false.
 */
class DashboardAccess
{
    /**
     * Overview sections that show money.
     */
    public const MONEY_SECTIONS = ['accounting', 'invoices', 'purchases', 'sales'];

    public static function mayViewDashboards(): bool
    {
        return static::switchIsOn('can_see_dashboard');
    }

    public static function mayViewMoney(): bool
    {
        return static::mayViewDashboards() && static::switchIsOn('can_see_money_figures');
    }

    protected static function switchIsOn(string $column): bool
    {
        $user = Filament::auth()?->user() ?? auth()->user();

        if ($user === null) {
            return false;
        }

        return $user->getAttribute($column) !== false;
    }
}
