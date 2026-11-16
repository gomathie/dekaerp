<?php

namespace Webkul\Attendance\Exceptions;

use RuntimeException;

class CorrectionImmutableException extends RuntimeException
{
    public const MESSAGE = 'A correction cannot be changed after it is written.';

    public function __construct()
    {
        parent::__construct(__(self::MESSAGE));
    }
}
