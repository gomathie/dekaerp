<?php

use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Webkul\PluginManager\Exceptions\PluginNotEntitledException;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Sale\Filament\Clusters\Orders;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages\ListQuotations;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Services\OrderWorkflow;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/SaleHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('sales');
    PluginAccess::flush();
});

function setSalesEntitlement(Company $company, bool $enabled): void
{
    $plugin = Plugin::query()->where('name', 'sales')->firstOrFail();

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

it('enforces the sales entitlement across UI API policies and services', function (): void {
    $companyA = Company::factory()->create();
    $companyB = Company::factory()->create();
    setSalesEntitlement($companyA, true);
    setSalesEntitlement($companyB, true);
    $user = FilamentHelper::actingAsCompanyUser($companyA, [
        'view_any_sale_quotation',
        'view_any_sale_order',
    ]);
    $order = SaleHelper::order(['company_id' => $companyA->getKey(), 'user_id' => $user->getKey()]);
    $plugin = Plugin::query()->where('name', 'sales')->firstOrFail();

    expect($plugin->is_installed)->toBeTrue()
        ->and(PluginAccess::enabledFor('sales', $companyA->getKey()))->toBeTrue()
        ->and(Orders::canAccess())->toBeTrue();

    Livewire::test(ListQuotations::class)->assertOk();
    $this->getJson(route('admin.api.v1.sales.orders.index'))->assertOk();

    setSalesEntitlement($companyA, false);

    expect(Orders::canAccess())->toBeFalse()
        ->and(Order::withoutGlobalScopes()->whereKey($order)->exists())->toBeTrue()
        ->and(PluginAccess::enabledFor('sales', $companyB->getKey()))->toBeTrue();

    Livewire::test(ListQuotations::class)->assertForbidden();
    $this->getJson(route('admin.api.v1.sales.orders.index'))->assertForbidden();

    expect(fn () => app(OrderWorkflow::class)->toggleLock($order->refresh()))
        ->toThrow(PluginNotEntitledException::class);

    setSalesEntitlement($companyA, true);

    expect(Orders::canAccess())->toBeTrue();
    Livewire::test(ListQuotations::class)->assertOk();
    $this->getJson(route('admin.api.v1.sales.orders.index'))->assertOk();
});
