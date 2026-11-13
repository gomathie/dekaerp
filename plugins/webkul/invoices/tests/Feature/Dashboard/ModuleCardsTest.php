<?php

use Illuminate\Support\Facades\URL;
use Webkul\Inventory\Filament\OverviewCard;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';

/**
 * Every "Across the company" card runs its real queries (2026-11-13). The
 * registry reports and hides a card whose query fails, so a wrong column
 * would otherwise vanish from the dashboard silently; here it fails a test.
 */
dataset('module cards', [
    'inventory'   => ['inventories', OverviewCard::class],
    'projects'    => ['projects', Webkul\Project\Filament\OverviewCard::class],
    'recruitment' => ['recruitments', Webkul\Recruitment\Filament\OverviewCard::class],
    'employees'   => ['time-off', Webkul\TimeOff\Filament\OverviewCard::class],
    'logistics'   => ['logistics', Webkul\Logistics\Filament\OverviewCard::class],
]);

/**
 * Operation has no policy and Filament allows a resource without one, so a
 * card gated on OperationResource::canViewAny() showed to everyone (found
 * 2026-11-13). It now follows the Inventory overview page's rule.
 */
it('hides the inventory card from a user who may not open the inventory overview', function (): void {
    TestBootstrapHelper::ensurePluginInstalled('inventories');

    CompanyHelper::actingAsCompanyUser(Company::factory()->create(), []);

    // The page's permission name comes from the panel, which must include
    // the plugin installed in this run.
    FilamentHelper::bootAdminPanel();

    expect(OverviewCard::visible())->toBeFalse();
});

it('builds the module card from real queries', function (string $plugin, string $card): void {
    TestBootstrapHelper::ensurePluginInstalled($plugin);

    CompanyHelper::actingAsCompanyUser(Company::factory()->create(), []);

    // The cards link to their lists. A plugin installed inside this test
    // run has no routes in it (they register at boot), so links resolve to
    // '#' here, as in the accounting report tests; the queries are the point.
    FilamentHelper::bootAdminPanel();
    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    $data = $card::data();

    expect($data['label'])->toBeString()->not->toBeEmpty()
        ->and($data['items'])->toHaveCount(3)
        ->and(array_column($data['items'], 'value'))->each->toBeString();
})->with('module cards');
