<?php

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Illuminate\Support\Facades\DB;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Filament\Pages\SalesDashboard;
use Webkul\Sale\Filament\Widgets\SaleStatsOverview;
use Webkul\Sale\Models\Order;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/SaleHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('sales');
    PluginAccess::flush();
});

function revokeSalesFor(Company $company): void
{
    $plugin = Plugin::query()->where('name', 'sales')->firstOrFail();

    DB::table('company_plugin')
        ->where('company_id', $company->getKey())
        ->where('plugin_id', $plugin->getKey())
        ->delete();

    PluginAccess::flush();
}

it('generates the permission name the sales dashboard checks', function (): void {
    // Every other test grants the name by hand, so a mismatch with what Shield
    // generates would pass here and leave the page unopenable in production.
    FilamentHelper::bootAdminPanel();

    $generated = collect(FilamentShield::getPages())
        ->flatMap(fn (array $page): array => array_keys($page['permissions']))
        ->all();

    expect($generated)->toContain('page_sale_sales_dashboard');
});

it('opens the sales dashboard only with the permission and the Sales module', function (): void {
    $company = Company::factory()->create();

    CompanyHelper::actingAsCompanyUser($company, []);

    expect(SalesDashboard::canAccess())->toBeFalse();

    CompanyHelper::actingAsCompanyUser($company, ['page_sale_sales_dashboard']);

    expect(SalesDashboard::canAccess())->toBeTrue();

    revokeSalesFor($company);

    expect(SalesDashboard::canAccess())->toBeFalse();
});

it('hides the sales widgets once Sales is revoked for the company', function (): void {
    FilamentHelper::bootAdminPanel();

    $company = Company::factory()->create();

    $widgetPermission = array_key_first(FilamentShield::getWidgets()[SaleStatsOverview::class]['permissions'] ?? []);

    expect($widgetPermission)->not->toBeNull();

    CompanyHelper::actingAsCompanyUser($company, [$widgetPermission]);

    expect(SaleStatsOverview::canView())->toBeTrue();

    revokeSalesFor($company);

    expect(SaleStatsOverview::canView())->toBeFalse();
});

it('reads only the current company’s confirmed orders', function (): void {
    $company = Company::factory()->create();
    $otherCompany = Company::factory()->create();

    $user = CompanyHelper::actingAsCompanyUser($company, []);

    $own = SaleHelper::order(['company_id' => $company->getKey(), 'user_id' => $user->getKey()]);
    $foreign = SaleHelper::order(['company_id' => $otherCompany->getKey()]);

    Order::withoutGlobalScopes()->whereKey([$own->getKey(), $foreign->getKey()])->update([
        'state'      => OrderState::SALE->value,
        'date_order' => now(),
    ]);

    $widget = new SaleStatsOverview;
    $widget->pageFilters = [];

    $ids = (fn () => $this->saleOrders()->pluck('id')->all())->call($widget);

    expect($ids)->toContain($own->getKey())
        ->not->toContain($foreign->getKey());
});

it('lists only the current company’s members as salespersons', function (): void {
    $company = Company::factory()->create();
    $otherCompany = Company::factory()->create();

    $member = CompanyHelper::actingAsCompanyUser($company, []);

    $outsider = User::factory()->create();
    $outsider->allowedCompanies()->sync([$otherCompany->getKey()]);

    $options = (fn (): array => static::companyMemberOptions())->bindTo(null, SalesDashboard::class)();

    expect($options)->toHaveKey($member->getKey())
        ->not->toHaveKey($outsider->getKey());
});
