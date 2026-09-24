<?php

namespace Webkul\Logistics;

use Filament\Panel;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Webkul\Chatter\Services\ChatterCleanupService;
use Webkul\Logistics\Listeners\CreateShipmentFromOrder;
use Webkul\Logistics\Models\Expense;
use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Models\Trip;
use Webkul\Logistics\Support\LogisticsAccess;
use Webkul\Logistics\Support\LogisticsSequences;
use Webkul\Logistics\Support\UninstallGuard;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;
use Webkul\Sale\Events\OrderConfirmed;
use Webkul\Support\Services\SequenceService;

class LogisticsServiceProvider extends PackageServiceProvider
{
    public static string $name = 'logistics';

    public static string $viewNamespace = 'logistics';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasTranslations()
            ->hasConfigFile('logistics')
            ->hasMigrations([
                '2026_10_01_000001_create_logistics_service_types_table',
                '2026_10_01_000002_create_logistics_vehicle_types_table',
                '2026_10_01_000003_create_logistics_package_types_table',
                '2026_10_01_000004_create_logistics_expense_categories_table',
                '2026_10_01_000005_create_logistics_company_settings_table',
                '2026_10_01_000006_create_logistics_drivers_table',
                '2026_10_01_000007_create_logistics_vehicles_table',
                '2026_10_01_000008_create_logistics_shipments_table',
                '2026_10_01_000009_create_logistics_shipment_lines_table',
                '2026_10_01_000010_create_logistics_trips_table',
                '2026_10_01_000011_create_logistics_trip_shipments_table',
                '2026_10_01_000012_create_logistics_stops_table',
                '2026_10_01_000013_create_logistics_shipment_events_table',
                '2026_10_01_000014_create_logistics_delivery_proofs_table',
                '2026_10_01_000015_create_logistics_stop_links_table',
                '2026_10_01_000016_create_logistics_shipment_charges_table',
                '2026_10_01_000017_create_logistics_shipment_charge_taxes_table',
                '2026_10_01_000018_create_logistics_expenses_table',
                '2026_10_01_000019_create_logistics_shipment_invoices_table',
            ])
            ->runsMigrations()
            ->hasRoutes(['web'])
            ->hasDependencies([
                'products',
                'employees',
                'accounts',
            ])
            ->hasSeeder('Webkul\\Logistics\\Database\\Seeders\\DatabaseSeeder')
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->installDependencies()
                    ->runsMigrations()
                    ->runsSeeders();
            })
            ->hasUninstallCommand(function (UninstallCommand $command): void {
                $command
                    ->startWith(function (): void {
                        UninstallGuard::ensureSafe();
                    })
                    ->endWith(function (): void {
                        ChatterCleanupService::purgeForModels([Shipment::class, Trip::class, Expense::class]);

                        SequenceService::purge(codes: LogisticsSequences::CODES);
                    });
            })
            ->icon('logistics');
    }

    public function packageBooted(): void
    {
        $this->registerStopLinkRateLimiter();
    }

    /**
     * Throttle for the public POD capture route (WP-5b).
     *
     * Defined in this plugin rather than in AppServiceProvider so it goes away
     * with the plugin, and keyed on the IP because there is no authenticated
     * user to key on. Tight on purpose: a driver opens one link and submits it
     * once, so anything beyond a handful a minute from one address is either a
     * mistake or someone guessing tokens.
     */
    protected function registerStopLinkRateLimiter(): void
    {
        RateLimiter::for('logistics-stop-link', function (Request $request): Limit {
            return Limit::perMinute(20)->by('ip:'.$request->ip());
        });
    }

    public function packageRegistered(): void
    {
        $this->app->scoped(LogisticsAccess::class);

        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(LogisticsPlugin::make());
        });

        $this->registerSalesIntegration();
    }

    /**
     * Listen for confirmed sales orders (D1).
     *
     * Registration is unconditional on purpose, and deliberately does NOT try
     * to decide here whether Sales is in use:
     *
     *  - Plugin installation is global, not per company. Sales being installed
     *    says nothing about whether *this* company sells, and Logistics being
     *    installed says nothing about whether this company ships.
     *  - `Package::isPluginInstalled()` reads the database. This runs at
     *    register time, before the database is necessarily reachable, and a
     *    boot-time query against the plugins table is what made
     *    `package:discover` hang for five minutes once already.
     *  - class_exists() would be no help either: every plugin's files are
     *    present in this monorepo whether or not it is installed.
     *
     * Nothing is needed. If Sales is not installed its tables do not exist, no
     * order can be confirmed, and the event never fires. If Sales is installed
     * but a company does not use Logistics, ShipmentFromOrder::shouldConvert()
     * declines on the per-company switch. The decision belongs there, once, in
     * a place that can be tested - not spread across boot.
     */
    protected function registerSalesIntegration(): void
    {
        Event::listen(OrderConfirmed::class, CreateShipmentFromOrder::class);
    }
}
