<?php

use Illuminate\Support\Facades\DB;
use Webkul\Logistics\Enums\CapacityCheck;
use Webkul\Logistics\Enums\ShipmentState;
use Webkul\Logistics\Enums\StopType;
use Webkul\Logistics\Enums\TripState;
use Webkul\Logistics\Exceptions\InvalidTripTransition;
use Webkul\Logistics\Exceptions\TripNotDispatchable;
use Webkul\Logistics\Models\CompanySetting;
use Webkul\Logistics\Models\Driver;
use Webkul\Logistics\Models\Stop;
use Webkul\Logistics\Models\Trip;
use Webkul\Logistics\Models\Vehicle;
use Webkul\Support\Models\Scopes\CompanyScope;
use Webkul\Logistics\Services\DispatchService;
use Webkul\Logistics\Services\ShipmentWorkflow;

require_once __DIR__.'/../../Helpers/LogisticsHelper.php';

beforeEach(function () {
    LogisticsHelper::install();
});

/**
 * A trip with an active vehicle and an in-date driver, ready to dispatch.
 */
function dispatchReadyTrip($company, array $vehicle = [], array $driver = []): Trip
{
    $vehicleModel = Vehicle::factory()->create(array_merge([
        'company_id'  => $company->id,
        'is_active'   => true,
        'capacity_kg' => 10000,
        'capacity_m3' => 50,
    ], $vehicle));

    $driverModel = Driver::factory()->create(array_merge([
        'company_id'         => $company->id,
        'is_active'          => true,
        'license_expires_at' => now()->addYear(),
    ], $driver));

    return Trip::create([
        'company_id' => $company->id,
        'state'      => TripState::PLANNED,
        'vehicle_id' => $vehicleModel->id,
        'driver_id'  => $driverModel->id,
    ]);
}

function confirmedShipment($company, array $overrides = [])
{
    $shipment = LogisticsHelper::shipment($company, $overrides);

    app(ShipmentWorkflow::class)->transition($shipment, ShipmentState::CONFIRMED, [
        'source' => Webkul\Logistics\Enums\ShipmentEventSource::SYSTEM,
    ]);

    return $shipment->refresh();
}

it('consolidates two shipments onto one trip and schedules their stops', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $trip = dispatchReadyTrip($company);

    $first = confirmedShipment($company);
    $second = confirmedShipment($company);

    CompanyHelper::actingAsCompanyUser($company, [
        'update_logistics_trip',
        'assign_logistics_shipment',
    ]);

    $warnings = app(DispatchService::class)->assign($trip, [$first, $second]);

    $trip->refresh();

    expect($warnings)->toBe([])
        ->and($trip->shipments()->count())->toBe(2)
        // A pickup and a delivery stop for each shipment.
        ->and($trip->stops()->count())->toBe(4)
        ->and($trip->stops()->where('type', StopType::PICKUP)->count())->toBe(2)
        ->and($first->refresh()->state)->toBe(ShipmentState::AWAITING_PICKUP)
        ->and($second->refresh()->state)->toBe(ShipmentState::AWAITING_PICKUP)
        // Crew is set, so attaching moved the trip off PLANNED.
        ->and($trip->state)->toBe(TripState::ASSIGNED);
});

it('gives every stop the shipment’s company, not the session’s', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $other = LogisticsHelper::enable(LogisticsHelper::company());

    $trip = dispatchReadyTrip($company);
    $shipment = confirmedShipment($company);

    // Acting in a different company while assigning.
    CompanyHelper::actingAsCompanyUser([$company, $other], [
        'update_logistics_trip',
        'assign_logistics_shipment',
    ], activeIds: [$other->id]);

    app(DispatchService::class)->assign($trip, [$shipment]);

    // Two things at once. Through the company scope the stops are invisible,
    // because the user is active in the other company - that is the scope
    // doing its job. Reading without the scope shows what was actually
    // written: the shipment's company, never the session's.
    expect($trip->stops()->count())->toBe(0);

    $written = Stop::withoutGlobalScope(CompanyScope::class)
        ->where('trip_id', $trip->getKey())
        ->pluck('company_id')
        ->unique()
        ->all();

    expect($written)->toBe([$company->id]);
});

it('refuses to attach a shipment that is not confirmed', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $trip = dispatchReadyTrip($company);
    $draft = LogisticsHelper::shipment($company);

    CompanyHelper::actingAsCompanyUser($company, ['update_logistics_trip']);

    expect(fn () => app(DispatchService::class)->assign($trip, [$draft]))
        ->toThrow(TripNotDispatchable::class);

    expect($trip->shipments()->count())->toBe(0)
        ->and($trip->stops()->count())->toBe(0);
});

