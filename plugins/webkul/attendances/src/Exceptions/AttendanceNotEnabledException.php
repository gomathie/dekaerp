<?php

namespace Webkul\Attendance\Exceptions;

use RuntimeException;

class AttendanceNotEnabledException extends RuntimeException
{
    public static function forCompany(?int $companyId): self
    {
        return new self(__('attendances::attendance.exceptions.not-enabled', [
            'company' => $companyId ?? '-',
        ]));
    }
}
