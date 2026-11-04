<?php

namespace Webkul\Employee\Policies\Concerns;

use Illuminate\Database\Eloquent\Model;
use Webkul\PluginManager\Support\PluginEntitlement;
use Webkul\Security\Models\User;

trait ChecksEmployeeEntitlement
{
    public function before(User $user, string $ability, mixed $record = null): ?bool
    {
        if ($record instanceof Model && $record->getAttribute('company_id') !== null) {
            return PluginEntitlement::enabledForCompany('employees', (int) $record->getAttribute('company_id')) ? null : false;
        }

        return PluginEntitlement::enabledForCurrent('employees') ? null : false;
    }
}