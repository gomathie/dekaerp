<?php

namespace Webkul\Logistics\Enums;

use Filament\Support\Contracts\HasLabel;

enum ExpensePaidBy: string implements HasLabel
{
    case COMPANY = 'company';

    case EMPLOYEE = 'employee';

    public static function options(): array
    {
        return [
            self::COMPANY->value  => __('logistics::enums/expense-paid-by.company'),
            self::EMPLOYEE->value => __('logistics::enums/expense-paid-by.employee'),
        ];
    }

    public function getLabel(): string
    {
        return __('logistics::enums/expense-paid-by.'.$this->value);
    }
}
