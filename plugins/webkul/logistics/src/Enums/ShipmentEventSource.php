<?php

namespace Webkul\Logistics\Enums;

use Filament\Support\Contracts\HasLabel;

enum ShipmentEventSource: string implements HasLabel
{
    case USER = 'user';

    case SYSTEM = 'system';

    case TELEMATICS = 'telematics';

    public static function options(): array
    {
        return [
            self::USER->value       => __('logistics::enums/shipment-event-source.user'),
            self::SYSTEM->value     => __('logistics::enums/shipment-event-source.system'),
            self::TELEMATICS->value => __('logistics::enums/shipment-event-source.telematics'),
        ];
    }

    public function getLabel(): string
    {
        return __('logistics::enums/shipment-event-source.'.$this->value);
    }
}
