<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Invoice\Filament\Clusters\Customers;
use Webkul\PluginManager\Exceptions\PluginNotEntitledException;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Sale\Enums\AdvancedPayment;
use Webkul\Sale\Filament\Clusters\Orders;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages\ListQuotations;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Services\Invoicer;
use Webkul\Sale\Services\OrderWorkflow;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/SaleHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('sales');
    PluginAccess::flush();
    URL::resolveMissingNamedRoutesUsing(fn () => '#');
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

/**
 * User, 2026-11-22: customer invoices sit under Sales as an "Invoices" tab
 * when the company has Sales, and fall back to their own sidebar entry when
 * it does not, so a company that only invoices keeps its menu.
 */
it('shows invoices under sales only while the company has sales', function (): void {
    $company = Company::factory()->create();
    setSalesEntitlement($company, true);
    FilamentHelper::actingAsCompanyUser([$company], []);
    CompanyHelper::setActive([$company->getKey()]);

    expect(Customers::getNavigationGroup())->toBe(NavigationGroup::Sale)
        ->and(Customers::getNavigationLabel())->toBe(__('invoices::filament/clusters/customers.navigation.in-sales'))
        // v1.16.2: placed after Orders and To Invoice (which have no
        // position), so the sidebar "Sales" link still opens Orders.
        ->and(Customers::getNavigationSort())->toBe(0)
        ->and(Orders::getNavigationSort())->toBeNull();

    setSalesEntitlement($company, false);

    expect(Customers::getNavigationGroup())->toBe(NavigationGroup::Invoice)
        ->and(Customers::getNavigationLabel())->toBe(__('invoices::filament/clusters/customers.navigation.title'));
});

it('enforces the sales entitlement across UI API policies and services', function (): void {
    $companyA = Company::factory()->create();
    $companyB = Company::factory()->create();
    setSalesEntitlement($companyA, true);
    setSalesEntitlement($companyB, true);
    $user = FilamentHelper::actingAsCompanyUser([$companyA, $companyB], [
        'view_any_sale_quotation',
        'view_any_sale_order',
    ]);
    CompanyHelper::setActive([$companyA->getKey()]);
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

    CompanyHelper::setActive([$companyB->getKey()]);

    expect(Orders::canAccess())->toBeTrue();
    $this->getJson(route('admin.api.v1.sales.orders.index'))->assertOk();

    CompanyHelper::setActive([$companyA->getKey()]);

    expect(fn () => app(OrderWorkflow::class)->toggleLock($order->refresh()))
        ->toThrow(PluginNotEntitledException::class);

    setSalesEntitlement($companyA, true);

    expect(Orders::canAccess())->toBeTrue();
    Livewire::test(ListQuotations::class)->assertOk();
    $this->getJson(route('admin.api.v1.sales.orders.index'))->assertOk();
});

/**
 * SAAS-05 review finding 3: invoicing was guarded only by the screens. A job,
 * another plugin or a super-admin-bypassed policy could still invoice an order
 * of a company with Sales revoked. User decision, 2026-11-05: refuse.
 */
it('refuses to invoice an order once Sales is revoked for its company', function (): void {
    $company = Company::factory()->create();
    setSalesEntitlement($company, true);
    FilamentHelper::actingAsCompanyUser([$company], []);
    CompanyHelper::setActive([$company->getKey()]);
    $order = SaleHelper::order(['company_id' => $company->getKey()]);

    setSalesEntitlement($company, false);

    expect(fn () => app(Invoicer::class)->createInvoice($order->refresh()))
        ->toThrow(PluginNotEntitledException::class)
        ->and(fn () => app(Invoicer::class)->invoiceOrder($order->refresh(), ['advance_payment_method' => AdvancedPayment::DELIVERED->value]))
        ->toThrow(PluginNotEntitledException::class)
        ->and($order->refresh()->accountMoves()->count())->toBe(0);
});