it('refuses to dispatch without a vehicle, without a driver, or on an expired licence', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    CompanyHelper::actingAsCompanyUser($company, ['dispatch_logistics_trip', 'update_logistics_trip']);

    $service = app(DispatchService::class);

    $noVehicle = dispatchReadyTrip($company);
    $noVehicle->forceFill(['vehicle_id' => null, 'state' => TripState::ASSIGNED])->save();
    expect(fn () => $service->dispatch($noVehicle))->toThrow(TripNotDispatchable::class);

    $noDriver = dispatchReadyTrip($company);
    $noDriver->forceFill(['driver_id' => null, 'state' => TripState::ASSIGNED])->save();
    expect(fn () => $service->dispatch($noDriver))->toThrow(TripNotDispatchable::class);

    $archivedVehicle = dispatchReadyTrip($company, vehicle: ['is_active' => false]);
    $archivedVehicle->forceFill(['state' => TripState::ASSIGNED])->save();
    expect(fn () => $service->dispatch($archivedVehicle))->toThrow(TripNotDispatchable::class);

    $expired = dispatchReadyTrip($company, driver: ['license_expires_at' => now()->subDay()]);
    $expired->forceFill(['state' => TripState::ASSIGNED])->save();
    expect(fn () => $service->dispatch($expired))->toThrow(TripNotDispatchable::class);

    // None of them moved.
    expect($expired->refresh()->state)->toBe(TripState::ASSIGNED);
});

it('warns about an overloaded vehicle but still dispatches it', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    CompanySetting::forCompany($company->id)->forceFill(['capacity_check' => CapacityCheck::WARN])->save();

    $trip = dispatchReadyTrip($company, vehicle: ['capacity_kg' => 100, 'capacity_m3' => 1]);
    $heavy = confirmedShipment($company, ['total_weight_kg' => 500, 'total_volume_m3' => 5]);

    CompanyHelper::actingAsCompanyUser($company, [
        'update_logistics_trip',
        'assign_logistics_shipment',
        'dispatch_logistics_trip',
    ]);

    $service = app(DispatchService::class);

    $warnings = $service->assign($trip, [$heavy]);

    expect($warnings)->not->toBeEmpty();

    // Over capacity is advisory (D5): the dispatch still succeeds.
    $service->dispatch($trip->refresh());

    expect($trip->refresh()->state)->toBe(TripState::DISPATCHED);
});

it('leaves capacity unchecked when the company switched it off', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    CompanySetting::forCompany($company->id)->forceFill(['capacity_check' => CapacityCheck::OFF])->save();

    $trip = dispatchReadyTrip($company, vehicle: ['capacity_kg' => 1, 'capacity_m3' => 1]);
    $heavy = confirmedShipment($company, ['total_weight_kg' => 500, 'total_volume_m3' => 5]);

    CompanyHelper::actingAsCompanyUser($company, [
        'update_logistics_trip',
        'assign_logistics_shipment',
    ]);

    expect(app(DispatchService::class)->assign($trip, [$heavy]))->toBe([]);
});

it('refuses a trip transition the state machine does not allow', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $trip = dispatchReadyTrip($company);

    CompanyHelper::actingAsCompanyUser($company, ['complete_logistics_trip']);

    // PLANNED cannot jump straight to COMPLETED.
    expect(fn () => app(DispatchService::class)->complete($trip))
        ->toThrow(InvalidTripTransition::class);

    expect($trip->refresh()->state)->toBe(TripState::PLANNED);
});

it('refuses to dispatch for a user without the dispatch permission', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $trip = dispatchReadyTrip($company);
    $trip->forceFill(['state' => TripState::ASSIGNED])->save();

    CompanyHelper::actingAsCompanyUser($company, ['view_any_logistics_trip']);

    expect(fn () => app(DispatchService::class)->dispatch($trip))
        ->toThrow(Illuminate\Auth\Access\AuthorizationException::class);

    expect($trip->refresh()->state)->toBe(TripState::ASSIGNED);
});

it('never lets one company’s trip take another company’s shipment', function () {
    $a = LogisticsHelper::enable(LogisticsHelper::company());
    $b = LogisticsHelper::enable(LogisticsHelper::company());

    $trip = dispatchReadyTrip($a);
    $theirs = confirmedShipment($b);

    CompanyHelper::actingAsCompanyUser($a, [
        'update_logistics_trip',
        'assign_logistics_shipment',
    ]);

    // The company scope hides it entirely: it cannot even be read to attach.
    expect(Webkul\Logistics\Models\Shipment::query()->whereKey($theirs->id)->exists())->toBeFalse();
});

it('builds the dispatch board with a fixed number of queries', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    foreach (range(1, 5) as $ignored) {
        confirmedShipment($company);
    }

    CompanyHelper::actingAsCompanyUser($company, ['view_any_logistics_shipment']);

    $statements = [];

    DB::listen(function ($query) use (&$statements): void {
        $statements[] = $query->sql;
    });

    $board = Webkul\Logistics\Models\Shipment::query()
        ->with(['customer:id,name'])
        ->where('state', ShipmentState::CONFIRMED)
        ->whereDoesntHave('trips')
        ->get();

    $partnerQueries = collect($statements)
        ->filter(fn (string $sql): bool => str_contains($sql, '"partners_partners"'))
        ->count();

    // Eager loaded: one query for every customer, not one per row.
    expect($board)->toHaveCount(5)
        ->and($partnerQueries)->toBeLessThanOrEqual(1);
});
