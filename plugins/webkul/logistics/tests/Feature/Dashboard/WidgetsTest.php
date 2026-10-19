<?php

use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Webkul\Logistics\Enums\ShipmentState;
use Webkul\Logistics\Enums\TripState;
use Webkul\Logistics\Filament\Pages\Dashboard as LogisticsDashboard;
use Webkul\Logistics\Filament\Widgets\FleetStatsWidget;
use Webkul\Logistics\Filament\Widgets\ShipmentStatsWidget;
use Webkul\Logistics\Filament\Widgets\UnbilledRevenueWidget;
use Webkul\Logistics\Models\Driver;
use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Models\Trip;
use Webkul\Logistics\Models\Vehicle;

require_once __DIR__.'/../../Helpers/LogisticsHelper.php';

beforeEach(function () {
    LogisticsHelper::install();
});

/**
 * Reads the numbers a stats widget renders, keyed by their label.
 *
 * @return array<string, string>
 */
function widgetFigures(object $widget): array
{
    $method = new ReflectionMethod($widget, 'getStats');
    $method->setAccessible(true);

    $figures = [];

    foreach ($method->invoke($widget) as $stat) {
        $figures[(string) $stat->getLabel()] = (string) $stat->getValue();
    }

    return $figures;
}

function shipmentInState($company, ShipmentState $state, array $overrides = []): Shipment
{
    return LogisticsHelper::shipment($company, array_merge(['state' => $state], $overrides));
}

it('counts shipments by state from the seeded data', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    shipmentInState($company, ShipmentState::AWAITING_PICKUP);
    shipmentInState($company, ShipmentState::AWAITING_PICKUP);
    shipmentInState($company, ShipmentState::IN_TRANSIT);
    shipmentInState($company, ShipmentState::OUT_FOR_DELIVERY);
    shipmentInState($company, ShipmentState::FAILED_DELIVERY);
    shipmentInState($company, ShipmentState::DELIVERED, ['actual_delivery_at' => now()]);

    // Overdue: past its expected delivery and still open.
    shipmentInState($company, ShipmentState::IN_TRANSIT, ['expected_delivery_at' => now()->subDay()]);

    // Not overdue, because it is already delivered.
    shipmentInState($company, ShipmentState::DELIVERED, [
        'expected_delivery_at' => now()->subDay(),
        'actual_delivery_at'   => now(),
    ]);

    CompanyHelper::actingAsCompanyUser($company, ['view_any_logistics_shipment']);

    $figures = widgetFigures(new ShipmentStatsWidget);

    expect($figures['Awaiting pickup'])->toBe('2')
        // Two in transit, one of which is the overdue one.
        ->and($figures['In transit'])->toBe('2')
        ->and($figures['Out for delivery'])->toBe('1')
        ->and($figures['Failed delivery'])->toBe('1')
        ->and($figures['Delivered today'])->toBe('2')
        ->and($figures['Overdue'])->toBe('1');
});

it('counts shipments with one grouped query, not one per stat', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    foreach (range(1, 6) as $ignored) {
        shipmentInState($company, ShipmentState::IN_TRANSIT);
    }

    CompanyHelper::actingAsCompanyUser($company, ['view_any_logistics_shipment']);

    $statements = [];

    DB::listen(function ($query) use (&$statements): void {
        $statements[] = $query->sql;
    });

    widgetFigures(new ShipmentStatsWidget);

    $shipmentQueries = collect($statements)
        ->filter(fn (string $sql): bool => str_contains($sql, '"logistics_shipments"'))
        ->count();

    // One grouped count plus the three dated figures. Nine separate counts
    // would mean nine round trips on every dashboard load.
    expect($shipmentQueries)->toBeLessThanOrEqual(4);
});

it('never counts another company’s shipments', function () {
    $a = LogisticsHelper::enable(LogisticsHelper::company());
    $b = LogisticsHelper::enable(LogisticsHelper::company());

    shipmentInState($a, ShipmentState::IN_TRANSIT);
    shipmentInState($b, ShipmentState::IN_TRANSIT);
    shipmentInState($b, ShipmentState::IN_TRANSIT);

    CompanyHelper::actingAsCompanyUser($a, ['view_any_logistics_shipment']);

    expect(widgetFigures(new ShipmentStatsWidget)['In transit'])->toBe('1');
});

