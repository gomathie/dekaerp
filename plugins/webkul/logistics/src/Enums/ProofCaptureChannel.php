<?php

namespace Webkul\Logistics\Enums;

use Filament\Support\Contracts\HasLabel;

enum ProofCaptureChannel: string implements HasLabel
{
    case OFFICE = 'office';

    case STOP_LINK = 'stop_link';

    public static function options(): array
    {
        return [
            self::OFFICE->value    => __('logistics::enums/proof-capture-channel.office'),
            self::STOP_LINK->value => __('logistics::enums/proof-capture-channel.stop_link'),
        ];
    }

    public function getLabel(): string
    {
        return __('logistics::enums/proof-capture-channel.'.$this->value);
    }
}
