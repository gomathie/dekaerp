<?php

namespace Webkul\Logistics\Exceptions;

use RuntimeException;

class NothingToInvoice extends RuntimeException
{
    public static function forShipment(string $shipment): self
    {
        return new self(__('logistics::exceptions.nothing-to-invoice', [
            'shipment' => $shipment,
        ]));
    }
}
