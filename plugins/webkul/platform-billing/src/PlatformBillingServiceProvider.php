<?php

namespace Webkul\PlatformBilling;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Webkul\PlatformBilling\Console\Commands\InvoiceSubscriptions;
use Webkul\PlatformBilling\Models\Allocation;
use Webkul\PlatformBilling\Models\DocumentNumber;
use Webkul\PlatformBilling\Models\Invoice;
use Webkul\PlatformBilling\Models\InvoiceLine;
use Webkul\PlatformBilling\Models\OverdueExtension;
use Webkul\PlatformBilling\Models\Payment;
use Webkul\PlatformBilling\Models\Plan;
use Webkul\PlatformBilling\Models\Price;
use Webkul\PlatformBilling\Models\Reversal;
use Webkul\PlatformBilling\Models\Subscription;
use Webkul\PlatformBilling\Policies\DocumentNumberPolicy;
use Webkul\PlatformBilling\Services\CompanyReadOnlyWriteGuard;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;

/**
 * DEKA's own billing: plans, subscriptions and the invoices it issues to
 * customers. Not a tenant module - it is how the installation is paid for.
 *
 * `isCore()` because it is platform infrastructure rather than something a
 * company subscribes to: core plugins are never offered on the Plugins page and
 * are exempt from the per-company entitlement gate, which is exactly right for a
 * plugin that administers tenants instead of serving one.
 *
 * Plans, subscriptions, discounts, recurring invoices and payments live here.
 * Screens are SAAS-12 and SAAS-13.
 */
class PlatformBillingServiceProvider extends PackageServiceProvider
{
    public static string $name = 'platform-billing';

    protected static ?object $readOnlyWriteGuardDispatcher = null;

    protected static ?object $invoiceScheduleApp = null;

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->isCore()
            ->hasConfigFile('platform-billing')
            ->hasMigrations([
                '2026_10_28_000006_create_platform_billing_document_numbers_table',
                '2026_11_07_000001_create_platform_billing_plans_prices_subscriptions_tables',
                '2026_11_07_000002_add_subscription_activation_fields',
                '2026_11_09_000001_create_platform_billing_discounts_tables',
                '2026_11_09_000002_create_platform_billing_invoices_tables',
                '2026_11_09_000003_create_platform_billing_payments_tables',
                '2026_11_09_000005_create_platform_billing_overdue_extensions_table',
            ])
            ->hasCommands([
                InvoiceSubscriptions::class,
            ])
            ->runsMigrations();
    }

    public function packageBooted(): void
    {
        // Policies are registered explicitly in this codebase (see `support` and
        // `fields`); Laravel's namespace guessing only covers App\Models.
        Gate::policy(DocumentNumber::class, DocumentNumberPolicy::class);
        // D-SAAS-06: catalog and subscription writes remain platform-only.
        Gate::policy(Plan::class, DocumentNumberPolicy::class);
        Gate::policy(Price::class, DocumentNumberPolicy::class);
        Gate::policy(Subscription::class, DocumentNumberPolicy::class);
        Gate::policy(Invoice::class, DocumentNumberPolicy::class);
        Gate::policy(InvoiceLine::class, DocumentNumberPolicy::class);
        Gate::policy(Payment::class, DocumentNumberPolicy::class);
        Gate::policy(Allocation::class, DocumentNumberPolicy::class);
        Gate::policy(Reversal::class, DocumentNumberPolicy::class);
        Gate::policy(OverdueExtension::class, DocumentNumberPolicy::class);

        // A process-wide boolean would skip every application after the first.
        // Pest builds a new application per test, so the hook has to follow the
        // instance, the same way the read-only guard follows the dispatcher.
        if (static::$invoiceScheduleApp !== $this->app) {
            static::$invoiceScheduleApp = $this->app;
            // packageBooted can run again on one application. One hook, and the
            // callback itself refuses a second event on the same scheduler.
            // The console kernel can resolve the scheduler before this provider
            // finishes booting; afterResolving does not replay that instance.
            $register = function (Schedule $schedule): void {
                $already = collect($schedule->events())->contains(
                    fn ($event): bool => str_contains((string) $event->command, 'platform-billing:invoice-subscriptions'),
                );

                if ($already) {
                    return;
                }

                $schedule->command('platform-billing:invoice-subscriptions')
                    ->dailyAt('02:15')
                    ->withoutOverlapping()
                    ->name('platform-billing-invoice-subscriptions');
            };

            $this->app->afterResolving(Schedule::class, $register);

            if ($this->app->resolved(Schedule::class)) {
                $register($this->app->make(Schedule::class));
            }
        }

        $dispatcher = Event::getFacadeRoot();

        if (static::$readOnlyWriteGuardDispatcher === $dispatcher) {
            return;
        }

        // Test bootstrap can replace Laravel's dispatcher between app instances.
        // Register once per dispatcher to avoid both stale guards and duplicates.
        static::$readOnlyWriteGuardDispatcher = $dispatcher;

        // Run after model-specific creating hooks have resolved the row's company.
        Event::listen('eloquent.creating: *', function (string $event, array $payload): void {
            $model = $payload[0] ?? null;

            if ($model instanceof Model) {
                app(CompanyReadOnlyWriteGuard::class)->assertMayWrite($model);
            }
        });
        Event::listen('eloquent.saving: *', function (string $event, array $payload): void {
            $model = $payload[0] ?? null;

            if ($model instanceof Model && $model->exists) {
                app(CompanyReadOnlyWriteGuard::class)->assertMayWrite($model);
            }
        });
        Event::listen('eloquent.deleting: *', function (string $event, array $payload): void {
            $model = $payload[0] ?? null;

            if ($model instanceof Model) {
                app(CompanyReadOnlyWriteGuard::class)->assertMayWrite($model);
            }
        });
    }
}