it('treats an archived vehicle or an expired licence as unavailable', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    Vehicle::factory()->create(['company_id' => $company->id, 'is_active' => true]);
    Vehicle::factory()->create(['company_id' => $company->id, 'is_active' => false]);

    Driver::factory()->create([
        'company_id'         => $company->id,
        'is_active'          => true,
        'license_expires_at' => now()->addYear(),
    ]);

    // Expired licence: DispatchService refuses to dispatch them, so the
    // dashboard must not offer them as available.
    Driver::factory()->create([
        'company_id'         => $company->id,
        'is_active'          => true,
        'license_expires_at' => now()->subDay(),
    ]);

    CompanyHelper::actingAsCompanyUser($company, ['view_any_logistics_trip']);

    $figures = widgetFigures(new FleetStatsWidget);

    expect($figures['Vehicles available'])->toBe('1')
        ->and($figures['Drivers available'])->toBe('1');
});

it('counts a dispatched trip as on the road', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    Trip::create(['company_id' => $company->id, 'state' => TripState::PLANNED]);
    Trip::create(['company_id' => $company->id, 'state' => TripState::DISPATCHED]);
    Trip::create(['company_id' => $company->id, 'state' => TripState::IN_PROGRESS]);
    Trip::create(['company_id' => $company->id, 'state' => TripState::COMPLETED]);

    CompanyHelper::actingAsCompanyUser($company, ['view_any_logistics_trip']);

    $figures = widgetFigures(new FleetStatsWidget);

    expect($figures['On the road'])->toBe('2')
        ->and($figures['Planned trips'])->toBe('1');
});

it('hides the finance widget from a user without view_financials', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    // Shipments and trips, but no financial permission.
    CompanyHelper::actingAsCompanyUser($company, [
        'view_any_logistics_shipment',
        'view_any_logistics_trip',
    ]);

    expect(UnbilledRevenueWidget::canView())->toBeFalse()
        // The operational widgets stay visible: this is about money, not access
        // to logistics generally.
        ->and(ShipmentStatsWidget::canView())->toBeTrue()
        ->and(FleetStatsWidget::canView())->toBeTrue();

    CompanyHelper::actingAsCompanyUser($company, ['view_financials_logistics_shipment']);

    expect(UnbilledRevenueWidget::canView())->toBeTrue();
});

it('hides every widget from a company that has not enabled Logistics', function () {
    $company = LogisticsHelper::company();

    CompanyHelper::actingAsCompanyUser($company, [
        'view_any_logistics_shipment',
        'view_any_logistics_trip',
        'view_financials_logistics_shipment',
    ]);

    expect(ShipmentStatsWidget::canView())->toBeFalse()
        ->and(FleetStatsWidget::canView())->toBeFalse()
        ->and(UnbilledRevenueWidget::canView())->toBeFalse();
});

it('opens the Logistics dashboard for a permitted user of an enabled company', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    CompanyHelper::actingAsCompanyUser($company, [
        'page_logistics_dashboard',
        'view_any_logistics_shipment',
        'view_any_logistics_trip',
    ]);

    expect(LogisticsDashboard::canAccess())->toBeTrue();

    Livewire::test(LogisticsDashboard::class)->assertOk();
});

it('forbids the Logistics dashboard without its page permission', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    // Permission to see shipments is not permission to open the dashboard.
    CompanyHelper::actingAsCompanyUser($company, ['view_any_logistics_shipment']);

    expect(LogisticsDashboard::canAccess())->toBeFalse();

    Livewire::test(LogisticsDashboard::class)->assertForbidden();
});

it('forbids the Logistics dashboard for a company that has not enabled Logistics', function () {
    $company = LogisticsHelper::company();

    // The page permission alone is not enough: the company switch decides
    // whether this company does logistics at all.
    CompanyHelper::actingAsCompanyUser($company, [
        'page_logistics_dashboard',
        'view_any_logistics_shipment',
    ]);

    expect(LogisticsDashboard::canAccess())->toBeFalse();
});

it('lists the three Logistics widgets on its own dashboard', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    CompanyHelper::actingAsCompanyUser($company, ['page_logistics_dashboard']);

    expect(app(LogisticsDashboard::class)->getWidgets())
        ->toBe([ShipmentStatsWidget::class, FleetStatsWidget::class, UnbilledRevenueWidget::class]);
});
