<?php

namespace Webkul\PlatformBilling;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Webkul\PlatformBilling\Models\DocumentNumber;
use Webkul\PlatformBilling\Models\Plan;
use Webkul\PlatformBilling\Models\Price;
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
 * Foundation only, by design: value objects, numbering and policies. Plans,
 * subscriptions, invoices, payments and screens are SAAS-09 to SAAS-12, each of
 * which adds its own migrations here.
 */
class PlatformBillingServiceProvider extends PackageServiceProvider
{
    public static string $name = 'platform-billing';

    protected static ?object $readOnlyWriteGuardDispatcher = null;

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
