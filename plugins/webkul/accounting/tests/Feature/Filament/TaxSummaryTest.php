<?php

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Accounting\Filament\Clusters\Reporting\Pages\TaxSummary;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../accounts/tests/Helpers/AccountHelper.php';
require_once __DIR__.'/../../Helpers/ReportHelper.php';

/**
 * Tax Summary (accounting plan A7). Proven with the plan's Ghana figures:
 * GHS 1,000 with VAT 15%, NHIL 2.5% and GETFund 2.5% shows base 1,000 and
 * tax 150, 25 and 25, each tax on its own row; a credit note reduces it.
 */
beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounting');

    DB::table('plugins')->whereIn('name', ['accounts', 'accounting'])->update([
        'is_installed' => true,
        'is_active'    => true,
        'updated_at'   => now(),
    ]);

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    FilamentHelper::actingAs(['page_accounting_tax_summary']);

    $this->income = AccountHelper::account('income');
    $this->partner = AccountHelper::partner();
});

function taxSummaryGhanaInvoice(array $taxes, MoveType $type = MoveType::OUT_INVOICE, float $price = 1000): void
{
    $move = AccountHelper::invoice($type, test()->partner);

    AccountHelper::productLine($move, test()->income, qty: 1, priceUnit: $price, taxes: array_values($taxes));

    AccountHelper::post($move);
}

it('generates the page permission the tax summary checks', function () {
    FilamentHelper::bootAdminPanel();

    $generated = collect(FilamentShield::getPages())
        ->flatMap(fn (array $page): array => array_keys($page['permissions']))
        ->all();

    expect($generated)->toContain('page_accounting_tax_summary');
});

it('renders the tax summary page', function () {
    Livewire::test(TaxSummary::class)->assertOk();
});

it('shows each Ghana tax on its own row with the right base and amount', function () {
    $taxes = [
        'vat'     => AccountHelper::taxWithAccounts(15),
        'nhil'    => AccountHelper::taxWithAccounts(2.5),
        'getfund' => AccountHelper::taxWithAccounts(2.5),
    ];

    taxSummaryGhanaInvoice($taxes);

    $data = ReportHelper::data(TaxSummary::class, 'taxSummaryData');
    $byTax = collect($data['sales'])->keyBy('id');

    expect($byTax[$taxes['vat']->id]['tax'])->toBe(150.0)
        ->and($byTax[$taxes['vat']->id]['base'])->toBe(1000.0)
        ->and($byTax[$taxes['nhil']->id]['tax'])->toBe(25.0)
        ->and($byTax[$taxes['getfund']->id]['tax'])->toBe(25.0)
        ->and($data['totals']['collected'])->toBe(200.0)
        ->and($data['totals']['net'])->toBe(200.0);
});

it('lets a credit note reduce the tax of its period', function () {
    $vat = AccountHelper::taxWithAccounts(15);

    taxSummaryGhanaInvoice(['vat' => $vat]);
    taxSummaryGhanaInvoice(['vat' => $vat], MoveType::OUT_REFUND, 200);

    $row = collect(ReportHelper::data(TaxSummary::class, 'taxSummaryData')['sales'])->firstWhere('id', $vat->id);

    expect($row['tax'])->toBe(120.0)
        ->and($row['base'])->toBe(800.0);
});

it('nets tax paid on purchases against tax collected', function () {
    taxSummaryGhanaInvoice(['vat' => AccountHelper::taxWithAccounts(15)]);

    $purchaseVat = AccountHelper::taxWithAccounts(15, type: TypeTaxUse::PURCHASE);
    $bill = AccountHelper::invoice(MoveType::IN_INVOICE, $this->partner, null, ['invoice_date' => now()->toDateString()]);
    AccountHelper::productLine($bill, AccountHelper::account('expense'), qty: 1, priceUnit: 400, taxes: [$purchaseVat]);
    AccountHelper::post($bill);

    $totals = ReportHelper::data(TaxSummary::class, 'taxSummaryData')['totals'];

    expect($totals['collected'])->toBe(150.0)
        ->and($totals['paid'])->toBe(60.0)
        ->and($totals['net'])->toBe(90.0);
});
