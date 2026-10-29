<?php

use Illuminate\Support\Facades\DB;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('products');

    clearCatalogue();
    seedExpectedStartingState();
});

/**
 * The rows this command is about are named by id, so the test has to start from
 * the production shape rather than from whatever a factory would produce.
 */
function clearCatalogue(): void
{
    DB::table('products_product_tag')->delete();
    DB::table('products_products')->delete();
    DB::table('products_categories')->delete();
    DB::table('products_tags')->delete();
    DB::table('partners_tags')->delete();
}

/**
 * Production before the backfill (docs/company-admin-role-plan.md §4k): the
 * seeded category tree, shared; product 1 shared and pointing at category 2; no
 * tags at all.
 */
function seedExpectedStartingState(): void
{
    $categories = [
        1  => ['All', null],
        2  => ['Consumable', 1],
        3  => ['Expenses', 1],
        4  => ['Home Construction', 1],
        5  => ['Internal', 1],
        6  => ['Saleable', 1],
        7  => ['Office Furniture', 6],
        8  => ['Outdoor furniture', 6],
        9  => ['Services', 6],
        10 => ['Saleable', 9],
    ];

    foreach ($categories as $id => [$name, $parentId]) {
        DB::table('products_categories')->insert([
            'id'          => $id,
            'name'        => $name,
            'full_name'   => $name,
            'parent_path' => $parentId === null ? '/' : '/'.$parentId.'/',
            'parent_id'   => $parentId,
            'company_id'  => null,
            'creator_id'  => null,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    $uomId = DB::table('unit_of_measures')->value('id');

    DB::table('products_products')->insert([
        'id'          => 1,
        'type'        => 'goods',
        'name'        => 'Company 2 first product',
        'uom_id'      => $uomId,
        'uom_po_id'   => $uomId,
        'category_id' => 2,
        'company_id'  => null,
        'creator_id'  => null,
        'created_at'  => now(),
        'updated_at'  => now(),
    ]);

    // Company 1 comes from the installation seeder; company 2 is inserted
    // directly so it exists without the products observer provisioning it a
    // root category, which would change the state under test.
    if (! DB::table('companies')->where('id', 2)->exists()) {
        DB::table('companies')->insert([
            'id'          => 2,
            'name'        => 'Second client',
            'company_id'  => 'BACKFILL-COMPANY-2',
            'is_active'   => true,
            'currency_id' => DB::table('currencies')->value('id'),
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }
}

it('writes nothing on a dry run', function () {
    expect(DB::table('companies')->where('id', 1)->exists())->toBeTrue();

    $this->artisan('products:company-backfill', ['--dry-run' => true])
        ->expectsOutputToContain('Dry run: nothing was written')
        ->assertExitCode(0);

    expect(DB::table('products_categories')->whereNotNull('company_id')->count())->toBe(0)
        ->and(DB::table('products_categories')->count())->toBe(10)
        ->and(DB::table('products_products')->where('id', 1)->value('company_id'))->toBeNull()
        ->and((int) DB::table('products_products')->where('id', 1)->value('category_id'))->toBe(2);
});

it('files the categories and product 1 under their company', function () {
    $this->artisan('products:company-backfill')
        ->expectsOutputToContain('Applied')
        ->assertExitCode(0);

    expect(DB::table('products_categories')->whereBetween('id', [1, 10])->where('company_id', 1)->count())->toBe(10);

    $copies = DB::table('products_categories')->where('company_id', 2)->orderBy('id')->get();

    expect($copies)->toHaveCount(2)
        ->and($copies[0]->name)->toBe('All')
        ->and($copies[0]->parent_id)->toBeNull()
        ->and($copies[0]->parent_path)->toBe('/')
        ->and($copies[0]->full_name)->toBe('All')
        ->and($copies[1]->name)->toBe('Consumable')
        ->and((int) $copies[1]->parent_id)->toBe((int) $copies[0]->id)
        ->and($copies[1]->parent_path)->toBe('/'.$copies[0]->id.'/')
        ->and($copies[1]->full_name)->toBe('All / Consumable');

    $product = DB::table('products_products')->where('id', 1)->first();

    expect((int) $product->company_id)->toBe(2)
        ->and((int) $product->category_id)->toBe((int) $copies[1]->id);
});

it('reports nothing to do when run a second time', function () {
    $this->artisan('products:company-backfill')->assertExitCode(0);

    $after = DB::table('products_categories')->orderBy('id')->get(['id', 'company_id', 'parent_id'])->toArray();

    $this->artisan('products:company-backfill')
        ->expectsOutputToContain('Nothing to do')
        ->assertExitCode(0);

    expect(DB::table('products_categories')->orderBy('id')->get(['id', 'company_id', 'parent_id'])->toArray())
        ->toEqual($after)
        ->and(DB::table('products_categories')->count())->toBe(12);
});

it('refuses an unexpected starting state and says what differs', function () {
    DB::table('products_categories')->where('id', 2)->update(['parent_id' => 3]);

    $this->artisan('products:company-backfill')
        ->expectsOutputToContain('The database does not match the expected starting state. Nothing was written.')
        ->expectsOutputToContain('Category #2 has parent 3, expected 1.')
        ->assertExitCode(1);

    expect(DB::table('products_categories')->whereNotNull('company_id')->count())->toBe(0)
        ->and(DB::table('products_categories')->count())->toBe(10);
});

it('refuses to touch tags the plan did not account for', function () {
    DB::table('products_tags')->insert([
        'name'       => 'Fragile',
        'color'      => '#123456',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->artisan('products:company-backfill')
        ->expectsOutputToContain('products_tags holds 1 tag(s)')
        ->assertExitCode(1);

    expect(DB::table('products_categories')->whereNotNull('company_id')->count())->toBe(0);
});
