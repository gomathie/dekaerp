<?php

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Illuminate\Support\Facades\DB;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Purchase\Filament\Admin\Pages\Purchases;
use Webkul\Purchase\Filament\Admin\Widgets\PurchaseHeadlineWidget;
use Webkul\Purchase\Filament\Admin\Widgets\PurchaseTrendWidget;
use Webkul\Purchase\Filament\Admin\Widgets\TopRfqsWidget;
use Webkul\Purchase\Models\Order;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/PurchaseHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('purchases');
    PluginAccess::flush();
});

function revokePurchasesFor(Company $company): void
{
    $plugin = Plugin::query()->where('name', 'purchases')->firstOrFail();

    DB::table('company_plugin')
        ->where('company_id', $company->getKey())
        ->where('plugin_id', $plugin->getKey())
        ->delete();

    PluginAccess::flush();
}

it('generates the permission name the purchase dashboard checks', function (): void {
    FilamentHelper::bootAdminPanel();

    $generated = collect(FilamentShield::getPages())
        ->flatMap(fn (array $page): array => array_keys($page['permissions']))
        ->all();

    expect($generated)->toContain('page_purchase_purchases');
});

it('opens the purchase dashboard only with the permission and the Purchases module', function (): void {
    $company = Company::factory()->create();

    CompanyHelper::actingAsCompanyUser($company, []);

    expect(Purchases::canAccess())->toBeFalse();

    CompanyHelper::actingAsCompanyUser($company, ['page_purchase_purchases']);

    expect(Purchases::canAccess())->toBeTrue();

    revokePurchasesFor($company);

    expect(Purchases::canAccess())->toBeFalse();
});

/**
 * Upstream gave TopRfqsWidget no check at all. It now needs its own
 * permission and the Purchases module, like every other dashboard widget.
 */
it('guards the RFQ widget by permission and by the Purchases module', function (): void {
    FilamentHelper::bootAdminPanel();

    $company = Company::factory()->create();

    CompanyHelper::actingAsCompanyUser($company, []);

    expect(TopRfqsWidget::canView())->toBeFalse();

    $permission = array_key_first(FilamentShield::getWidgets()[TopRfqsWidget::class]['permissions'] ?? []);

    expect($permission)->not->toBeNull();

    CompanyHelper::actingAsCompanyUser($company, [$permission]);

    expect(TopRfqsWidget::canView())->toBeTrue();

    revokePurchasesFor($company);

    expect(TopRfqsWidget::canView())->toBeFalse();
});

it('counts orders per day and state in one grouped query within the company', function (): void {
    $company = Company::factory()->create();
    $otherCompany = Company::factory()->create();

    $user = CompanyHelper::actingAsCompanyUser($company, []);

    $orders = [
        PurchaseHelper::order(['company_id' => $company->getKey(), 'user_id' => $user->getKey()]),
        PurchaseHelper::order(['company_id' => $company->getKey(), 'user_id' => $user->getKey()]),
        PurchaseHelper::order(['company_id' => $otherCompany->getKey()]),
    ];

    Order::withoutGlobalScopes()->whereKey($orders[0]->getKey())->update(['ordered_at' => '2026-03-01 10:00', 'state' => 'draft']);
    Order::withoutGlobalScopes()->whereKey($orders[1]->getKey())->update(['ordered_at' => '2026-03-02 10:00', 'state' => 'purchase']);
    Order::withoutGlobalScopes()->whereKey($orders[2]->getKey())->update(['ordered_at' => '2026-03-01 10:00', 'state' => 'draft']);

    $widget = new PurchaseTrendWidget;
    $widget->pageFilters = ['start_date' => '2026-03-01', 'end_date' => '2026-03-02'];

    DB::flushQueryLog();
    DB::enableQueryLog();

    $data = (fn (): array => $this->getData())->call($widget);

    $orderQueries = collect(DB::getQueryLog())
        // Data queries only, not one-off column lookups (pg_attribute).
        ->filter(fn (array $entry): bool => str_contains($entry['query'], 'from "purchases_orders"'))
        ->count();

    DB::disableQueryLog();

    $byLabel = collect($data['datasets'])->mapWithKeys(fn (array $set, int $index): array => [$index => $set['data']]);

    // Series order follows PurchaseTrendWidget::SERIES_COLORS: draft first, purchase third.
    expect($byLabel[0])->toBe([1, 0])
        ->and($byLabel[2])->toBe([0, 1])
        ->and($orderQueries)->toBe(1);
});

/**
 * Sentry DEKAERP-12 (2026-11-11): the Overview's purchases card summed
 * amount_total, a Sales column, and 500'd the page. Spend is the confirmed
 * orders' total_cc_amount, within the company.
 */
it('computes the overview purchase spend from confirmed orders in company currency', function (): void {
    $company = Company::factory()->create();
    $user = CompanyHelper::actingAsCompanyUser($company, ['page_purchase_purchases']);

    $confirmed = PurchaseHelper::order(['company_id' => $company->getKey(), 'user_id' => $user->getKey()]);
    $draft = PurchaseHelper::order(['company_id' => $company->getKey(), 'user_id' => $user->getKey()]);

    Order::withoutGlobalScopes()->whereKey($confirmed->getKey())->update(['ordered_at' => now(), 'state' => 'purchase', 'total_cc_amount' => 250]);
    Order::withoutGlobalScopes()->whereKey($draft->getKey())->update(['ordered_at' => now(), 'state' => 'draft', 'total_cc_amount' => 999]);

    $stats = (new PurchaseHeadlineWidget)->headlineStats([]);

    expect($stats)->toHaveCount(2)
        ->and((string) $stats[0]->getValue())->toContain('250')
        ->and((string) $stats[0]->getValue())->not->toContain('999')
        ->and((string) $stats[1]->getValue())->toBe('1');
});
