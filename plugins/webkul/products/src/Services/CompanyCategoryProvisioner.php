<?php

namespace Webkul\Product\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Webkul\Product\Models\Category;
use Webkul\Support\Models\Company;

/**
 * Gives a company a root product category of its own.
 *
 * Why it is needed at all: the product form defaults `category_id` to
 * `Category::first()`, and once categories are per company each client has its
 * own tree (docs/company-admin-role-plan.md §4k). A company created after the
 * installation therefore has no category, and the required select on the
 * product form opens empty with nothing to choose.
 *
 * The rule lives here rather than in the form or in a caller, so Logistics'
 * provisioner and the backfill ask the same question instead of writing their
 * own query against `products_categories` - a second implementation of "this
 * company's default category" is how the two come to disagree.
 */
class CompanyCategoryProvisioner
{
    public const DEFAULT_CATEGORY_NAME = 'All';

    /**
     * Safe to repeat: the company's root category is found rather than added
     * again, whatever it is named.
     */
    public function provision(Company $company): ?Category
    {
        if (! Schema::hasTable('products_categories')) {
            return null;
        }

        return Category::withoutGlobalScopes()->firstOrCreate(
            [
                'company_id' => $company->id,
                'parent_id'  => null,
            ],
            [
                'name'       => self::DEFAULT_CATEGORY_NAME,
                'creator_id' => $company->creator_id ?? Auth::id(),
            ]
        );
    }

    public function defaultCategoryId(int $companyId): ?int
    {
        return Category::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->whereNull('parent_id')
            ->orderBy('id')
            ->value('id');
    }
}
