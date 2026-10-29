<?php

use Webkul\Product\Models\Packaging;
use Webkul\Product\Models\PriceList;
use Webkul\Product\Models\Product;
use Webkul\Support\Exceptions\CompanyMismatchException;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('products');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('hides price lists owned by another company', function () {
    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    $ownPriceList = PriceList::factory()->company($companyA)->create();
    $otherPriceList = PriceList::factory()->company($companyB)->create();

    CompanyHelper::actingAsCompanyUser($companyA);

    $visible = PriceList::query()->pluck('id');

    expect($visible)->toContain($ownPriceList->id)
        ->not->toContain($otherPriceList->id);
});

it('stamps a new price list with the active company', function () {
    $companyB = CompanyHelper::company();

    CompanyHelper::actingAsCompanyUser($companyB);

    $priceList = PriceList::factory()->create();

    expect($priceList->company_id)->toBe($companyB->id);
});

/**
 * Inverted 2026-10-29. This used to pin "leaves product packaging shared even
 * when its product belongs to a company". Each client has its own catalogue, so
 * a packaging now takes its product's company (docs/company-admin-role-plan.md
 * §4k).
 */
it('gives product packaging its product\'s company and hides it from another company', function () {
    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    CompanyHelper::actingAsCompanyUser($companyA);

    $product = Product::factory()->create(['company_id' => $companyA->id]);

    $packaging = Packaging::factory()->create(['product_id' => $product->id]);

    expect($packaging->company_id)->toBe($companyA->id)
        ->and(Packaging::query()->pluck('id'))->toContain($packaging->id);

    CompanyHelper::actingAsCompanyUser($companyB);

    expect(Packaging::query()->pluck('id'))->not->toContain($packaging->id);
});

it('stamps a new product with the active company and hides it from another company', function () {
    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    CompanyHelper::actingAsCompanyUser($companyA);

    $product = Product::factory()->create();

    expect($product->company_id)->toBe($companyA->id);

    CompanyHelper::actingAsCompanyUser($companyB);

    expect(Product::query()->pluck('id'))->not->toContain($product->id);
});

it('gives a variant its template\'s company, not the session\'s', function () {
    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    $template = Product::factory()->create(['company_id' => $companyA->id]);

    // Acting in B, where the template is not even visible: the variant must
    // still be filed under the template's company A.
    CompanyHelper::actingAsCompanyUser($companyB);

    $variant = Product::factory()->create(['parent_id' => $template->id]);

    expect($variant->company_id)->toBe($companyA->id);
});

it('refuses a variant filed under a different company from its template', function () {
    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    $template = Product::factory()->create(['company_id' => $companyA->id]);

    expect(fn () => Product::factory()->create([
        'parent_id'  => $template->id,
        'company_id' => $companyB->id,
    ]))->toThrow(CompanyMismatchException::class);
});
