<?php

namespace Webkul\Invoice\Policies;

use Webkul\Invoice\Models\RecurringInvoiceTemplate;
use Webkul\Security\Models\User;

class RecurringInvoiceTemplatePolicy
{
    use Concerns\ChecksInvoiceEntitlement;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_invoice_recurring::invoice::template');
    }

    public function view(User $user, RecurringInvoiceTemplate $template): bool
    {
        return $user->can('view_invoice_recurring::invoice::template');
    }

    public function create(User $user): bool
    {
        return $user->can('create_invoice_recurring::invoice::template');
    }

    public function update(User $user, RecurringInvoiceTemplate $template): bool
    {
        return $user->can('update_invoice_recurring::invoice::template');
    }

    public function delete(User $user, RecurringInvoiceTemplate $template): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
