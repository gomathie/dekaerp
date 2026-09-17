<?php

namespace Webkul\Logistics\Exceptions;

use LogicException;

class CompanyMismatchException extends LogicException
{
    public static function between(string $record, string $related): self
    {
        return new self(__('logistics::exceptions.company-mismatch', ['record' => $record, 'related' => $related]));
    }
}
