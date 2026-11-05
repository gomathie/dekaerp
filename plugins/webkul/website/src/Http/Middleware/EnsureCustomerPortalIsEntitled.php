<?php

namespace Webkul\Website\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Webkul\PluginManager\Support\PluginEntitlement;

class EnsureCustomerPortalIsEntitled
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $customer = Auth::guard('customer')->user();

        abort_unless($customer, Response::HTTP_FORBIDDEN);

        // A shared customer (no company, kept by §4k) belongs to no tenant, so
        // no company's Website switch governs them: they may always sign in.
        // Casting null to 0 refused every one of them. User decision, 2026-11-05.
        if ($customer->company_id === null) {
            return $next($request);
        }

        abort_unless(
            PluginEntitlement::enabledForCompany('website', (int) $customer->company_id),
            Response::HTTP_FORBIDDEN,
        );

        return $next($request);
    }
}
