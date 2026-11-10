<?php

namespace Webkul\Security\Http\Middleware;

use Closure;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Sentry\State\Scope;
use Symfony\Component\HttpFoundation\Response;
use Webkul\Security\Services\TenantImpersonation;

/**
 * Ends an impersonation that has expired or is no longer allowed, on the
 * request where that becomes true, and labels everything else.
 *
 * Without the per-request check the 60-minute limit would be a number on a
 * banner, and revoking someone's grant mid-session would change nothing until
 * they chose to leave. The log context is how "relevant actions" stay
 * auditable: every log line, and every Sentry event, carries the
 * administrator's id while the request runs as the tenant user.
 *
 * Registered on the admin panel's authMiddleware, after Authenticate.
 */
class EnforceImpersonationSession
{
    public function __construct(protected TenantImpersonation $impersonation) {}

    public function handle(Request $request, Closure $next): Response
    {
        $state = $this->impersonation->state();

        if ($state === null) {
            return $next($request);
        }

        $why = $this->impersonation->invalidReason();

        if ($why !== null) {
            $this->impersonation->end($why);

            Notification::make()
                ->warning()
                ->title(__('security::impersonation.notifications.ended-automatically.'.$why))
                ->send();

            return redirect()->to(filament()->getUrl() ?? '/');
        }

        Log::withContext([
            'impersonator_id'          => $state['impersonator_id'],
            'impersonated_id'          => $state['target_id'],
            'impersonation_company_id' => $state['company_id'],
        ]);

        if (function_exists('\Sentry\configureScope')) {
            \Sentry\configureScope(function (Scope $scope) use ($state): void {
                $scope->setTag('impersonator_id', (string) $state['impersonator_id']);
            });
        }

        return $next($request);
    }
}
