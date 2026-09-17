<?php

namespace Webkul\Logistics\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum StopState: string implements HasColor, HasLabel
{
    case PENDING = 'pending';

    case ARRIVED = 'arrived';

    case DEPARTED = 'departed';

    case SKIPPED = 'skipped';

    public static function options(): array
    {
        return [
            self::PENDING->value => __('logistics::enums/stop-state.pending'),
            self::ARRIVED->value => __('logistics::enums/stop-state.arrived'),
            self::DEPARTED->value => __('logistics::enums/stop-state.departed'),
            self::SKIPPED->value => __('logistics::enums/stop-state.skipped'),
        ];
    }

    public function getLabel(): string
    {
        return __('logistics::enums/stop-state.'.$this->value);
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'gray',
            self::ARRIVED => 'info',
            self::DEPARTED => 'success',
            self::SKIPPED => 'warning',
        };
    }
}
