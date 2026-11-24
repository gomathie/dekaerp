<?php

use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Account\Filament\Resources\ProductResource\Pages\EditProduct;
use Webkul\Product\Models\Category;
use Webkul\Product\Models\Product;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

/**
 * 2026-11-19: editing a product in production would not save. Saving an
 * unchanged product, and a renamed one, from its own company must succeed.
 *
 * Uses the Accounts edit page, a real subclass of the base EditProduct: the
 * base ProductResource registers no pages, so rendering the base page alone
 * fails on the missing index route before it reaches saving (2026-11-21).
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

    FilamentHelper::actingAsCompanyUser($company, [
        'view_any_account_product', 'view_account_product', 'update_account_product',
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

    // An administrator who is a member of the product's company; acting as
    // a member is what CompanyScope requires to load the record at all.
    FilamentHelper::actingAsCompanyUser($company, [
        'view_any_account_product', 'view_account_product', 'update_account_product',
        'delete_account_product', 'create_account_product',
    ]);

    // Created by nobody, so the factory's category must be given the same
    // company explicitly; otherwise it lands in another company and the form
    // rightly refuses it as an invalid category.
    $product = CompanyHelper::asNobody(fn () => Product::factory()->create([
        'company_id'  => $company->id,
        'category_id' => Category::factory()->create(['company_id' => $company->id])->id,
    ]));

    Livewire::test(EditProduct::class, ['record' => $product->getKey()])
        ->fillForm(['name' => 'Renamed by admin'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($product->refresh()->name)->toBe('Renamed by admin');
});
