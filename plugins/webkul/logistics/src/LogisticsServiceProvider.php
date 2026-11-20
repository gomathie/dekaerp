<?php

namespace Webkul\Logistics;

use Filament\Panel;
use Webkul\Chatter\Services\ChatterCleanupService;
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

    public function packageRegistered(): void
    {
        $this->app->scoped(LogisticsAccess::class);

        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(LogisticsPlugin::make());
        });
    }
}
