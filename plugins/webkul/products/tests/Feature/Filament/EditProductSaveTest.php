<?php

use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Product\Filament\Resources\ProductResource\Pages\EditProduct;
use Webkul\Product\Models\Product;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

/**
 * 2026-11-19: editing a product in production would not save. Saving an
 * unchanged product, and a renamed one, from its own company must succeed.
 */
beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('products');
    // The product form reads taxes, which the accounts plugin owns; in
    // production it is always installed.
    TestBootstrapHelper::ensurePluginInstalled('accounts');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');
});

it('saves an edited product for a user of its company', function () {
    $company = CompanyHelper::company();

    CompanyHelper::actingAsCompanyUser($company, [
        'view_any_product_product', 'view_product_product', 'update_product_product',
    ]);

    $product = Product::factory()->create(['company_id' => $company->id]);

    Livewire::test(EditProduct::class, ['record' => $product->getKey()])
        ->fillForm(['name' => 'Renamed product'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($product->refresh()->name)->toBe('Renamed product');
});

it('saves an edited product for an administrator', function () {
    $company = CompanyHelper::company();

    FilamentHelper::actingAs();
    CompanyHelper::setActive([$company->id]);
    app()->forgetInstance(\Webkul\Support\Services\CompanyContext::class);

    $product = CompanyHelper::asNobody(fn () => Product::factory()->create(['company_id' => $company->id]));

    Livewire::test(EditProduct::class, ['record' => $product->getKey()])
        ->fillForm(['name' => 'Renamed by admin'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($product->refresh()->name)->toBe('Renamed by admin');
});
