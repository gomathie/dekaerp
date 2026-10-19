<?php

namespace Webkul\Logistics\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Webkul\Logistics\Enums\ShipmentEventSource;
use Webkul\Logistics\Enums\ShipmentEventType;
use Webkul\Logistics\Enums\ShipmentState;
use Webkul\Logistics\Exceptions\InvalidShipmentTransition;
use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Models\ShipmentEvent;
use Webkul\Logistics\Support\LogisticsAccess;

/**
 * The only place a shipment's state changes.
 *
 * Every transition is checked against ShipmentState::transitions(), recorded as a
 * shipment event, and logged to chatter by HasLogActivity (state is one of the
 * logged attributes). Callers never assign `state` directly.
 *
 * Authorisation is repeated here because the super-admin Gate::before bypass
 * skips policies, and because dispatch, delivery and telematics call this
 * service outside the Filament actions.
 */
class ShipmentWorkflow
{
    /**
     * The event written when a shipment reaches a state.
     *
     * @var array<string, ShipmentEventType>
     */
    protected const EVENTS = [
        'draft'            => ShipmentEventType::CREATED,
        'confirmed'        => ShipmentEventType::CONFIRMED,
        'awaiting_pickup'  => ShipmentEventType::TRIP_ASSIGNED,
        'picked_up'        => ShipmentEventType::PICKED_UP,
        'in_transit'       => ShipmentEventType::IN_TRANSIT,
        'out_for_delivery' => ShipmentEventType::OUT_FOR_DELIVERY,
        'delivered'        => ShipmentEventType::DELIVERED,
        'on_hold'          => ShipmentEventType::PUT_ON_HOLD,
        'failed_delivery'  => ShipmentEventType::DELIVERY_FAILED,
        'returned'         => ShipmentEventType::RETURNED,
        'cancelled'        => ShipmentEventType::CANCELLED,
    ];

    /**
     * @param  array<string, mixed>  $context  ability, notes, source, user_id, occurred_at,
     *                                         trip_id, stop_id, location_label, latitude,
     *                                         longitude, metadata
     */
    public function transition(Shipment $shipment, ShipmentState $to, array $context = []): Shipment
    {
        LogisticsAccess::ensureEnabled($shipment->company_id);

        $this->authorize($shipment, $context);

        $from = $shipment->state;

        if (! $from->canTransitionTo($to)) {
            throw InvalidShipmentTransition::between($from, $to);
        }

        return DB::transaction(function () use ($shipment, $to, $context): Shipment {
            $shipment->state = $to;

            $this->stampTimestamps($shipment, $to);

            $shipment->save();

            $this->recordEvent($shipment, self::EVENTS[$to->value], $context);

            return $shipment;
        });
    }

    public function confirm(Shipment $shipment, array $context = []): Shipment
    {
        return $this->transition($shipment, ShipmentState::CONFIRMED, $context + ['ability' => 'confirm']);
    }

    public function hold(Shipment $shipment, array $context = []): Shipment
    {
        return $this->transition($shipment, ShipmentState::ON_HOLD, $context + ['ability' => 'update']);
    }

    public function release(Shipment $shipment, array $context = []): Shipment
    {
        return $this->transition($shipment, ShipmentState::CONFIRMED, $context + ['ability' => 'update']);
    }

    public function cancel(Shipment $shipment, array $context = []): Shipment
    {
        return $this->transition($shipment, ShipmentState::CANCELLED, $context + ['ability' => 'cancel']);
    }

    /**
     * Write a timeline entry without changing the state (arrivals, positions, notes).
     *
     * @param  array<string, mixed>  $context
     */
    public function recordEvent(Shipment $shipment, ShipmentEventType $type, array $context = []): ShipmentEvent
    {
        return ShipmentEvent::create([
            'shipment_id'    => $shipment->id,
            'company_id'     => $shipment->company_id,
            'type'           => $type,
            'source'         => $context['source'] ?? ShipmentEventSource::USER,
            'user_id'        => $context['user_id'] ?? Auth::id(),
            'occurred_at'    => $context['occurred_at'] ?? now(),
            'notes'          => $context['notes'] ?? null,
            'trip_id'        => $context['trip_id'] ?? null,
            'stop_id'        => $context['stop_id'] ?? null,
            'location_label' => $context['location_label'] ?? null,
            'latitude'       => $context['latitude'] ?? null,
            'longitude'      => $context['longitude'] ?? null,
            'metadata'       => $context['metadata'] ?? null,
        ]);
    }

    protected function stampTimestamps(Shipment $shipment, ShipmentState $to): void
    {
        if ($to === ShipmentState::PICKED_UP) {
            $shipment->actual_pickup_at ??= now();
        }

        if ($to === ShipmentState::DELIVERED) {
            $shipment->actual_delivery_at ??= now();
        }
    }

    /**
     * @param  array<string, mixed>  $context
     */
    protected function authorize(Shipment $shipment, array $context): void
    {
        $ability = $context['ability'] ?? null;

        // System and telematics callers have no user to authorise.
        if (! $ability || ($context['source'] ?? null) === ShipmentEventSource::SYSTEM || ! Auth::check()) {
            return;
        }

        Gate::authorize($ability, $shipment);
    }
}
