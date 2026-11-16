<?php

namespace Webkul\Logistics\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Webkul\Logistics\Enums\CapacityCheck;
use Webkul\Logistics\Enums\ShipmentState;
use Webkul\Logistics\Enums\StopState;
use Webkul\Logistics\Enums\StopType;
use Webkul\Logistics\Enums\TripState;
use Webkul\Logistics\Exceptions\InvalidTripTransition;
use Webkul\Logistics\Exceptions\TripNotDispatchable;
use Webkul\Logistics\Models\CompanySetting;
use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Models\Stop;
use Webkul\Logistics\Models\Trip;
use Webkul\Logistics\Support\LogisticsAccess;

/**
 * The only place a trip's state changes, and the only place shipments are put
 * onto a trip.
 *
 * Every method is atomic, authorises server-side, and refuses to move a trip
 * that has no vehicle or driver, an archived vehicle or driver, or a driver
 * whose licence has expired. Capacity is different: when the company sets
 * capacity_check to "warn" an overrun is reported and the dispatch still
 * proceeds (D5). Blocking on capacity would strand real deliveries over an
 * estimate, so it is deliberately advisory.
 *
 * Authorisation is repeated here rather than left to the Filament actions,
 * because the super-admin Gate::before bypass skips policies and because the
 * dispatch board and future telematics callers reach this service directly.
 */
class DispatchService
{
    public function __construct(protected ShipmentWorkflow $workflow) {}

    /**
     * Put confirmed shipments on a trip, schedule their stops, and move each to
     * AwaitingPickup.
     *
     * @param  array<int, Shipment>|Collection<int, Shipment>  $shipments
     * @return array<int, string> capacity warnings; empty when within capacity
     *                            or when the company has the check switched off
     */
    public function assign(Trip $trip, array|Collection $shipments, array $context = []): array
    {
        LogisticsAccess::ensureEnabled((int) $trip->company_id);

        $this->authorize('update', $trip);

        $shipments = collect($shipments);

        foreach ($shipments as $shipment) {
            if (! $shipment->state instanceof ShipmentState || $shipment->state !== ShipmentState::CONFIRMED) {
                throw TripNotDispatchable::shipmentNotConfirmed((string) $shipment->name);
            }
        }

        return DB::transaction(function () use ($trip, $shipments, $context): array {
            $sequence = (int) $trip->stops()->max('sequence');

            foreach ($shipments as $index => $shipment) {
                $trip->shipments()->syncWithoutDetaching([
                    $shipment->getKey() => ['leg_sequence' => $index + 1],
                ]);

                // The stop takes the shipment's company, never the session's.
                // A dispatcher working across companies would otherwise stamp
                // their own on another company's stop.
                $this->createStop($trip, $shipment, StopType::PICKUP, ++$sequence, $shipment->planned_pickup_at);
                $this->createStop($trip, $shipment, StopType::DELIVERY, ++$sequence, $shipment->expected_delivery_at);

                $this->workflow->transition($shipment, ShipmentState::AWAITING_PICKUP, $context + [
                    'ability' => 'assign',
                    'trip_id' => $trip->getKey(),
                ]);
            }

            if ($trip->state === TripState::PLANNED && $trip->vehicle_id && $trip->driver_id) {
                $this->transition($trip, TripState::ASSIGNED);
            }

            return $this->capacityWarnings($trip->refresh());
        });
    }

    public function dispatch(Trip $trip): Trip
    {
        return $this->move($trip, TripState::DISPATCHED, 'dispatch', requireCrew: true);
    }

    public function start(Trip $trip): Trip
    {
        return $this->move($trip, TripState::IN_PROGRESS, 'dispatch', requireCrew: true, stamp: 'actual_start_at');
    }

    public function complete(Trip $trip): Trip
    {
        return $this->move($trip, TripState::COMPLETED, 'complete', requireCrew: false, stamp: 'actual_end_at');
    }

    /**
     * Weight and volume against the vehicle's capacity, as advisory warnings.
     *
     * @return array<int, string>
     */
    public function capacityWarnings(Trip $trip): array
    {
        $setting = CompanySetting::forCompany((int) $trip->company_id);

        if (($setting->capacity_check ?? CapacityCheck::WARN) === CapacityCheck::OFF) {
            return [];
        }

        $vehicle = $trip->vehicle;

        if (! $vehicle) {
            return [];
        }

        $warnings = [];

        $weight = (float) $trip->shipments()->sum('total_weight_kg');
        $volume = (float) $trip->shipments()->sum('total_volume_m3');

        if ($vehicle->capacity_kg && $weight > (float) $vehicle->capacity_kg) {
            $warnings[] = __('logistics::dispatch.capacity.weight', [
                'load'     => number_format($weight, 2),
                'capacity' => number_format((float) $vehicle->capacity_kg, 2),
            ]);
        }

        if ($vehicle->capacity_m3 && $volume > (float) $vehicle->capacity_m3) {
            $warnings[] = __('logistics::dispatch.capacity.volume', [
                'load'     => number_format($volume, 2),
                'capacity' => number_format((float) $vehicle->capacity_m3, 2),
            ]);
        }

        return $warnings;
    }

    /**
     * The refusals that stop a trip leaving. Capacity is not among them.
     */
    public function assertDispatchable(Trip $trip): void
    {
        $vehicle = $trip->vehicle;
        $driver = $trip->driver;

        if (! $vehicle) {
            throw TripNotDispatchable::noVehicle();
        }

        if (! $driver) {
            throw TripNotDispatchable::noDriver();
        }

        if (! $vehicle->is_active) {
            throw TripNotDispatchable::inactiveVehicle($vehicle);
        }

        if (! $driver->is_active) {
            throw TripNotDispatchable::inactiveDriver($driver);
        }

        if ($driver->isLicenseExpired()) {
            throw TripNotDispatchable::expiredLicense($driver);
        }
    }

    protected function move(Trip $trip, TripState $to, string $ability, bool $requireCrew, ?string $stamp = null): Trip
    {
        LogisticsAccess::ensureEnabled((int) $trip->company_id);

        $this->authorize($ability, $trip);

        if ($requireCrew) {
            $this->assertDispatchable($trip);
        }

        return DB::transaction(function () use ($trip, $to, $stamp): Trip {
            $this->transition($trip, $to, $stamp);

            return $trip;
        });
    }

    protected function transition(Trip $trip, TripState $to, ?string $stamp = null): void
    {
        $from = $trip->state;

        if (! $from->canTransitionTo($to)) {
            throw InvalidTripTransition::between($from, $to);
        }

        $trip->state = $to;

        if ($stamp) {
            $trip->{$stamp} ??= now();
        }

        $trip->save();
    }

    protected function createStop(Trip $trip, Shipment $shipment, StopType $type, int $sequence, $plannedAt): Stop
    {
        return Stop::create([
            'trip_id'            => $trip->getKey(),
            'shipment_id'        => $shipment->getKey(),
            'company_id'         => $shipment->company_id,
            'type'               => $type,
            'state'              => StopState::PENDING,
            'sequence'           => $sequence,
            'planned_arrival_at' => $plannedAt,
        ]);
    }

    protected function authorize(string $ability, Trip $trip): void
    {
        if (! Auth::check()) {
            return;
        }

        Gate::authorize($ability, $trip);
    }
}
