<?php

namespace Webkul\Logistics\Policies;

use Webkul\Logistics\Models\Expense;
use Webkul\Security\Models\User;

class ExpensePolicy extends LogisticsPolicy
{
    protected function subject(): string
    {
        return 'expense';
    }

    public function approve(User $user, Expense $expense): bool
    {
        return $this->recordAbility($user, 'approve', $expense);
    }

    public function postBill(User $user, Expense $expense): bool
    {
        return $this->recordAbility($user, 'post_bill', $expense);
    }
}
