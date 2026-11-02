<?php

use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Webkul\PluginManager\Exceptions\PluginNotEntitledException;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Product\Filament\Resources\PriceListResource;
use Webkul\Product\Filament\Resources\PriceListResource\Pages\ListPriceLists;
use Webkul\Product\Models\PriceList;
use Webkul\Product\Services\CompanyCategoryProvisioner;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('products');
    PluginAccess::flush();
});

function setProductsEntitlement(Company $company, bool $enabled): void
{
    $plugin = Plugin::query()->where('name', 'products')->firstOrFail();

    if ($enabled) {
        DB::table('company_plugin')->insertOrIgnore([
            'company_id' => $company->getKey(),
            'plugin_id'  => $plugin->getKey(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    } else {
        DB::table('company_plugin')
            ->where('company_id', $company->getKey())
            ->where('plugin_id', $plugin->getKey())
            ->delete();
    }

    PluginAccess::flush();
}

it('enforces the products entitlement across UI API policies and services', function (): void {
    $companyA = Company::factory()->create();
    $companyB = Company::factory()->create();
    FilamentHelper::actingAsCompanyUser($companyA, [
        'view_any_product_category',
        'view_any_product_price::list',
    ]);
    $priceList = PriceList::factory()->create(['company_id' => $companyA->getKey()]);
    $plugin = Plugin::query()->where('name', 'products')->firstOrFail();

    expect($plugin->is_installed)->toBeTrue()
        ->and(PluginAccess::enabledFor('products', $companyA->getKey()))->toBeTrue()
        ->and(PriceListResource::shouldRegisterNavigation())->toBeFalse();

    Livewire::test(ListPriceLists::class)->assertOk();
    $this->getJson(route('admin.api.v1.products.categories.index'))->assertOk();

    setProductsEntitlement($companyA, false);

    expect(PriceListResource::canAccess())->toBeFalse()
        ->and(PriceList::withoutGlobalScopes()->whereKey($priceList)->exists())->toBeTrue()
        ->and(PluginAccess::enabledFor('products', $companyB->getKey()))->toBeTrue();

    Livewire::test(ListPriceLists::class)->assertForbidden();
    $this->getJson(route('admin.api.v1.products.categories.index'))->assertForbidden();

    expect(fn () => app(CompanyCategoryProvisioner::class)->provision($companyA))
        ->toThrow(PluginNotEntitledException::class);

    setProductsEntitlement($companyA, true);

    expect(PriceListResource::canAccess())->toBeTrue();
    Livewire::test(ListPriceLists::class)->assertOk();
    $this->getJson(route('admin.api.v1.products.categories.index'))->assertOk();
});
