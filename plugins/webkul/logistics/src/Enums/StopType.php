<?php

namespace Webkul\Logistics\Enums;

use Filament\Support\Contracts\HasLabel;

enum StopType: string implements HasLabel
{
    case PICKUP = 'pickup';

    case DELIVERY = 'delivery';

    case VIA = 'via';

    public static function options(): array
    {
        return [
            self::PICKUP->value   => __('logistics::enums/stop-type.pickup'),
            self::DELIVERY->value => __('logistics::enums/stop-type.delivery'),
            self::VIA->value      => __('logistics::enums/stop-type.via'),
        ];
    }

    public function getLabel(): string
    {
        return __('logistics::enums/stop-type.'.$this->value);
    }
}
