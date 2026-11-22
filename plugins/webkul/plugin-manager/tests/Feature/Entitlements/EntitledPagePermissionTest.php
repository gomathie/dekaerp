<?php

use Illuminate\Support\Facades\DB;
use Webkul\Accounting\Filament\Pages\Overview as AccountingOverview;
use Webkul\Barcode\Filament\Pages\LaunchBarcode;
use Webkul\Inventory\Filament\Pages\Overview as InventoryOverview;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Project\Filament\Pages\Dashboard as ProjectDashboard;
use Webkul\Purchase\Filament\Admin\Clusters\Settings\Pages\ManageOrders as PurchaseManageOrders;
use Webkul\Purchase\Filament\Admin\Clusters\Settings\Pages\ManageProducts as PurchaseManageProducts;
use Webkul\Recruitment\Filament\Pages\Recruitments;
use Webkul\Sale\Filament\Clusters\Settings\Pages\ManageInvoice;
use Webkul\Sale\Filament\Clusters\Settings\Pages\ManagePricing;
use Webkul\Sale\Filament\Clusters\Settings\Pages\ManageProducts as SaleManageProducts;
use Webkul\Sale\Filament\Clusters\Settings\Pages\ManageQuotationAndOrder;
use Webkul\Support\Models\Company;
use Webkul\TimeOff\Filament\Pages\ByType;
use Webkul\TimeOff\Filament\Pages\Dashboard as TimeOffDashboard;
use Webkul\TimeOff\Filament\Pages\Overview as TimeOffOverview;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

/**
 * A canAccess() written on a Shield page replaces HasPageShield's. The SAAS-05
 * rollout (2026-11-03/04) wrote one that checked only the module switch on
 * these pages, so every user of an entitled company could open them - company
 * settings included - whatever their role. Found 2026-11-07; each page now
 * also requires its own permission (ChecksPagePermission).
 */
dataset('entitled shield pages', [
    'accounting overview'        => [AccountingOverview::class, 'accounting'],
    'barcode launcher'           => [LaunchBarcode::class, 'barcode'],
    'inventory overview'         => [InventoryOverview::class, 'inventories'],
    'project dashboard'          => [ProjectDashboard::class, 'projects'],
    'purchase order settings'    => [PurchaseManageOrders::class, 'purchases'],
    'purchase product settings'  => [PurchaseManageProducts::class, 'purchases'],
    'recruitment dashboard'      => [Recruitments::class, 'recruitments'],
    'sale invoice settings'      => [ManageInvoice::class, 'sales'],
    'sale pricing settings'      => [ManagePricing::class, 'sales'],
    'sale product settings'      => [SaleManageProducts::class, 'sales'],
    'sale quotation settings'    => [ManageQuotationAndOrder::class, 'sales'],
    'time off by type'           => [ByType::class, 'time-off'],
    'time off dashboard'         => [TimeOffDashboard::class, 'time-off'],
    'time off overview'          => [TimeOffOverview::class, 'time-off'],
]);

it('refuses an entitled company user without the page permission', function (string $page, string $plugin): void {
    TestBootstrapHelper::ensurePluginInstalled($plugin);
    PluginAccess::flush();
    FilamentHelper::bootAdminPanel();

    $company = Company::factory()->create();

    $permission = (fn (): ?string => static::getPagePermission())->bindTo(null, $page)();

    expect($permission)->not->toBeNull();

    CompanyHelper::actingAsCompanyUser($company, []);

    expect($page::canAccess())->toBeFalse();

    CompanyHelper::actingAsCompanyUser($company, [$permission]);

    expect($page::canAccess())->toBeTrue();
})->with('entitled shield pages');

/**
 * TRACE365, 2026-11-22: the Barcode launcher showed in the sidebar of a
 * company without the Barcode module. With the module revoked it is refused
 * even holding its permission.
 */
it('hides the barcode launcher from a company without the module', function (): void {
    TestBootstrapHelper::ensurePluginInstalled('barcode');
    FilamentHelper::bootAdminPanel();

    $company = Company::factory()->create();
    $permission = (fn (): ?string => static::getPagePermission())->bindTo(null, LaunchBarcode::class)();

    DB::table('company_plugin')
        ->where('company_id', $company->getKey())
        ->where('plugin_id', Plugin::query()->where('name', 'barcode')->value('id'))
        ->delete();
    PluginAccess::flush();

    CompanyHelper::actingAsCompanyUser($company, [$permission]);

    expect(LaunchBarcode::canAccess())->toBeFalse();
});
