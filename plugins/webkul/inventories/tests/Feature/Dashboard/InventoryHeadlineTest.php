<?php

use Webkul\Invoice\Filament\Overview\OverviewRegistry;
use Webkul\Inventory\Filament\Widgets\InventoryHeadlineWidget;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    PluginAccess::flush();
});

/**
 * Inventory on the combined Overview (user, 2026-11-11). The section follows
 * the Inventory overview page's own access (module switch plus its Shield page
 * permission), so a user who cannot open Inventory sees no inventory cards.
 */
it('shows the inventory section only to users who may open the inventory overview', function (): void {
    $company = Company::factory()->create();

    CompanyHelper::actingAsCompanyUser($company, []);
    expect(array_key_exists('inventory', OverviewRegistry::visible()))->toBeFalse();

    CompanyHelper::actingAsCompanyUser($company, ['page_inventory_overview']);
    expect(array_key_exists('inventory', OverviewRegistry::visible()))->toBeTrue()
        ->and(OverviewRegistry::visible()['inventory']['headline'])->toBe(InventoryHeadlineWidget::class);
});

it('gives two cards, receipts and deliveries, each with an icon and a late count', function (): void {
    CompanyHelper::actingAsCompanyUser(Company::factory()->create(), ['page_inventory_overview']);

    $stats = (new InventoryHeadlineWidget)->headlineStats([]);

    expect($stats)->toHaveCount(2);

    foreach ($stats as $stat) {
        expect($stat->getIcon())->not->toBeNull()
            ->and((string) $stat->getValue())->toBe('0')
            ->and((string) $stat->getDescription())->toBe('None late');
    }
});
