<?php

use Webkul\Product\Models\Category;
use Webkul\Product\Services\CompanyCategoryProvisioner;
use Webkul\Support\Exceptions\CompanyMismatchException;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('products');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('stamps a new product category with the active company and hides it from another company', function () {
    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    CompanyHelper::actingAsCompanyUser($companyA);

    $category = Category::factory()->create();

    expect($category->company_id)->toBe($companyA->id);

    CompanyHelper::actingAsCompanyUser($companyB);

    expect(Category::query()->pluck('id'))->not->toContain($category->id);
});

it('gives a sub-category its parent\'s company, not the session\'s', function () {
    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    $parent = Category::factory()->company($companyA)->create();

    // Acting in B, where the parent is not even visible. The child must still be
    // filed under A - the parent's company is a fact about the parent, not a
    // question about who is looking.
    CompanyHelper::actingAsCompanyUser($companyB);

    $child = Category::withoutGlobalScopes()->create([
        'name'       => 'Child of A',
        'parent_id'  => $parent->id,
        'company_id' => null,
    ]);

    expect($child->company_id)->toBe($companyA->id)
        ->and($child->parent_path)->toBe('/'.$parent->id.'/')
        ->and($child->full_name)->toBe($parent->name.' / Child of A');
});

it('refuses a sub-category filed under a different company from its parent', function () {
    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    $parent = Category::factory()->company($companyA)->create();

    CompanyHelper::actingAsCompanyUser($companyB);

    expect(fn () => Category::factory()->create([
        'parent_id'  => $parent->id,
        'company_id' => $companyB->id,
    ]))->toThrow(CompanyMismatchException::class);
});

it('gives a new company its own root category so the product form has a default', function () {
    $company = CompanyHelper::company();

    $categories = Category::withoutGlobalScopes()->where('company_id', $company->id)->get();

    expect($categories)->toHaveCount(1)
        ->and($categories->first()->parent_id)->toBeNull()
        ->and($categories->first()->name)->toBe(CompanyCategoryProvisioner::DEFAULT_CATEGORY_NAME);

    // Reachable through the scoped query the form uses, which is the point.
    expect(Category::query()->whereKey($categories->first()->id)->exists())->toBeTrue();
});
