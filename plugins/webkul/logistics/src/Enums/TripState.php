<?php

namespace Webkul\Logistics\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TripState: string implements HasColor, HasLabel
{
    case PLANNED = 'planned';

    case ASSIGNED = 'assigned';

    case DISPATCHED = 'dispatched';

    case IN_PROGRESS = 'in_progress';

    case COMPLETED = 'completed';

    case CANCELLED = 'cancelled';

    public static function options(): array
    {
        return [
            self::PLANNED->value     => __('logistics::enums/trip-state.planned'),
            self::ASSIGNED->value    => __('logistics::enums/trip-state.assigned'),
            self::DISPATCHED->value  => __('logistics::enums/trip-state.dispatched'),
            self::IN_PROGRESS->value => __('logistics::enums/trip-state.in_progress'),
            self::COMPLETED->value   => __('logistics::enums/trip-state.completed'),
            self::CANCELLED->value   => __('logistics::enums/trip-state.cancelled'),
        ];
    }

    public static function transitions(): array
    {
        return [
            self::PLANNED->value     => [self::ASSIGNED, self::CANCELLED],
            self::ASSIGNED->value    => [self::DISPATCHED, self::CANCELLED],
            self::DISPATCHED->value  => [self::IN_PROGRESS, self::CANCELLED],
            self::IN_PROGRESS->value => [self::COMPLETED],
            self::COMPLETED->value   => [],
            self::CANCELLED->value   => [],
        ];
    }

    public function canTransitionTo(self $to): bool
    {
        return in_array($to, self::transitions()[$this->value], true);
    }

    public function getLabel(): string
    {
        return __('logistics::enums/trip-state.'.$this->value);
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PLANNED     => 'gray',
            self::ASSIGNED    => 'info',
            self::DISPATCHED  => 'primary',
            self::IN_PROGRESS => 'warning',
            self::COMPLETED   => 'success',
            self::CANCELLED   => 'danger',
        };
    }
}
