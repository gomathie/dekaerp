<?php

namespace App\Providers;

use Illuminate\Auth\Events\Authenticated;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Livewire\Component;
use Livewire\Livewire;
use Sentry\State\Scope;
use Throwable;
use Webkul\Security\Models\User;
use Webkul\Support\Services\CompanyContext;

use function Livewire\on;
use function Livewire\store;
use function Sentry\configureScope;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(Authenticatable::class, User::class);
    }

    public function boot(): void
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by(strtolower((string) $request->input('email')).'|'.$request->ip());
        });

        $this->tagSentryWithTenantContext();

        on('dehydrate', function (Component $component): void {
            if (! Livewire::isLivewireRequest()) {
                return;
            }

            if (! store($component)->has('redirect')) {
                return;
            }

            $notifications = session()->pull('filament.notifications');

            if (empty($notifications)) {
                return;
            }

            session()->put('filament.claimed_notifications', $notifications);
        });
    }

    /**
     * Attach the tenant context to Sentry events.
     *
     * Data in this application is company-scoped across 87 models, so "which
     * company was this?" is the first question on any report. Without the tag
     * a stack trace cannot be tied to the records that produced it.
     *
     * Bound to Authenticated rather than resolved at report time: the scope is
     * populated during the normal request, so nothing has to touch the session
     * or database while an exception is already being handled.
     */
    protected function tagSentryWithTenantContext(): void
    {
        Event::listen(Authenticated::class, function (Authenticated $event): void {
            if (! app()->bound('sentry')) {
                return;
            }

            try {
                $context = app(CompanyContext::class);

                configureScope(function (Scope $scope) use ($event, $context): void {
                    // Identifier only. Request payloads here carry customer,
                    // partner and invoice data, so no email or name is sent.
                    $scope->setUser(['id' => $event->user->getAuthIdentifier()]);

                    $scope->setTag('company.current', (string) ($context->currentId() ?? 'none'));
                    $scope->setTag('company.active_count', (string) count($context->activeIds()));
                    $scope->setTag('company.bypassed', $context->bypassed() ? 'true' : 'false');
                });
            } catch (Throwable) {
                // Tagging is diagnostic. It must never fail a request, and must
                // never replace the error it was meant to describe.
            }
        });
    }
}
