<?php

namespace Webkul\Invoice\Policies\Concerns;

use Illuminate\Database\Eloquent\Model;
use Webkul\PluginManager\Support\PluginEntitlement;
use Webkul\Security\Models\User;

trait ChecksInvoiceEntitlement
{
    public function before(User $user, string $ability, mixed $record = null): ?bool
    {
        if ($record instanceof Model && $record->getAttribute('company_id') !== null) {
            return PluginEntitlement::enabledForCompany('invoices', (int) $record->getAttribute('company_id')) ? null : false;
        }

        return PluginEntitlement::enabledForCurrent('invoices') ? null : false;
    }
}