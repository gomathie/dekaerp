<?php

namespace Webkul\Logistics\Enums;

use Filament\Support\Contracts\HasLabel;

enum CapacityCheck: string implements HasLabel
{
    case WARN = 'warn';

    case OFF = 'off';

    public static function options(): array
    {
        return [
            self::WARN->value => __('logistics::enums/capacity-check.warn'),
            self::OFF->value  => __('logistics::enums/capacity-check.off'),
        ];
    }

    public function getLabel(): string
    {
        return __('logistics::enums/capacity-check.'.$this->value);
    }
}
