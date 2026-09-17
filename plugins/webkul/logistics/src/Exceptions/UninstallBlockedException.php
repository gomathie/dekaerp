<?php

namespace Webkul\Logistics\Exceptions;

use RuntimeException;

class UninstallBlockedException extends RuntimeException
{
    public static function shipmentsExist(int $count): self
    {
        return new self(__('logistics::exceptions.uninstall-blocked', ['count' => $count]));
    }
}
