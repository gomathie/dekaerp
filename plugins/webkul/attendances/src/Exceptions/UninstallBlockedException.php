<?php

namespace Webkul\Attendance\Exceptions;

use RuntimeException;

class UninstallBlockedException extends RuntimeException
{
    public static function rowsExist(int $count): self
    {
        return new self(__('attendances::attendance.exceptions.uninstall-blocked', [
            'count' => $count,
        ]));
    }
}
