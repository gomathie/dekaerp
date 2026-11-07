<?php

namespace Webkul\Support\Filament\Concerns;

/**
 * The Shield page-permission half of a page's canAccess().
 *
 * A canAccess() written on a page replaces HasPageShield::canAccess(), so a
 * page that adds the module switch (SAAS-05) must check its permission itself.
 * The SAAS-05 rollout (fa9d134f8, 98f40d9e6, 2026-11-03/04) replaced it with
 * the module check alone on 13 pages, opening company settings and dashboards
 * to every user of an entitled company whatever their role. Found and fixed
 * 2026-11-07. Pages combine both: `Module::enabledForCurrent() &&
 * static::hasPagePermission()`.
 *
 * Mirrors HasPageShield exactly, including its rule that a page Shield does
 * not manage (no permission name) stays open, so no page that worked before
 * the rollout changes behaviour.
 */
trait ChecksPagePermission
{
    protected static function hasPagePermission(): bool
    {
        $permission = static::getPagePermission();

        if ($permission === null) {
            return true;
        }

        return (bool) auth()->user()?->can($permission);
    }
}
