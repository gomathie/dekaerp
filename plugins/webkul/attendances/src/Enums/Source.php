<?php

namespace Webkul\Attendance\Enums;

enum Source: string
{
    case Banner = 'banner';

    case Kiosk = 'kiosk';

    case Manager = 'manager';

    case System = 'system';
}
