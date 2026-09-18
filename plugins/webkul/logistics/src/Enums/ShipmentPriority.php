<?php

namespace Webkul\Logistics\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ShipmentPriority: string implements HasColor, HasLabel
{
    case LOW = 'low';

    case NORMAL = 'normal';

    case HIGH = 'high';

    case URGENT = 'urgent';

    public static function options(): array
    {
        return [
            self::LOW->value    => __('logistics::enums/shipment-priority.low'),
            self::NORMAL->value => __('logistics::enums/shipment-priority.normal'),
            self::HIGH->value   => __('logistics::enums/shipment-priority.high'),
            self::URGENT->value => __('logistics::enums/shipment-priority.urgent'),
        ];
    }

    public function getLabel(): string
    {
        return __('logistics::enums/shipment-priority.'.$this->value);
    }

    public function getColor(): string
    {
        return match ($this) {
            self::LOW    => 'gray',
            self::NORMAL => 'info',
            self::HIGH   => 'warning',
            self::URGENT => 'danger',
        };
    }
}
