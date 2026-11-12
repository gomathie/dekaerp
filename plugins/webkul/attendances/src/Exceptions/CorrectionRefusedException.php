<?php

namespace Webkul\Attendance\Exceptions;

use Illuminate\Validation\ValidationException;

class CorrectionRefusedException extends ValidationException
{
    public const REASON_REQUIRED = 'A correction needs a reason.';

    public const NOT_ALLOWED = 'You can only correct attendance for your own team.';

    public const NOT_CLOCKED_IN = 'You are not clocked in.';

    public const NO_EMPLOYEE = 'You do not have an employee record in this company.';

    public const CHECK_OUT_ORDER = 'Check-out has to be after check-in.';

    public static function reasonRequired(): self
    {
        return static::withMessages(['reason' => __(self::REASON_REQUIRED)]);
    }

    public static function notAllowed(): self
    {
        return static::withMessages(['attendance' => __(self::NOT_ALLOWED)]);
    }

    public static function notClockedIn(): self
    {
        return static::withMessages(['attendance' => __(self::NOT_CLOCKED_IN)]);
    }

    public static function noEmployee(): self
    {
        return static::withMessages(['employee' => __(self::NO_EMPLOYEE)]);
    }

    public static function checkOutOrder(): self
    {
        return static::withMessages(['check_out_at' => __(self::CHECK_OUT_ORDER)]);
    }
}
