<?php

use Webkul\Product\Models\Attribute;
use Webkul\Product\Models\AttributeOption;
use Webkul\Product\Models\Product;
use Webkul\Product\Models\ProductAttribute;
use Webkul\Product\Models\ProductAttributeValue;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('products');

    Package::$plugins = Plugin::all()->keyBy('name');
});

it('preserves the parent product name when generating variants repeatedly', function () {
    $product = Product::factory()->create([
        'name' => 'Warehouse pallet',
    ]);
    $attribute = Attribute::factory()->create(['name' => 'Size']);
    $option = AttributeOption::factory()->create([
        'attribute_id' => $attribute->id,
        'name'         => 'Large',
    ]);
    $productAttribute = ProductAttribute::factory()->create([
        'product_id'   => $product->id,
        'attribute_id' => $attribute->id,
    ]);

    ProductAttributeValue::query()->create([
        'product_id'           => $product->id,
        'attribute_id'         => $attribute->id,
        'product_attribute_id' => $productAttribute->id,
        'attribute_option_id'  => $option->id,
        'extra_price'          => 0,
    ]);

    $product->generateVariants();
    expect($product->refresh()->name)->toBe('Warehouse pallet');

    $product->generateVariants();
    expect($product->refresh()->name)->toBe('Warehouse pallet');
});
