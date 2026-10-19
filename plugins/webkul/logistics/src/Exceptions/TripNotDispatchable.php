<?php

namespace Webkul\Logistics\Exceptions;

use RuntimeException;
use Webkul\Logistics\Models\Driver;
use Webkul\Logistics\Models\Vehicle;

/**
 * A trip cannot leave because of who or what is on it.
 *
 * These are refusals, not capacity warnings: an expired licence or an inactive
 * vehicle blocks the dispatch outright, while a capacity overrun only warns
 * (D5). Keep that distinction - see DispatchService::capacityWarnings().
 */
class TripNotDispatchable extends RuntimeException
{
    public static function noVehicle(): self
    {
        return new self(__('logistics::exceptions.trip-no-vehicle'));
    }

    public static function noDriver(): self
    {
        return new self(__('logistics::exceptions.trip-no-driver'));
    }

    public static function inactiveVehicle(Vehicle $vehicle): self
    {
        return new self(__('logistics::exceptions.trip-inactive-vehicle', [
            'vehicle' => $vehicle->registration_no ?? $vehicle->name ?? '#'.$vehicle->getKey(),
        ]));
    }

    public static function inactiveDriver(Driver $driver): self
    {
        return new self(__('logistics::exceptions.trip-inactive-driver', [
            'driver' => $driver->name ?? '#'.$driver->getKey(),
        ]));
    }

    public static function expiredLicense(Driver $driver): self
    {
        return new self(__('logistics::exceptions.trip-expired-license', [
            'driver' => $driver->name ?? '#'.$driver->getKey(),
            'date'   => optional($driver->license_expires_at)->toFormattedDateString() ?? '-',
        ]));
    }

    public static function shipmentNotConfirmed(string $shipment): self
    {
        return new self(__('logistics::exceptions.trip-shipment-not-confirmed', [
            'shipment' => $shipment,
        ]));
    }
}
