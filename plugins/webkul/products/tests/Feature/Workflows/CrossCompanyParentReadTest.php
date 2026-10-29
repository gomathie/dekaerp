<?php

use Webkul\Product\Models\Product;
use Webkul\Product\Models\ProductSupplier;
use Webkul\Support\Models\UOM;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('products');
});

it('inherits a supplier row company from its product across actor company contexts', function () {
    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    CompanyHelper::actingAsCompanyUser($companyA);

    $product = Product::factory()->forCompany($companyA)->create();

    CompanyHelper::actingAsCompanyUser($companyB);

    $supplier = ProductSupplier::factory()->create(['product_id' => $product->id]);

    expect($supplier->company_id)->toBe($companyA->id);
});

it('detects a product using a UOM when the deleting actor is in another company', function () {
    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    CompanyHelper::actingAsCompanyUser($companyA);

    $uom = UOM::factory()->create();
    $product = Product::factory()->forCompany($companyA)->create([
        'uom_id'    => $uom->id,
        'uom_po_id' => $uom->id,
    ]);

    CompanyHelper::actingAsCompanyUser($companyB);

    expect(fn () => $uom->delete())
        ->toThrow(Exception::class, $product->name);
});
