<?php

namespace Webkul\Purchase\Support;

use Webkul\PluginManager\Support\PluginEntitlement;

class PurchasesAccess
{
    /**
     * Keep the purchasing UI and current-company policy checks aligned with SAAS-04.
     */
    public static function enabledForCurrent(): bool
    {
        return static::superAdminBypassesEntitlement()
            || PluginEntitlement::enabledForCurrent('purchases');
    }

    public static function enabledForCompany(?int $companyId): bool
    {
        return static::superAdminBypassesEntitlement()
            || PluginEntitlement::enabledForCompany('purchases', $companyId);
    }

    public static function assertForCompany(?int $companyId): void
    {
        if (static::superAdminBypassesEntitlement()) {
            return;
        }

        PluginEntitlement::assertForCompany('purchases', $companyId);
    }

    protected static function superAdminBypassesEntitlement(): bool
    {
        return config('plugin-manager.entitlements.super_admin_bypass', true)
            && auth()->user()?->isSuperAdmin() === true;
    }
}
