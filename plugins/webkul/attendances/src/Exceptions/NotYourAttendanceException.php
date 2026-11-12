<?php

namespace Webkul\Attendance\Exceptions;

use RuntimeException;

class NotYourAttendanceException extends RuntimeException
{
    public const MESSAGE = 'You can only clock yourself in or out.';

    public function __construct()
    {
        parent::__construct(__(self::MESSAGE));
    }
}
