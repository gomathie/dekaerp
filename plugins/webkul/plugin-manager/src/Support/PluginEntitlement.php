<?php

namespace Webkul\PluginManager\Support;

use Webkul\PluginManager\Exceptions\PluginNotEntitledException;
use Webkul\Support\Services\CompanyContext;

/**
 * The plugin-enforcement contract.
 *
 * `PluginAccess` answers "may this company use this plugin". This class is the
 * single place that turns that answer into a decision, so navigation, routes,
 * policies and services cannot each invent their own rule (and cannot each
 * invent their own way of getting it wrong).
 *
 * Three questions, deliberately separate:
 *
 *  - `enabledForCurrent()` - navigation. True when any company active in this
 *    session holds the grant; a menu entry that exists in one of the user's
 *    companies should be visible.
 *  - `enabledForCompany()` - a known company, typically a record's own. This is
 *    the check a policy must use.
 *  - `assertForCompany()` - the same, as an authorization failure, for services.
 *
 * An exempt plugin (the core set in `config('plugin-manager.entitlements.exempt')`)
 * is always enabled: it is part of the installation rather than a module, and
 * gating it would take the permission layer off the screen that manages it.
 */
class PluginEntitlement
{
    public static function isExempt(string $plugin): bool
    {
        $exempt = array_map(
            fn ($name): string => mb_strtolower(trim((string) $name)),
            (array) config('plugin-manager.entitlements.exempt', []),
        );

        return in_array(mb_strtolower(trim($plugin)), $exempt, true);
    }

    public static function enabledForCompany(string $plugin, ?int $companyId): bool
    {
        if (static::isExempt($plugin)) {
            return true;
        }

        if (! $companyId) {
            return false;
        }

        return PluginAccess::enabledFor($plugin, $companyId);
    }

    public static function enabledForCurrent(string $plugin): bool
    {
        if (static::isExempt($plugin)) {
            return true;
        }

        return PluginAccess::enabledForCurrent($plugin);
    }

    /**
     * @throws PluginNotEntitledException
     */
    public static function assertForCompany(string $plugin, ?int $companyId): void
    {
        if (static::enabledForCompany($plugin, $companyId)) {
            return;
        }

        throw $companyId
            ? PluginNotEntitledException::forCompany($plugin, $companyId)
            : PluginNotEntitledException::withoutCompany($plugin);
    }

    /**
     * The company a request should be judged against when the route carries no
     * record: the explicitly selected one, else the session default.
     *
     * SAAS-02 owns an explicit API selector; until then this is the same
     * fallback CompanyContext already documents for API requests.
     */
    public static function currentCompanyId(): ?int
    {
        return app(CompanyContext::class)->currentId();
    }
}
