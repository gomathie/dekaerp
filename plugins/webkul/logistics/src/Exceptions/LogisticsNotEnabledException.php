<?php

namespace Webkul\Logistics\Exceptions;

use RuntimeException;

class LogisticsNotEnabledException extends RuntimeException
{
    public static function forCompany(?int $companyId): self
    {
        return new self(__('logistics::exceptions.not-enabled', ['company' => $companyId ?? '-']));
    }
}
