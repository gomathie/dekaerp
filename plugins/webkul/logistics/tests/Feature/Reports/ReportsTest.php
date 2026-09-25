<?php

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Livewire\Livewire;
use Webkul\Logistics\Enums\ExpenseState;
use Webkul\Logistics\Enums\ShipmentState;
use Webkul\Logistics\Filament\Clusters\Reporting\Pages\DeliveryPerformance;
use Webkul\Logistics\Filament\Clusters\Reporting\Pages\DriverTripHistory;
use Webkul\Logistics\Filament\Clusters\Reporting\Pages\ShipmentProfitability;
use Webkul\Logistics\Filament\Clusters\Reporting\Pages\ShipmentRegister;
use Webkul\Logistics\Filament\Clusters\Reporting\Pages\VehicleTripHistory;
use Webkul\Logistics\Models\Driver;
use Webkul\Logistics\Models\Expense;
use Webkul\Logistics\Models\ExpenseCategory;
use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Models\Trip;
use Webkul\Logistics\Models\Vehicle;

require_once __DIR__.'/../../Helpers/LogisticsHelper.php';

beforeEach(function () {
    LogisticsHelper::install();
});

/**
 * A shipment with billable charges and approved costs against it.
 */
function reportedShipment($company, float $revenue, float $cost, array $overrides = []): Shipment
{
    $shipment = LogisticsHelper::shipment($company, $overrides);

    if ($revenue > 0) {
        $shipment->charges()->create([
            'description' => 'Freight',
            'quantity'    => 1,
            'price_unit'  => $revenue,
            'is_billable' => true,
            'currency_id' => $company->currency_id,
        ]);
    }

    if ($cost > 0) {
        Expense::factory()->create([
            'company_id'  => $company->id,
            'shipment_id' => $shipment->id,
            'category_id' => ExpenseCategory::factory()->create()->id,
            'state'       => ExpenseState::APPROVED,
            'amount'      => $cost,
            'currency_id' => $company->currency_id,
        ]);
    }

    return $shipment->refresh();
}

it('sums revenue and costs per shipment', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    $profitable = reportedShipment($company, revenue: 1000, cost: 400);

    CompanyHelper::actingAsCompanyUser($company, [
        'page_logistics_shipment_profitability',
        'view_financials_logistics_shipment',
    ]);

    Livewire::test(ShipmentProfitability::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$profitable]);

    $row = ShipmentProfitability::class;

    // Read back through the page's own query, so the figures asserted are the
    // ones the page shows rather than a second calculation that might agree by
    // accident.
    $record = Livewire::test($row)->instance()->getTable()->getQuery()->find($profitable->getKey());

    expect((float) $record->revenue_total)->toBe(1000.0)
        ->and((float) $record->cost_total)->toBe(400.0);
});

it('counts approved and billed costs but not drafts', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    $shipment = reportedShipment($company, revenue: 500, cost: 100);

    // A claim nobody has approved must not move the margin.
    Expense::factory()->create([
        'company_id'  => $company->id,
        'shipment_id' => $shipment->id,
        'category_id' => ExpenseCategory::factory()->create()->id,
        'state'       => ExpenseState::DRAFT,
        'amount'      => 9000,
        'currency_id' => $company->currency_id,
    ]);

    CompanyHelper::actingAsCompanyUser($company, [
        'page_logistics_shipment_profitability',
        'view_financials_logistics_shipment',
    ]);

    $record = Livewire::test(ShipmentProfitability::class)
        ->instance()
        ->getTable()
        ->getQuery()
        ->find($shipment->getKey());

    expect((float) $record->cost_total)->toBe(100.0);
});

it('never reports another company’s shipments', function () {
    $mine = LogisticsHelper::enable(LogisticsHelper::company());
    $theirs = LogisticsHelper::enable(LogisticsHelper::company());

    $ours = reportedShipment($mine, revenue: 100, cost: 10);
    $theirsShipment = reportedShipment($theirs, revenue: 999, cost: 1);

    CompanyHelper::actingAsCompanyUser($mine, [
        'page_logistics_shipment_register',
        'page_logistics_shipment_profitability',
        'view_financials_logistics_shipment',
    ]);

    // A report is the worst place to cross the company boundary: it is where
    // people take numbers from and act on them.
    Livewire::test(ShipmentRegister::class)
        ->assertCanSeeTableRecords([$ours])
        ->assertCanNotSeeTableRecords([$theirsShipment]);

    Livewire::test(ShipmentProfitability::class)
        ->assertCanSeeTableRecords([$ours])
        ->assertCanNotSeeTableRecords([$theirsShipment]);
});

it('keeps profitability behind view_financials, not the page permission alone', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    CompanyHelper::actingAsCompanyUser($company, ['page_logistics_shipment_profitability']);

    expect(ShipmentProfitability::canAccess())->toBeFalse();

    CompanyHelper::actingAsCompanyUser($company, [
        'page_logistics_shipment_profitability',
        'view_financials_logistics_shipment',
    ]);

    expect(ShipmentProfitability::canAccess())->toBeTrue()
        // The register shows no money, so it needs only its page permission.
        ->and(ShipmentRegister::canAccess())->toBeFalse();
});

