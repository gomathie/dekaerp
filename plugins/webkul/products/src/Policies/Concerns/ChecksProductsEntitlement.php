<?php

namespace Webkul\Product\Policies\Concerns;

use Illuminate\Database\Eloquent\Model;
use Webkul\Product\Support\ProductsAccess;
use Webkul\Security\Models\User;

trait ChecksProductsEntitlement
{
    /**
     * Keep product abilities available only to companies holding the catalogue module.
     */
    public function before(User $user, string $ability, mixed $record = null): ?bool
    {
        if ($record instanceof Model && $record->getAttribute('company_id') !== null) {
            return ProductsAccess::enabledForCompany((int) $record->getAttribute('company_id')) ? null : false;
        }

        return ProductsAccess::enabledForCurrent() ? null : false;
    }
}
