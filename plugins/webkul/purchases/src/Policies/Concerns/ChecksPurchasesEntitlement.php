<?php

namespace Webkul\Purchase\Policies\Concerns;

use Illuminate\Database\Eloquent\Model;
use Webkul\Purchase\Support\PurchasesAccess;
use Webkul\Security\Models\User;

trait ChecksPurchasesEntitlement
{
    /**
     * Keep purchasing abilities available only to companies holding the purchases module.
     */
    public function before(User $user, string $ability, mixed $record = null): ?bool
    {
        if ($record instanceof Model && $record->getAttribute('company_id') !== null) {
            return PurchasesAccess::enabledForCompany((int) $record->getAttribute('company_id')) ? null : false;
        }

        return PurchasesAccess::enabledForCurrent() ? null : false;
    }
}
