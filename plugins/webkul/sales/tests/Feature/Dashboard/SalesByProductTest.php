<?php

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Sale\Filament\Clusters\Reporting\Pages\SalesByProduct;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/SaleHelper.php';

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

/**
 * User, 2026-11-20 (hotfix): lines of invoices made without an order are
 * listed with the order lines; an invoice made from an order is not, since
 * its order line is already there.
 */
it('lists lines of invoices made without an order, and not order invoices twice', function (): void {
    // SaleHelper::product() fills Inventory's is_storable column.
    TestBootstrapHelper::ensurePluginInstalled('inventories');

    $company = Company::factory()->create();
    $user = CompanyHelper::actingAsCompanyUser($company, ['page_sale_sales_by_product']);

    $product = SaleHelper::product(['company_id' => $company->getKey()]);
    $partner = SaleHelper::partner();
    $currencyId = SaleHelper::currency()->getKey();

    $invoice = function (string $name, float $amount, float $qty) use ($company, $product, $partner, $currencyId, $user): int {
        $moveId = DB::table('accounts_account_moves')->insertGetId([
            'company_id'          => $company->getKey(),
            'partner_id'          => $partner->getKey(),
            'name'                => $name,
            'move_type'           => 'out_invoice',
            'state'               => 'posted',
            'date'                => now()->toDateString(),
            'invoice_date'        => now()->toDateString(),
            'amount_total'        => $amount,
            'amount_total_signed' => $amount,
            'creator_id'          => $user->getKey(),
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);

        DB::table('accounts_account_move_lines')->insert([
            'move_id'        => $moveId,
            'company_id'     => $company->getKey(),
            'currency_id'    => $currencyId,
            'product_id'     => $product->getKey(),
            'display_type'   => 'product',
            'quantity'       => $qty,
            'price_subtotal' => $amount,
            'price_total'    => $amount,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return $moveId;
    };

    $invoice('INV/DIRECT/1', 80, 4);

    $fromOrder = $invoice('INV/FROM-ORDER/1', 500, 5);
    $order = SaleHelper::order(['company_id' => $company->getKey(), 'user_id' => $user->getKey()]);
    DB::table('sales_order_invoices')->insert(['order_id' => $order->getKey(), 'move_id' => $fromOrder]);

    $rows = SalesByProduct::rowsQuery()->get();

    expect($rows->pluck('document')->all())->toContain('INV/DIRECT/1')
        ->not->toContain('INV/FROM-ORDER/1')
        ->and((float) $rows->firstWhere('document', 'INV/DIRECT/1')->untaxed)->toBe(80.0)
        ->and((float) $rows->firstWhere('document', 'INV/DIRECT/1')->quantity)->toBe(4.0);

    FilamentHelper::bootAdminPanel();
    Livewire::test(SalesByProduct::class)->assertOk()->assertSee('INV/DIRECT/1');
});

/**
 * Plan #9 R-5 (user, 2026-11-25: "count, no stock"): an invoice line without
 * a product is listed once, under "No product". It never reaches an order
 * line, so it is listed from its invoice even when an order goes with that
 * invoice, whose product lines stay out.
 */
it('lists invoice lines without a product once, under No product', function (): void {
    TestBootstrapHelper::ensurePluginInstalled('inventories');

    $company = Company::factory()->create();
    $user = CompanyHelper::actingAsCompanyUser($company, ['page_sale_sales_by_product']);

    $product = SaleHelper::product(['company_id' => $company->getKey()]);
    $partner = SaleHelper::partner();
    $currencyId = SaleHelper::currency()->getKey();

    $invoice = function (string $name, array $lines) use ($company, $partner, $currencyId, $user): int {
        $moveId = DB::table('accounts_account_moves')->insertGetId([
            'company_id'          => $company->getKey(),
            'partner_id'          => $partner->getKey(),
            'name'                => $name,
            'move_type'           => 'out_invoice',
            'state'               => 'posted',
            'date'                => now()->toDateString(),
            'invoice_date'        => now()->toDateString(),
            'amount_total'        => array_sum(array_column($lines, 1)),
            'amount_total_signed' => array_sum(array_column($lines, 1)),
            'creator_id'          => $user->getKey(),
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);

        foreach ($lines as [$productId, $amount]) {
            DB::table('accounts_account_move_lines')->insert([
                'move_id'        => $moveId,
                'company_id'     => $company->getKey(),
                'currency_id'    => $currencyId,
                'product_id'     => $productId,
                'name'           => $productId ? null : 'Site survey',
                'display_type'   => 'product',
                'quantity'       => 1,
                'price_subtotal' => $amount,
                'price_total'    => $amount,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }

        return $moveId;
    };

    $invoice('INV/DIRECT/2', [[$product->getKey(), 80], [null, 30]]);

    $fromOrder = $invoice('INV/FROM-ORDER/2', [[$product->getKey(), 500], [null, 45]]);
    $order = SaleHelper::order(['company_id' => $company->getKey(), 'user_id' => $user->getKey()]);
    DB::table('sales_order_invoices')->insert(['order_id' => $order->getKey(), 'move_id' => $fromOrder]);

    $rows = SalesByProduct::rowsQuery()->get();
    $noProduct = __('sales::filament/clusters/reporting.by-product.columns.none');

    $unnamed = $rows->where('product_name', $noProduct);

    expect($unnamed->pluck('untaxed')->map(fn ($amount): float => (float) $amount)->sort()->values()->all())->toBe([30.0, 45.0])
        ->and($unnamed->pluck('document')->sort()->values()->all())->toBe(['INV/DIRECT/2', 'INV/FROM-ORDER/2'])
        // The order's invoice adds only its line without a product.
        ->and($rows->where('document', 'INV/FROM-ORDER/2')->count())->toBe(1)
        ->and($rows->where('document', 'INV/DIRECT/2')->count())->toBe(2);

    FilamentHelper::bootAdminPanel();
    Livewire::test(SalesByProduct::class)->assertOk()->assertSee($noProduct);
});
