<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Coarse-grained scoping for Sanctum tokens on the plugin API.
 *
 * Sanctum stores abilities per token but nothing enforces them unless a route
 * asks, and none of the plugin routes do - so every issued token was
 * effectively unlimited. Rather than annotate several hundred endpoints, this
 * maps the HTTP method onto one of two abilities: safe methods need "read",
 * everything else needs "write". That is enough to hand a client an
 * integration token that cannot mutate the ERP.
 *
 * Tokens holding "*" bypass the check entirely, so every token issued before
 * this existed keeps working exactly as it did.
 */
class EnforceApiTokenAbilities
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->user()?->currentAccessToken();

        // Unauthenticated routes (login) and session-authenticated requests
        // have no personal access token to scope.
        if (! $token) {
            return $next($request);
        }

        if ($token->can('*')) {
            return $next($request);
        }

        $required = $request->isMethodSafe() ? 'read' : 'write';

        if (! $token->can($required)) {
            return response()->json([
                'message' => "This API token does not have the [{$required}] ability.",
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
