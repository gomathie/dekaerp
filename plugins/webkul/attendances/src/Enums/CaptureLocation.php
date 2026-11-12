<?php

namespace Webkul\Attendance\Enums;

use Filament\Support\Contracts\HasLabel;

enum CaptureLocation: string implements HasLabel
{
    case Off = 'off';

    case Optional = 'optional';

    case Required = 'required';

    public function getLabel(): string
    {
        return __('attendances::attendance.capture.'.$this->value);
    }
}
