<?php

namespace Webkul\Logistics\Enums;

use Filament\Support\Contracts\HasLabel;

enum LineType: string implements HasLabel
{
    case Internal = 'internal';

    case External = 'external';

    public function getLabel(): string
    {
        return match ($this) {
            self::Internal => __('logistics::enums/line-type.internal'),
            self::External => __('logistics::enums/line-type.external'),
        };
    }
}
