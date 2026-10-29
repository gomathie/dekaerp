<?php

use Illuminate\Support\Collection;
use Webkul\Inventory\Models\Location;
use Webkul\Product\Models\Category;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
});

it('reads a product category ancestry while the actor is in another company', function () {
    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    CompanyHelper::actingAsCompanyUser($companyA);

    $root = Category::withoutGlobalScopes()
        ->where('company_id', $companyA->id)
        ->whereNull('parent_id')
        ->firstOrFail();

    $child = Category::withoutGlobalScopes()->create([
        'name'       => 'Company A stock category',
        'parent_id'  => $root->id,
        'company_id' => $companyA->id,
    ]);

    CompanyHelper::actingAsCompanyUser($companyB);

    $method = new ReflectionMethod(Location::class, 'categoryAncestry');
    $method->setAccessible(true);

    $ancestry = $method->invoke(new Location, new Collection([(object) ['category_id' => $child->id]]));

    expect($ancestry->all())->toBe([$child->id, $root->id]);
});
