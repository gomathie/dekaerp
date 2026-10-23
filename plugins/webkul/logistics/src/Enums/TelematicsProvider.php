<?php

namespace Webkul\Logistics\Enums;

use Filament\Support\Contracts\HasLabel;

enum TelematicsProvider: string implements HasLabel
{
    case PILOT = 'pilot';

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case): array => [$case->value => $case->getLabel()])
            ->all();
    }

    public function getLabel(): string
    {
        return __('logistics::enums/telematics-provider.'.$this->value);
    }
}
