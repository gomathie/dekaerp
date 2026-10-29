<?php

namespace Webkul\Product\Observers;

use Webkul\Product\Services\CompanyCategoryProvisioner;
use Webkul\Support\Models\Company;

/**
 * Contributes the products plugin's part of provisioning a new company, the way
 * inventories contributes its virtual locations. Hooked here rather than in
 * `Webkul\Support\Models\Company` because `support` is the base plugin and must
 * not know about products (docs/company-admin-role-plan.md §4k).
 */
class CompanyObserver
{
    public function created(Company $company): void
    {
        app(CompanyCategoryProvisioner::class)->provision($company);
    }
}
