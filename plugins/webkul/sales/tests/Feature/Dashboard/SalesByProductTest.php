<?php

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Livewire\Livewire;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Sale\Filament\Clusters\Reporting\Pages\SalesByProduct;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

/**
 * Sales by product (reports plan R2, 2026-11-13): the permission it checks
 * is the one Shield generates, it needs that permission, and it renders
 * (the line query, groups and SQL totals run).
 */
beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('sales');
    PluginAccess::flush();
});

it('generates the permission name sales by product checks', function (): void {
    FilamentHelper::bootAdminPanel();

    $generated = collect(FilamentShield::getPages())
        ->flatMap(fn (array $page): array => array_keys($page['permissions']))
        ->all();

    expect($generated)->toContain('page_sale_sales_by_product');
});

it('opens only with the permission, and renders', function (): void {
    $company = Company::factory()->create();

    CompanyHelper::actingAsCompanyUser($company, []);
    expect(SalesByProduct::canAccess())->toBeFalse();

    CompanyHelper::actingAsCompanyUser($company, ['page_sale_sales_by_product']);
    expect(SalesByProduct::canAccess())->toBeTrue();

    FilamentHelper::bootAdminPanel();
    Livewire::test(SalesByProduct::class)->assertOk();
});
