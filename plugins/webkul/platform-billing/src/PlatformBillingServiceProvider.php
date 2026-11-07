<?php

namespace Webkul\PlatformBilling;

use Illuminate\Support\Facades\Gate;
use Webkul\PlatformBilling\Models\DocumentNumber;
use Webkul\PlatformBilling\Models\Plan;
use Webkul\PlatformBilling\Models\Price;
use Webkul\PlatformBilling\Models\Subscription;
use Webkul\PlatformBilling\Policies\DocumentNumberPolicy;
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

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->isCore()
            ->hasConfigFile('platform-billing')
            ->hasMigrations([
                '2026_10_28_000006_create_platform_billing_document_numbers_table',
                '2026_11_07_000001_create_platform_billing_plans_prices_subscriptions_tables',
                '2026_11_07_000002_add_subscription_activation_fields',
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
    }
}
