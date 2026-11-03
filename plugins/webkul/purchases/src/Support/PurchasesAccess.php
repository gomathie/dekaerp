<?php

namespace Webkul\Purchase\Support;

use Webkul\Partner\Models\Partner;
use Webkul\PluginManager\Support\PluginEntitlement;
use Webkul\Security\Models\User;

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

    /**
     * Stop non-HTTP purchasing writes at the same entitlement boundary as the UI and API.
     */
    public static function assertForCompany(?int $companyId): void
    {
        if (static::superAdminBypassesEntitlement()) {
            return;
        }

        PluginEntitlement::assertForCompany('purchases', $companyId);
    }

    public static function enabledForCustomer(?Partner $customer): bool
    {
        // Customer guards do not use CompanyContext, so their own company selects the grant.
        return $customer?->company_id !== null
            && PluginEntitlement::enabledForCompany('purchases', (int) $customer->company_id);
    }

    protected static function superAdminBypassesEntitlement(): bool
    {
        $user = auth()->user();

        return config('plugin-manager.entitlements.super_admin_bypass', true)
            && $user instanceof User
            && $user->isSuperAdmin();
    }
}
