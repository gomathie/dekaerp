<?php

namespace Webkul\Sale\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/**
 * A mark on a quotation, separate from its state. Sales figures still
 * count confirmed orders (`OrderState::SALE`); this label does not.
 */
enum QuotationOutcome: string implements HasColor, HasLabel
{
    case Won = 'won';

    case Lost = 'lost';

    public function getLabel(): string
    {
        return __('sales::enums/quotation-outcome.'.$this->value);
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Won  => 'success',
            self::Lost => 'danger',
        };
    }
}
