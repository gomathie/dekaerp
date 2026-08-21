<?php

use Webkul\Sale\Filament\Clusters\Orders\Resources\DirectSaleResource;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Services\DirectSaleManager;

require_once __DIR__.'/../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('sales');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('persists the sale_source attribute on an order', function () {
    $order = Order::factory()->create(['sale_source' => DirectSaleResource::SALE_SOURCE]);

    expect($order->fresh()->sale_source)->toBe(DirectSaleResource::SALE_SOURCE);
});

it('creates a single reusable walk-in customer', function () {
    SecurityHelper::authenticateWithPermissions([]);

    $first = DirectSaleManager::resolveWalkInCustomer();
    $second = DirectSaleManager::resolveWalkInCustomer();

    expect($first->id)->toBe($second->id)
        ->and($first->name)->toBe('Walk-In Customer')
        ->and($first->sub_type)->toBe('customer');

    expect(\Webkul\Partner\Models\Partner::where('name', 'Walk-In Customer')->count())->toBe(1);
});

it('registers the index, create and receipt pages', function () {
    $pages = DirectSaleResource::getPages();

    expect($pages)->toHaveKeys(['index', 'create', 'receipt']);
});

it('scopes its query to direct sales only', function () {
    SecurityHelper::authenticateWithPermissions([]);

    $direct = Order::factory()->create(['sale_source' => DirectSaleResource::SALE_SOURCE]);
    $regular = Order::factory()->create(['sale_source' => null]);

    $ids = DirectSaleResource::getEloquentQuery()->pluck('id');

    expect($ids)->toContain($direct->id)
        ->and($ids)->not->toContain($regular->id);
});
