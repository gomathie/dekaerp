<?php

namespace Webkul\Attendance\Exceptions;

use Illuminate\Validation\ValidationException;

class LocationRequiredException extends ValidationException
{
    public const MESSAGE = 'A location is required to clock in or out.';

    public static function missing(): self
    {
        return static::withMessages([
            'location' => __(self::MESSAGE),
        ]);
    }
}
