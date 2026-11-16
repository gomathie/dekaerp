<?php

namespace Webkul\Sale\Support;

use Webkul\PluginManager\Support\PluginEntitlement;
use Webkul\Security\Models\User;

class SalesAccess
{
    /**
     * Keep the sales UI and current-company policy checks aligned with SAAS-04.
     */
    public static function enabledForCurrent(): bool
    {
        return static::superAdminBypassesEntitlement()
            || PluginEntitlement::enabledForCurrent('sales');
    }

    public static function enabledForCompany(?int $companyId): bool
    {
        return static::superAdminBypassesEntitlement()
            || PluginEntitlement::enabledForCompany('sales', $companyId);
    }

    /**
     * Stop non-HTTP sales writes at the same entitlement boundary as the UI and API.
     */
    public static function assertForCompany(?int $companyId): void
    {
        if (static::superAdminBypassesEntitlement()) {
            return;
        }

        PluginEntitlement::assertForCompany('sales', $companyId);
    }

    protected static function superAdminBypassesEntitlement(): bool
    {
        $user = auth()->user();

        return config('plugin-manager.entitlements.super_admin_bypass', true)
            && $user instanceof User
            && $user->isSuperAdmin();
    }
}
