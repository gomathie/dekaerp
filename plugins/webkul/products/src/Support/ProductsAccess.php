<?php

namespace Webkul\Product\Support;

use Webkul\PluginManager\Support\PluginEntitlement;

class ProductsAccess
{
    /**
     * A company with a revoked catalogue grant must not reach product UI or policies.
     */
    public static function enabledForCurrent(): bool
    {
        return static::superAdminBypassesEntitlement()
            || PluginEntitlement::enabledForCurrent('products');
    }

    public static function enabledForCompany(?int $companyId): bool
    {
        return static::superAdminBypassesEntitlement()
            || PluginEntitlement::enabledForCompany('products', $companyId);
    }

    public static function assertForCompany(?int $companyId): void
    {
        if (static::superAdminBypassesEntitlement()) {
            return;
        }

        PluginEntitlement::assertForCompany('products', $companyId);
    }

    protected static function superAdminBypassesEntitlement(): bool
    {
        return config('plugin-manager.entitlements.super_admin_bypass', true)
            && auth()->user()?->isSuperAdmin() === true;
    }
}
