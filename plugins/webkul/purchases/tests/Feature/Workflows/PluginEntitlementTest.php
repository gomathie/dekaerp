<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\PluginManager\Exceptions\PluginNotEntitledException;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Purchase\Filament\Admin\Clusters\Orders;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderResource\Pages\ListPurchaseOrders;
use Webkul\Purchase\Filament\Customer\Clusters\Account\Resources\OrderResource as CustomerOrderResource;
use Webkul\Purchase\Filament\Customer\Clusters\Account\Resources\PurchaseOrderResource as CustomerPurchaseOrderResource;
use Webkul\Purchase\Filament\Customer\Clusters\Account\Resources\QuotationResource as CustomerQuotationResource;
use Webkul\Purchase\Models\Order;
use Webkul\Purchase\Services\OrderWorkflow;
use Webkul\Support\Models\Company;
use Webkul\Website\Models\Partner as Customer;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/PurchaseHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('purchases');
    PluginAccess::flush();
    URL::resolveMissingNamedRoutesUsing(fn () => '#');
});

function setPurchasesEntitlement(Company $company, bool $enabled): void
{
    $plugin = Plugin::query()->where('name', 'purchases')->firstOrFail();

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

it('enforces the purchases entitlement across UI API policies and services', function (): void {
    $companyA = Company::factory()->create();
    $companyB = Company::factory()->create();
    setPurchasesEntitlement($companyA, true);
    setPurchasesEntitlement($companyB, true);
    $user = FilamentHelper::actingAsCompanyUser([$companyA, $companyB], ['view_any_purchase_purchase::order']);
    CompanyHelper::setActive([$companyA->getKey()]);
    $customerA = new Customer;
    $customerA->company_id = $companyA->getKey();
    $customerB = new Customer;
    $customerB->company_id = $companyB->getKey();
    $order = PurchaseHelper::order(['company_id' => $companyA->getKey(), 'user_id' => $user->getKey()]);
    $plugin = Plugin::query()->where('name', 'purchases')->firstOrFail();

    expect($plugin->is_installed)->toBeTrue()
        ->and(PluginAccess::enabledFor('purchases', $companyA->getKey()))->toBeTrue()
        ->and(Orders::canAccess())->toBeTrue();

    Auth::guard('customer')->setUser($customerA);
    expect(CustomerOrderResource::canAccess())->toBeTrue()
        ->and(CustomerPurchaseOrderResource::canAccess())->toBeTrue()
        ->and(CustomerQuotationResource::canAccess())->toBeTrue();

    Livewire::test(ListPurchaseOrders::class)->assertOk();
    $this->getJson(route('admin.api.v1.purchases.purchase-orders.index'))->assertOk();

    setPurchasesEntitlement($companyA, false);

    expect(Orders::canAccess())->toBeFalse()
        ->and(Order::withoutGlobalScopes()->whereKey($order)->exists())->toBeTrue()
        ->and(PluginAccess::enabledFor('purchases', $companyB->getKey()))->toBeTrue();

    Livewire::test(ListPurchaseOrders::class)->assertForbidden();
    $this->getJson(route('admin.api.v1.purchases.purchase-orders.index'))->assertForbidden();

    Auth::guard('customer')->setUser($customerA);
    expect(CustomerOrderResource::canAccess())->toBeFalse()
        ->and(CustomerPurchaseOrderResource::canAccess())->toBeFalse()
        ->and(CustomerQuotationResource::canAccess())->toBeFalse();

    CompanyHelper::setActive([$companyB->getKey()]);

    expect(Orders::canAccess())->toBeTrue();
    $this->getJson(route('admin.api.v1.purchases.purchase-orders.index'))->assertOk();
    Auth::guard('customer')->setUser($customerB);
    expect(CustomerOrderResource::canAccess())->toBeTrue()
        ->and(CustomerPurchaseOrderResource::canAccess())->toBeTrue()
        ->and(CustomerQuotationResource::canAccess())->toBeTrue();

    CompanyHelper::setActive([$companyA->getKey()]);

    expect(fn () => app(OrderWorkflow::class)->backToDraft($order->refresh()))
        ->toThrow(PluginNotEntitledException::class);

    setPurchasesEntitlement($companyA, true);

    expect(Orders::canAccess())->toBeTrue();
    Livewire::test(ListPurchaseOrders::class)->assertOk();
    $this->getJson(route('admin.api.v1.purchases.purchase-orders.index'))->assertOk();
});