it('hides every report from a company that has not enabled Logistics', function () {
    $company = LogisticsHelper::company();

    CompanyHelper::actingAsCompanyUser($company, [
        'page_logistics_shipment_register',
        'page_logistics_delivery_performance',
        'page_logistics_shipment_profitability',
        'page_logistics_vehicle_trip_history',
        'page_logistics_driver_trip_history',
        'view_financials_logistics_shipment',
    ]);

    expect(ShipmentRegister::canAccess())->toBeFalse()
        ->and(DeliveryPerformance::canAccess())->toBeFalse()
        ->and(ShipmentProfitability::canAccess())->toBeFalse()
        ->and(VehicleTripHistory::canAccess())->toBeFalse()
        ->and(DriverTripHistory::canAccess())->toBeFalse();
});

it('scores a delivery against the date that was promised', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    $onTime = LogisticsHelper::shipment($company, [
        'state'                => ShipmentState::DELIVERED,
        'expected_delivery_at' => now()->subDays(2),
        'actual_delivery_at'   => now()->subDays(3),
    ]);

    $late = LogisticsHelper::shipment($company, [
        'state'                => ShipmentState::DELIVERED,
        'expected_delivery_at' => now()->subDays(3),
        'actual_delivery_at'   => now()->subDay(),
    ]);

    $failed = LogisticsHelper::shipment($company, [
        'state' => ShipmentState::FAILED_DELIVERY,
    ]);

    // Delivered, but nobody ever promised a date - scoring it on time would
    // invent a commitment and flatter the numbers.
    //
    // expected_delivery_at must be nulled explicitly: ShipmentFactory fills it
    // with now()+2 days, so without this the shipment has a promise after all
    // and the branch this test exists for is never reached.
    $unpromised = LogisticsHelper::shipment($company, [
        'state'                => ShipmentState::DELIVERED,
        'expected_delivery_at' => null,
        'actual_delivery_at'   => now(),
    ]);

    expect(DeliveryPerformance::outcome($onTime))->toBe('on-time')
        ->and(DeliveryPerformance::outcome($late))->toBe('late')
        ->and(DeliveryPerformance::outcome($failed))->toBe('failed')
        ->and(DeliveryPerformance::outcome($unpromised))->toBe('not-measured');
});

it('filters trips by vehicle and by driver', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    $vehicle = Vehicle::factory()->create(['company_id' => $company->id]);
    $otherVehicle = Vehicle::factory()->create(['company_id' => $company->id]);
    $driver = Driver::factory()->create(['company_id' => $company->id]);

    $mine = Trip::create([
        'company_id' => $company->id,
        'vehicle_id' => $vehicle->id,
        'driver_id'  => $driver->id,
    ]);

    $other = Trip::create([
        'company_id' => $company->id,
        'vehicle_id' => $otherVehicle->id,
    ]);

    CompanyHelper::actingAsCompanyUser($company, [
        'page_logistics_vehicle_trip_history',
        'page_logistics_driver_trip_history',
    ]);

    Livewire::test(VehicleTripHistory::class)
        ->assertCanSeeTableRecords([$mine, $other])
        ->filterTable('vehicle', $vehicle->id)
        ->assertCanSeeTableRecords([$mine])
        ->assertCanNotSeeTableRecords([$other]);

    Livewire::test(DriverTripHistory::class)
        ->filterTable('driver', $driver->id)
        ->assertCanSeeTableRecords([$mine])
        ->assertCanNotSeeTableRecords([$other]);
});

it('exports what the filters left on screen, not the whole table', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    $delivered = LogisticsHelper::shipment($company, ['state' => ShipmentState::DELIVERED]);
    $draft = LogisticsHelper::shipment($company, ['state' => ShipmentState::DRAFT]);

    CompanyHelper::actingAsCompanyUser($company, ['page_logistics_shipment_register']);

    // An export that ignored the filters would hand someone more rows than the
    // page ever showed them - the same concern as the company scope, one step
    // removed.
    //
    // getTableQueryForExport() is the exact method Filament's ExportAction
    // calls (CanExportRecords), and it applies the filters, the search and the
    // sort. Asserting on getTable()->getQuery() instead would pass no matter
    // what the filters did, because that is the unfiltered base query.
    $rows = Livewire::test(ShipmentRegister::class)
        ->filterTable('state', [ShipmentState::DELIVERED->value])
        ->instance()
        ->getTableQueryForExport()
        ->pluck('id')
        ->all();

    expect($rows)->toContain($delivered->getKey())
        ->and($rows)->not->toContain($draft->getKey());
});

it('generates the page permission each report asks for', function () {
    // Every other test here grants the permission by name, so all of them would
    // still pass if Shield generated a different name than the page checks for.
    // In production that combination is a report nobody can ever open: canAccess()
    // returns false for everyone, including a role with every box ticked, because
    // the permission the page names does not exist.
    //
    // The names come from PermissionManager's key builder (page + plugin +
    // class), not from Shield's default `view_<class>`, which is why they are
    // worth pinning down rather than assuming.
    FilamentHelper::bootAdminPanel();

    $generated = collect(FilamentShield::getPages())
        ->flatMap(fn (array $page): array => array_keys($page['permissions']))
        ->all();

    expect($generated)
        ->toContain('page_logistics_shipment_register')
        ->toContain('page_logistics_delivery_performance')
        ->toContain('page_logistics_shipment_profitability')
        ->toContain('page_logistics_vehicle_trip_history')
        ->toContain('page_logistics_driver_trip_history')
        // The cluster is excluded on purpose: it guards itself with the company
        // switch and has nothing of its own to permit.
        ->not->toContain('page_logistics_reporting');
});
