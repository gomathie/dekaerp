<?php

namespace Webkul\Accounting\Policies\Concerns;

use Illuminate\Database\Eloquent\Model;
use Webkul\PluginManager\Support\PluginEntitlement;
use Webkul\Security\Models\User;

trait ChecksAccountingEntitlement
{
    public function before(User $user, string $ability, mixed $record = null): ?bool
    {
        if ($record instanceof Model && $record->getAttribute('company_id') !== null) {
            return PluginEntitlement::enabledForCompany('accounting', (int) $record->getAttribute('company_id')) ? null : false;
        }

        return PluginEntitlement::enabledForCurrent('accounting') ? null : false;
    }
}