<?php

namespace Webkul\PlatformBilling;

use Illuminate\Support\Facades\Gate;
use Webkul\PlatformBilling\Models\DocumentNumber;
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
            ])
            ->runsMigrations();
    }

    public function packageBooted(): void
    {
        // Policies are registered explicitly in this codebase (see `support` and
        // `fields`); Laravel's namespace guessing only covers App\Models.
        Gate::policy(DocumentNumber::class, DocumentNumberPolicy::class);
    }
}
