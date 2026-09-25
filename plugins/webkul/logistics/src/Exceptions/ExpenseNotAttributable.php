<?php

namespace Webkul\Logistics\Exceptions;

use RuntimeException;

/**
 * An expense that points at nothing.
 *
 * Every cost has to hang off a shipment, a trip or a vehicle: that is what makes
 * it attributable, what the shipment margin is calculated from, and what WP-8b
 * needs before it can put the cost on a vendor bill. The expense form already
 * requires one of the three, but a form is not a boundary - an API write or an
 * import reaches the service directly.
 */
class ExpenseNotAttributable extends RuntimeException
{
    public static function make(): self
    {
        return new self(__('logistics::exceptions.expense-not-attributable'));
    }
}
