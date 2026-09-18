<?php

namespace Webkul\Logistics\Exceptions;

use RuntimeException;
use Webkul\Logistics\Enums\ShipmentState;

class InvalidShipmentTransition extends RuntimeException
{
    public static function between(ShipmentState $from, ShipmentState $to): self
    {
        return new self(__('logistics::exceptions.invalid-transition', [
            'from' => $from->value,
            'to'   => $to->value,
        ]));
    }
}
