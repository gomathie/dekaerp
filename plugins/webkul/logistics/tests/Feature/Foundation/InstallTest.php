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

it('only creates logistics tables in its migrations', function () {
    foreach (glob(base_path('plugins/webkul/logistics/database/migrations/*.php')) as $file) {
        $source = file_get_contents($file);

        expect($source)->not->toContain('Schema::table(')
            ->and($source)->not->toContain('DB::')
            ->and(preg_match_all("/Schema::create\('([^']+)'/", $source, $matches))->toBe(1)
            ->and($matches[1][0])->toStartWith('logistics_');
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
