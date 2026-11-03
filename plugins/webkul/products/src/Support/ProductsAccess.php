<?php

namespace Webkul\Product\Support;

use Webkul\PluginManager\Support\PluginEntitlement;
use Webkul\Security\Models\User;

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

    protected static function superAdminBypassesEntitlement(): bool
    {
        $user = auth()->user();

        return config('plugin-manager.entitlements.super_admin_bypass', true)
            && $user instanceof User
            && $user->isSuperAdmin();
    }
}
