<?php

namespace Webkul\Logistics\Exceptions;

use RuntimeException;
use Webkul\Logistics\Enums\TripState;

class InvalidTripTransition extends RuntimeException
{
    public static function between(TripState $from, TripState $to): self
    {
        return new self(__('logistics::exceptions.invalid-trip-transition', [
            'from' => $from->value,
            'to'   => $to->value,
        ]));
    }
}
