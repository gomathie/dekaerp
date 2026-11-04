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

        abort_unless(
            $customer && PluginEntitlement::enabledForCompany('website', (int) $customer->company_id),
            Response::HTTP_FORBIDDEN,
        );

        return $next($request);
    }
}
