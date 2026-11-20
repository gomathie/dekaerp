<?php

namespace Webkul\Logistics\Enums;

use Filament\Support\Contracts\HasLabel;

enum ShipmentEventType: string implements HasLabel
{
    case CREATED = 'created';

    case CONFIRMED = 'confirmed';

    case DRIVER_ASSIGNED = 'driver_assigned';

    case VEHICLE_ASSIGNED = 'vehicle_assigned';

    case TRIP_ASSIGNED = 'trip_assigned';

    case DISPATCHED = 'dispatched';

    case PICKUP_STARTED = 'pickup_started';

    case PICKED_UP = 'picked_up';

    case DEPARTED_ORIGIN = 'departed_origin';

    case IN_TRANSIT = 'in_transit';

    case ARRIVED_DESTINATION = 'arrived_destination';

    case OUT_FOR_DELIVERY = 'out_for_delivery';

    case DELIVERED = 'delivered';

    case DELIVERY_FAILED = 'delivery_failed';

    case DELIVERY_RETRIED = 'delivery_retried';

    case RETURNED = 'returned';

    case PUT_ON_HOLD = 'put_on_hold';

    case RELEASED = 'released';

    case CANCELLED = 'cancelled';

    case POD_CAPTURED = 'pod_captured';

    case INVOICE_CREATED = 'invoice_created';

    case POSITION_UPDATE = 'position_update';

    public static function options(): array
    {
        return [
            self::CREATED->value             => __('logistics::enums/shipment-event-type.created'),
            self::CONFIRMED->value           => __('logistics::enums/shipment-event-type.confirmed'),
            self::DRIVER_ASSIGNED->value     => __('logistics::enums/shipment-event-type.driver_assigned'),
            self::VEHICLE_ASSIGNED->value    => __('logistics::enums/shipment-event-type.vehicle_assigned'),
            self::TRIP_ASSIGNED->value       => __('logistics::enums/shipment-event-type.trip_assigned'),
            self::DISPATCHED->value          => __('logistics::enums/shipment-event-type.dispatched'),
            self::PICKUP_STARTED->value      => __('logistics::enums/shipment-event-type.pickup_started'),
            self::PICKED_UP->value           => __('logistics::enums/shipment-event-type.picked_up'),
            self::DEPARTED_ORIGIN->value     => __('logistics::enums/shipment-event-type.departed_origin'),
            self::IN_TRANSIT->value          => __('logistics::enums/shipment-event-type.in_transit'),
            self::ARRIVED_DESTINATION->value => __('logistics::enums/shipment-event-type.arrived_destination'),
            self::OUT_FOR_DELIVERY->value    => __('logistics::enums/shipment-event-type.out_for_delivery'),
            self::DELIVERED->value           => __('logistics::enums/shipment-event-type.delivered'),
            self::DELIVERY_FAILED->value     => __('logistics::enums/shipment-event-type.delivery_failed'),
            self::DELIVERY_RETRIED->value    => __('logistics::enums/shipment-event-type.delivery_retried'),
            self::RETURNED->value            => __('logistics::enums/shipment-event-type.returned'),
            self::PUT_ON_HOLD->value         => __('logistics::enums/shipment-event-type.put_on_hold'),
            self::RELEASED->value            => __('logistics::enums/shipment-event-type.released'),
            self::CANCELLED->value           => __('logistics::enums/shipment-event-type.cancelled'),
            self::POD_CAPTURED->value        => __('logistics::enums/shipment-event-type.pod_captured'),
            self::INVOICE_CREATED->value     => __('logistics::enums/shipment-event-type.invoice_created'),
            self::POSITION_UPDATE->value     => __('logistics::enums/shipment-event-type.position_update'),
        ];
    }

    public function getLabel(): string
    {
        return __('logistics::enums/shipment-event-type.'.$this->value);
    }
}
