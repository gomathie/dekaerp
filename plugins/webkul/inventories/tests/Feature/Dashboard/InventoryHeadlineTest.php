<?php

use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Filament\Widgets\InventoryHeadlineWidget;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Models\OrderPoint;
use Webkul\Inventory\Models\ProductQuantity;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Invoice\Filament\Overview\OverviewRegistry;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Product\Models\Product;
use Webkul\Security\Enums\PermissionType;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/InventoryHelper.php';

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

/**
 * Plan 10, item 2 (2026-11-25): TRACE365 had 4 deliveries ready but never
 * validated while Needs attention said "All clear". Ready deliveries of this
 * company count, with a link to the deliveries list's Ready view; waiting
 * deliveries, ready receipts and another company's deliveries do not.
 */
it('lists the company\'s deliveries ready to validate in needs attention', function (): void {
    $warehouse = InventoryHelper::warehouse();
    $product = InventoryHelper::product();
    $other = Company::factory()->create();

    $ready = [InventoryHelper::delivery($warehouse, [[$product, 1]]), InventoryHelper::delivery($warehouse, [[$product, 2]])];
    $waiting = InventoryHelper::delivery($warehouse, [[$product, 3]]);
    $receipt = InventoryHelper::receipt($warehouse, [[$product, 4]]);
    $elsewhere = InventoryHelper::delivery($warehouse, [[$product, 5]]);

    Operation::withoutGlobalScopes()->whereKey([...array_map(fn ($op) => $op->id, $ready), $receipt->id, $elsewhere->id])
        ->update(['state' => OperationState::ASSIGNED]);
    Operation::withoutGlobalScopes()->whereKey($waiting->id)->update(['state' => OperationState::CONFIRMED]);
    Operation::withoutGlobalScopes()->whereKey($elsewhere->id)->update(['company_id' => $other->id]);

    $user = CompanyHelper::actingAsCompanyUser(InventoryHelper::company(), ['page_inventory_overview', 'view_any_inventory_delivery']);
    // The production default: everyone sees their company's records.
    $user->forceFill(['resource_permission' => PermissionType::GLOBAL])->saveQuietly();

    $items = InventoryHeadlineWidget::attention();

    expect($items)->toHaveCount(1)
        ->and($items[0]['count'])->toBe(2)
        ->and((string) $items[0]['url'])->toContain('activeTableView=ready')
        ->and($items[0]['label'])->toBe(trans_choice('inventories::filament/widgets/inventory-headline.ready-deliveries', 2, ['count' => 2]))
        ->and(collect(OverviewRegistry::attentionItems())->pluck('label'))->toContain($items[0]['label']);
});

it('leaves ready deliveries out of needs attention for someone who cannot open the deliveries list', function (): void {
    CompanyHelper::actingAsCompanyUser(InventoryHelper::company(), ['page_inventory_overview']);

    expect(InventoryHeadlineWidget::attention())->toBe([]);
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
    // Built before signing in: since UF-025 a member of $company may not write into $other.
    $otherProduct = Product::factory()->create(['company_id' => $other->id]);
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
    OrderPoint::withoutEvents(fn () => OrderPoint::factory()->create(['company_id' => $other->id, 'product_id' => $otherProduct->id, 'product_min_qty' => 5, 'warehouse_id' => $warehouseId, 'location_id' => $internal->id]));

    $widget = new InventoryHeadlineWidget;

    expect((fn () => $this->lowStockCount())->call($widget))->toBe(1)
        // 3 x 10 + 50 x 2; the customer location is excluded.
        ->and((fn () => $this->stockValue())->call($widget))->toBe(130.0);
});
