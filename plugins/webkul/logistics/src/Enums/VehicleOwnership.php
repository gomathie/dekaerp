<?php

namespace Webkul\Logistics\Enums;

use Filament\Support\Contracts\HasLabel;

enum VehicleOwnership: string implements HasLabel
{
    case OWNED = 'owned';

    case LEASED = 'leased';

    case THIRD_PARTY = 'third_party';

    public static function options(): array
    {
        return [
            self::OWNED->value       => __('logistics::enums/vehicle-ownership.owned'),
            self::LEASED->value      => __('logistics::enums/vehicle-ownership.leased'),
            self::THIRD_PARTY->value => __('logistics::enums/vehicle-ownership.third_party'),
        ];
    }

    public function getLabel(): string
    {
        return __('logistics::enums/vehicle-ownership.'.$this->value);
    }
}
