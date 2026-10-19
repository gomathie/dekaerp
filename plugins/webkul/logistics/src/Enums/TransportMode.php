<?php

namespace Webkul\Logistics\Enums;

use Filament\Support\Contracts\HasLabel;

enum TransportMode: string implements HasLabel
{
    case ROAD = 'road';

    case AIR = 'air';

    case SEA = 'sea';

    case RAIL = 'rail';

    case MULTIMODAL = 'multimodal';

    public static function options(): array
    {
        return [
            self::ROAD->value       => __('logistics::enums/transport-mode.road'),
            self::AIR->value        => __('logistics::enums/transport-mode.air'),
            self::SEA->value        => __('logistics::enums/transport-mode.sea'),
            self::RAIL->value       => __('logistics::enums/transport-mode.rail'),
            self::MULTIMODAL->value => __('logistics::enums/transport-mode.multimodal'),
        ];
    }

    public function getLabel(): string
    {
        return __('logistics::enums/transport-mode.'.$this->value);
    }
}
