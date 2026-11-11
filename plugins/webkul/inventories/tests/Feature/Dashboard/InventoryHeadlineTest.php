<?php

use Webkul\Invoice\Filament\Overview\OverviewRegistry;
use Webkul\Inventory\Filament\Widgets\InventoryHeadlineWidget;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\OrderPoint;
use Webkul\Inventory\Models\ProductQuantity;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Product\Models\Product;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    // Shield names a page permission only once the panel is booted; without it
    // hasPagePermission() has nothing to check and allows every user.
    FilamentHelper::bootAdminPanel();
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

it('gives four cards with icons: receipts, deliveries, low stock and stock value', function (): void {
    CompanyHelper::actingAsCompanyUser(Company::factory()->create(), ['page_inventory_overview']);

    $stats = (new InventoryHeadlineWidget)->headlineStats([]);

    expect($stats)->toHaveCount(4)
        ->and((string) $stats[0]->getDescription())->toBe('None late')
        ->and((string) $stats[1]->getDescription())->toBe('None late')
        ->and((string) $stats[2]->getValue())->toBe('0');

    foreach ($stats as $stat) {
        expect($stat->getIcon())->not->toBeNull();
    }
});

/**
 * Low stock and stock value (user, 2026-11-11, recommendations accepted):
 * low stock counts products at or below their reorder minimum across internal
 * locations; stock value is on-hand quantity in internal locations times cost.
 * Stock outside internal locations, and another company's stock, count for
 * neither.
 */
it('counts low stock against the reorder minimum and values internal stock at cost', function (): void {
    $company = Company::factory()->create();
    $other = Company::factory()->create();
    CompanyHelper::actingAsCompanyUser($company, ['page_inventory_overview']);

    $internal = Location::factory()->internal()->create(['company_id' => $company->id]);
    // The warehouse factory cannot build one on its own here; the install seeds one, and neither card reads it.
    $warehouseId = Warehouse::withoutGlobalScopes()->value('id');
    $customer = Location::factory()->customer()->create(['company_id' => $company->id]);

    $low = Product::factory()->create(['company_id' => $company->id, 'cost' => 10]);
    $fine = Product::factory()->create(['company_id' => $company->id, 'cost' => 2]);

    ProductQuantity::factory()->create(['company_id' => $company->id, 'product_id' => $low->id, 'location_id' => $internal->id, 'quantity' => 3]);
    ProductQuantity::factory()->create(['company_id' => $company->id, 'product_id' => $fine->id, 'location_id' => $internal->id, 'quantity' => 50]);
    // Already delivered to a customer: not on hand.
    ProductQuantity::factory()->create(['company_id' => $company->id, 'product_id' => $fine->id, 'location_id' => $customer->id, 'quantity' => 999]);

    OrderPoint::factory()->create(['company_id' => $company->id, 'product_id' => $low->id, 'product_min_qty' => 5, 'warehouse_id' => $warehouseId, 'location_id' => $internal->id]);
    OrderPoint::factory()->create(['company_id' => $company->id, 'product_id' => $fine->id, 'product_min_qty' => 5, 'warehouse_id' => $warehouseId, 'location_id' => $internal->id]);

    // Another company's low rule must not count here.
    $otherProduct = Product::factory()->create(['company_id' => $other->id]);
    OrderPoint::withoutEvents(fn () => OrderPoint::factory()->create(['company_id' => $other->id, 'product_id' => $otherProduct->id, 'product_min_qty' => 5, 'warehouse_id' => $warehouseId, 'location_id' => $internal->id]));

    $widget = new InventoryHeadlineWidget;

    expect((fn () => $this->lowStockCount())->call($widget))->toBe(1)
        // 3 x 10 + 50 x 2; the customer location is excluded.
        ->and((fn () => $this->stockValue())->call($widget))->toBe(130.0);
});
