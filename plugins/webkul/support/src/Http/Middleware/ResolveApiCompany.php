<?php

namespace Webkul\Support\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Webkul\Security\Models\User;
use Webkul\Support\Services\CompanyContext;

/**
 * Lets an API client say which of its companies the request is for.
 *
 * Without this the company was implicit: `CompanyContext` reads the web session,
 * and a token request has none, so every integration silently acted on the
 * user's *default* company. Two consequences - a client with several companies
 * could not address the second one, and a mistake looked like data loss rather
 * than a refusal.
 *
 * The selector is the `X-Company-Id` header and nothing else. A `company_id`
 * body or query field is deliberately **not** read: plenty of API writes carry
 * one as record data, and treating it as the selector would silently switch the
 * tenant of a request that only meant to set a column. Membership is checked
 * against the user's own allow-list, so a forged id is a 403 and not a context
 * switch; a super admin may select any company, which is the same
 * installation-administrator position they hold in the UI.
 *
 * No header keeps the documented compatibility fallback: the default company,
 * exactly as before, so existing integrations are unaffected until they opt in.
 * A guest passes through untouched so the route's own `auth:sanctum` answers
 * 401 - a 403 here would tell a client with an expired token the wrong thing.
 */
class ResolveApiCompany
{
    public const HEADER = 'X-Company-Id';

    public function handle(Request $request, Closure $next): Response
    {
        /*
         * This runs before the route's own `auth:sanctum`, so `$request->user()`
         * is null here. Resolving the guards directly is what lets the check see
         * the actor; `Auth::setUser()` then aligns the default guard because
         * CompanyContext reads `auth()->user()` and the default guard is not
         * always `web` on a token request.
         */
        $user = $request->user()
            ?? Auth::guard('sanctum')->user()
            ?? Auth::guard('web')->user();

        if (! $user instanceof User) {
            return $next($request);
        }

        Auth::setUser($user);

        $requested = $request->header(self::HEADER);

        if ($requested === null || $requested === '') {
            return $next($request);
        }

        if (! is_numeric($requested)) {
            throw new AuthorizationException(__('The requested company is not valid.'));
        }

        $companyId = (int) $requested;

        if ($user->isSuperAdmin()) {
            app(CompanyContext::class)->forceActive($companyId);

            return $next($request);
        }

        if (! in_array($companyId, app(CompanyContext::class)->allowedIds(), true)) {
            throw new AuthorizationException(__('You are not a member of the requested company.'));
        }

        app(CompanyContext::class)->forceActive($companyId);

        return $next($request);
    }
}
