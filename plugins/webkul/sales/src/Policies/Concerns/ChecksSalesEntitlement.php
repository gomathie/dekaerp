<?php

namespace Webkul\Sale\Policies\Concerns;

use Illuminate\Database\Eloquent\Model;
use Webkul\Sale\Support\SalesAccess;
use Webkul\Security\Models\User;

trait ChecksSalesEntitlement
{
    /**
     * Keep sales abilities available only to companies holding the sales module.
     */
    public function before(User $user, string $ability, mixed $record = null): ?bool
    {
        if ($record instanceof Model && $record->getAttribute('company_id') !== null) {
            return SalesAccess::enabledForCompany((int) $record->getAttribute('company_id')) ? null : false;
        }

        return SalesAccess::enabledForCurrent() ? null : false;
    }
}
