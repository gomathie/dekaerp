<?php

namespace Webkul\Invoice\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Invoice\Models\Expense;
use Webkul\Security\Models\User;

/**
 * Expenses (plan #10 Z1). The Invoices module switch is checked first, per
 * record company (ChecksInvoiceEntitlement). The super-admin Gate::before
 * skips this policy, so ExpenseRecorder repeats the company checks itself.
 * No bulk delete: each delete reverses a posted entry and needs a reason.
 */
class ExpensePolicy
{
    use Concerns\ChecksInvoiceEntitlement, HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_invoice_expense');
    }

    public function view(User $user, Expense $expense): bool
    {
        return $user->can('view_invoice_expense');
    }

    public function create(User $user): bool
    {
        return $user->can('create_invoice_expense');
    }

    public function update(User $user, Expense $expense): bool
    {
        return $user->can('update_invoice_expense');
    }

    public function delete(User $user, Expense $expense): bool
    {
        return $user->can('delete_invoice_expense');
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
