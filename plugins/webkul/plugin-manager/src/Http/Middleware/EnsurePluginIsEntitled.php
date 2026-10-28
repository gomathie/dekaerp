<?php

namespace Webkul\PluginManager\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Webkul\PluginManager\Exceptions\PluginNotEntitledException;
use Webkul\PluginManager\Support\PluginEntitlement;
use Webkul\Security\Models\User;

/**
 * Denies a route belonging to a module the acting company is not entitled to.
 *
 * Applied as `plugin.entitlement:<plugin>` to every plugin's `routes/api.php`
 * from `PackageServiceProvider`, so a plugin cannot forget it. Web routes and
 * Filament pages are guarded per plugin by the SAAS-05 slices, because a page
 * needs its own `canAccess()`/`canViewAny()` and a blanket middleware cannot
 * express "this record belongs to another company".
 *
 * Why this exists: grants could be revoked while the plugin's routes stayed
 * reachable - SAAS-00 proved a revoked Sales grant still answered 200 on the
 * orders endpoint. Revoking a module that still works is worse than not having
 * the feature, because it looks enforced.
 */
class EnsurePluginIsEntitled
{
    public function handle(Request $request, Closure $next, string $plugin): Response
    {
        if (PluginEntitlement::isExempt($plugin)) {
            return $next($request);
        }

        /*
         * This group runs *before* the route's own `auth:sanctum`, so
         * `$request->user()` is still null here. Resolving the guards directly is
         * what makes the check see the actor; Sanctum's RequestGuard reads the
         * bearer token on demand, and the web guard covers session users.
         *
         * `Auth::setUser()` then aligns the default guard with the actor we just
         * resolved, because CompanyContext reads `auth()->user()` - and the
         * default guard is not always `web` (a Sanctum request can make the token
         * guard the default). Without that, an entitled request would be read as
         * "no company" and denied.
         */
        $user = $request->user()
            ?? Auth::guard('sanctum')->user()
            ?? Auth::guard('web')->user();

        // A guest has no company to judge, and the route's own `auth:sanctum`
        // answers them with 401. Denying here would turn "authenticate" into
        // "forbidden" on every plugin API, which tells a client with an expired
        // token the wrong thing; nothing is reachable either way.
        if (! $user instanceof User) {
            return $next($request);
        }

        Auth::setUser($user);

        // The installation administrator, who may hold no membership at all.
        // Entitlement still applies to every tenant actor below.
        if (
            config('plugin-manager.entitlements.super_admin_bypass', true)
            && $user->isSuperAdmin()
        ) {
            return $next($request);
        }

        $companyId = PluginEntitlement::currentCompanyId();

        if (! $companyId) {
            throw PluginNotEntitledException::withoutCompany($plugin);
        }

        // A route carries no record, so "any active company" is the honest
        // answer here - the same one navigation uses. Record-scoped checks
        // belong in the policy, via PluginEntitlement::assertForCompany().
        if (! PluginEntitlement::enabledForCurrent($plugin)) {
            throw PluginNotEntitledException::forCompany($plugin, (int) $companyId);
        }

        return $next($request);
    }
}
