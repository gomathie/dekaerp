<?php

use Illuminate\Support\Facades\DB;
use Webkul\Field\Models\Field;
use Webkul\Inventory\Models\Product;
use Webkul\Partner\Models\Partner;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('sales');
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    Field::flushCustomizableCache();
});

afterEach(fn () => Field::flushCustomizableCache());

/**
 * A custom field is a column of a table, so it belongs on every screen that
 * edits that table (unified product resource, user 2026-11-11). Before, a
 * field created on the Inventory product showed only on Inventory screens
 * and was not even saved from the Sales product screen, because the Sales
 * product model does not inherit from the Inventory one.
 */
it('applies a custom field made on one module product model to every product model on the same table', function (): void {
    DB::table('custom_fields')->insert([
        'code'              => 'shelf_life_note',
        'name'              => 'Shelf life note',
        'type'              => 'text',
        'customizable_type' => Product::class,
        'created_at'        => now(),
        'updated_at'        => now(),
    ]);
    Field::flushCustomizableCache();

    expect(Field::customizableTypes(Webkul\Sale\Models\Product::class))
        ->toContain(Product::class)
        ->and(Field::forCustomizable(Webkul\Sale\Models\Product::class)->pluck('code')->all())
        ->toContain('shelf_life_note');
});

it('does not share custom fields between models on different tables', function (): void {
    DB::table('custom_fields')->insert([
        'code'              => 'loyalty_tier',
        'name'              => 'Loyalty tier',
        'type'              => 'text',
        'customizable_type' => Partner::class,
        'created_at'        => now(),
        'updated_at'        => now(),
    ]);
    Field::flushCustomizableCache();

    expect(Field::forCustomizable(Webkul\Sale\Models\Product::class)->pluck('code')->all())
        ->not->toContain('loyalty_tier');
});
