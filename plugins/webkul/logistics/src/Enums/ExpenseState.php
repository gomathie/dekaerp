<?php

namespace Webkul\Logistics\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ExpenseState: string implements HasColor, HasLabel
{
    case DRAFT = 'draft';

    case SUBMITTED = 'submitted';

    case APPROVED = 'approved';

    case REJECTED = 'rejected';

    case BILLED = 'billed';

    public static function options(): array
    {
        return [
            self::DRAFT->value => __('logistics::enums/expense-state.draft'),
            self::SUBMITTED->value => __('logistics::enums/expense-state.submitted'),
            self::APPROVED->value => __('logistics::enums/expense-state.approved'),
            self::REJECTED->value => __('logistics::enums/expense-state.rejected'),
            self::BILLED->value => __('logistics::enums/expense-state.billed'),
        ];
    }

    public static function transitions(): array
    {
        return [
            self::DRAFT->value => [self::SUBMITTED],
            self::SUBMITTED->value => [self::APPROVED, self::REJECTED],
            self::APPROVED->value => [self::BILLED],
            self::REJECTED->value => [self::DRAFT],
            self::BILLED->value => [],
        ];
    }

    public function canTransitionTo(self $to): bool
    {
        return in_array($to, static::transitions()[$this->value], true);
    }

    public function getLabel(): string
    {
        return __('logistics::enums/expense-state.'.$this->value);
    }

    public function getColor(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::SUBMITTED => 'info',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
            self::BILLED => 'primary',
        };
    }
}
