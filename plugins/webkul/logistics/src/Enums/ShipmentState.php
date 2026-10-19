<?php

namespace Webkul\Logistics\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ShipmentState: string implements HasColor, HasLabel
{
    case DRAFT = 'draft';

    case CONFIRMED = 'confirmed';

    case AWAITING_PICKUP = 'awaiting_pickup';

    case PICKED_UP = 'picked_up';

    case IN_TRANSIT = 'in_transit';

    case OUT_FOR_DELIVERY = 'out_for_delivery';

    case DELIVERED = 'delivered';

    case ON_HOLD = 'on_hold';

    case FAILED_DELIVERY = 'failed_delivery';

    case RETURNED = 'returned';

    case CANCELLED = 'cancelled';

    public static function options(): array
    {
        return [
            self::DRAFT->value            => __('logistics::enums/shipment-state.draft'),
            self::CONFIRMED->value        => __('logistics::enums/shipment-state.confirmed'),
            self::AWAITING_PICKUP->value  => __('logistics::enums/shipment-state.awaiting_pickup'),
            self::PICKED_UP->value        => __('logistics::enums/shipment-state.picked_up'),
            self::IN_TRANSIT->value       => __('logistics::enums/shipment-state.in_transit'),
            self::OUT_FOR_DELIVERY->value => __('logistics::enums/shipment-state.out_for_delivery'),
            self::DELIVERED->value        => __('logistics::enums/shipment-state.delivered'),
            self::ON_HOLD->value          => __('logistics::enums/shipment-state.on_hold'),
            self::FAILED_DELIVERY->value  => __('logistics::enums/shipment-state.failed_delivery'),
            self::RETURNED->value         => __('logistics::enums/shipment-state.returned'),
            self::CANCELLED->value        => __('logistics::enums/shipment-state.cancelled'),
        ];
    }

    public static function transitions(): array
    {
        return [
            self::DRAFT->value            => [self::CONFIRMED, self::CANCELLED],
            self::CONFIRMED->value        => [self::AWAITING_PICKUP, self::ON_HOLD, self::CANCELLED],
            self::AWAITING_PICKUP->value  => [self::PICKED_UP, self::ON_HOLD, self::CANCELLED],
            self::PICKED_UP->value        => [self::IN_TRANSIT],
            self::IN_TRANSIT->value       => [self::OUT_FOR_DELIVERY, self::ON_HOLD],
            self::OUT_FOR_DELIVERY->value => [self::DELIVERED, self::FAILED_DELIVERY],
            self::DELIVERED->value        => [],
            self::ON_HOLD->value          => [self::CONFIRMED],
            self::FAILED_DELIVERY->value  => [self::OUT_FOR_DELIVERY, self::RETURNED],
            self::RETURNED->value         => [],
            self::CANCELLED->value        => [],
        ];
    }

    public function canTransitionTo(self $to): bool
    {
        return in_array($to, self::transitions()[$this->value], true);
    }

    public function getLabel(): string
    {
        return __('logistics::enums/shipment-state.'.$this->value);
    }

    public function getColor(): string
    {
        return match ($this) {
            self::DRAFT            => 'gray',
            self::CONFIRMED        => 'info',
            self::AWAITING_PICKUP  => 'info',
            self::PICKED_UP        => 'primary',
            self::IN_TRANSIT       => 'primary',
            self::OUT_FOR_DELIVERY => 'warning',
            self::DELIVERED        => 'success',
            self::ON_HOLD          => 'warning',
            self::FAILED_DELIVERY  => 'danger',
            self::RETURNED         => 'danger',
            self::CANCELLED        => 'gray',
        };
    }
}
