<?php

namespace Webkul\Logistics\Policies;

class ExpenseCategoryPolicy extends ConfigurationPolicy
{
    protected function subject(): string
    {
        return 'expense::category';
    }
}
