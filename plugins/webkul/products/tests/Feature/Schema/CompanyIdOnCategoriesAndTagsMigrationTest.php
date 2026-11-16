<?php

use Illuminate\Support\Facades\Schema;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('products');
});

/**
 * The migrations are objects under test here rather than steps of the test
 * database: only running the real down() and up() proves they are reversible,
 * and both are rolled back with the test transaction.
 *
 * @return array<int, object>
 */
function companyIdMigrations(): array
{
    return [
        require base_path('plugins/webkul/products/database/migrations/2026_10_29_120000_add_company_id_to_products_categories_and_tags_tables.php'),
        require base_path('plugins/webkul/partners/database/migrations/2026_10_29_120001_add_company_id_to_partners_tags_table.php'),
    ];
}

it('adds a nullable company column to categories and to both tag tables', function () {
    foreach (['products_categories', 'products_tags', 'partners_tags'] as $table) {
        $column = collect(Schema::getColumns($table))->firstWhere('name', 'company_id');

        expect($column)->not->toBeNull()
            ->and($column['nullable'])->toBeTrue();
    }
});

it('indexes the new company columns', function () {
    foreach (['products_categories', 'products_tags', 'partners_tags'] as $table) {
        $indexed = collect(Schema::getIndexes($table))
            ->contains(fn (array $index): bool => in_array('company_id', $index['columns'], true));

        expect($indexed)->toBeTrue();
    }
});

it('makes a tag name unique per company instead of globally', function (string $table) {
    $indexes = collect(Schema::getIndexes($table));

    expect($indexes->contains(fn (array $index): bool => $index['unique'] && $index['columns'] === ['company_id', 'name']))
        ->toBeTrue()
        ->and($indexes->contains(fn (array $index): bool => $index['unique'] && $index['columns'] === ['name']))
        ->toBeFalse();
})->with(['products_tags', 'partners_tags']);

it('reverses and re-applies both migrations', function () {
    [$productsMigration, $partnersMigration] = companyIdMigrations();

    $partnersMigration->down();
    $productsMigration->down();

    foreach (['products_categories', 'products_tags', 'partners_tags'] as $table) {
        expect(Schema::hasColumn($table, 'company_id'))->toBeFalse();
    }

    $productsMigration->up();
    $partnersMigration->up();

    foreach (['products_categories', 'products_tags', 'partners_tags'] as $table) {
        expect(Schema::hasColumn($table, 'company_id'))->toBeTrue();
    }
});
