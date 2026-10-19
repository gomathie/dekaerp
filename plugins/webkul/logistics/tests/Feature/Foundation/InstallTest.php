<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Webkul\Logistics\Exceptions\UninstallBlockedException;
use Webkul\Logistics\Models\CompanySetting;
use Webkul\Logistics\Models\ExpenseCategory;
use Webkul\Logistics\Models\PackageType;
use Webkul\Logistics\Models\ServiceType;
use Webkul\Logistics\Models\VehicleType;
use Webkul\Logistics\Support\LogisticsSequences;
use Webkul\Logistics\Support\UninstallGuard;
use Webkul\PluginManager\Package;
use Webkul\Support\Models\Sequence;

require_once __DIR__.'/../../Helpers/LogisticsHelper.php';

beforeEach(function () {
    LogisticsHelper::install();
});

it('registers the plugin as installed', function () {
    expect(Package::isPluginInstalled('logistics'))->toBeTrue();
});

it('creates every logistics table', function () {
    $tables = [
        'logistics_service_types', 'logistics_vehicle_types', 'logistics_package_types',
        'logistics_expense_categories', 'logistics_company_settings', 'logistics_drivers',
        'logistics_vehicles', 'logistics_shipments', 'logistics_shipment_lines', 'logistics_trips',
        'logistics_trip_shipments', 'logistics_stops', 'logistics_shipment_events',
        'logistics_delivery_proofs', 'logistics_stop_links', 'logistics_shipment_charges',
        'logistics_shipment_charge_taxes', 'logistics_expenses', 'logistics_shipment_invoices',
    ];

    foreach ($tables as $table) {
        expect(Schema::hasTable($table))->toBeTrue("{$table} is missing");
    }
});

it('seeds only shared configuration, with no company', function () {
    expect(ServiceType::withoutGlobalScopes()->whereNull('company_id')->count())->toBeGreaterThanOrEqual(4)
        ->and(VehicleType::withoutGlobalScopes()->whereNull('company_id')->count())->toBeGreaterThanOrEqual(4)
        ->and(PackageType::withoutGlobalScopes()->whereNull('company_id')->count())->toBeGreaterThanOrEqual(6)
        ->and(ExpenseCategory::withoutGlobalScopes()->whereNull('company_id')->count())->toBeGreaterThanOrEqual(9)
        ->and(ServiceType::withoutGlobalScopes()->whereNotNull('company_id')->count())->toBe(0);
});

it('creates nothing per company at install', function () {
    expect(CompanySetting::withoutGlobalScopes()->count())->toBe(0)
        ->and(Sequence::withoutGlobalScopes()->whereIn('code', LogisticsSequences::CODES)->count())->toBe(0);
});

it('can run the seeders again without duplicating shared rows', function () {
    $before = ServiceType::withoutGlobalScopes()->count();

    Artisan::call('db:seed', ['--class' => 'Webkul\\Logistics\\Database\\Seeders\\DatabaseSeeder', '--force' => true]);

    expect(ServiceType::withoutGlobalScopes()->count())->toBe($before);
});

it('only touches logistics tables in its migrations', function () {
    foreach (glob(base_path('plugins/webkul/logistics/database/migrations/*.php')) as $file) {
        $source = file_get_contents($file);

        // Raw queries would bypass the schema builder entirely and could reach
        // any table in the database.
        expect($source)->not->toContain('DB::');

        // Every table this plugin touches must be its own, because uninstall
        // drops logistics_* and nothing else: a column added to a table owned
        // by another plugin would survive uninstall and be left behind.
        // Schema::table() is allowed for exactly that reason - an additive
        // migration on a logistics table (..._000020) goes away with the table.
        preg_match_all("/Schema::(?:create|table|rename|drop|dropIfExists)\('([^']+)'/", $source, $touched, PREG_SET_ORDER);

        expect($touched)->not->toBeEmpty();

        foreach ($touched as $match) {
            expect($match[1])->toStartWith('logistics_');
        }

        // A migration that creates a table creates one, as the WP-1 set does.
        $creates = preg_match_all("/Schema::create\('/", $source);

        if ($creates > 0) {
            expect($creates)->toBe(1);
        }
    }
});

it('refuses to uninstall while shipments exist', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    LogisticsHelper::shipment($company);

    expect(fn () => UninstallGuard::ensureSafe())->toThrow(UninstallBlockedException::class);
});

it('wires the uninstall guard to run before any table is dropped', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    LogisticsHelper::shipment($company);

    $command = Artisan::all()['logistics:uninstall'];

    expect($command->startWith)->not->toBeNull()
        ->and(fn () => ($command->startWith)($command))->toThrow(UninstallBlockedException::class);
});

it('allows uninstall when no shipments exist or when explicitly allowed', function () {
    UninstallGuard::ensureSafe();

    $company = LogisticsHelper::enable(LogisticsHelper::company());
    LogisticsHelper::shipment($company);

    config(['logistics.allow_uninstall_with_data' => true]);

    UninstallGuard::ensureSafe();

    expect(true)->toBeTrue();
});
