<?php

use Barryvdh\DomPDF\Facade\Pdf;
use Webkul\Account\Models\Tax;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Sale\Support\OrderDocument;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../accounts/tests/Helpers/AccountHelper.php';
require_once __DIR__.'/../../Helpers/SaleHelper.php';

/**
 * TRACE365, 2026-11-23 (plan #10 R-8): a printed quotation lists each tax on
 * its own line (NHIL/GETFund, VAT), computed as the order computes its tax,
 * so the rows add up to the order's total.
 */
beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('sales');
    PluginAccess::flush();

    SaleHelper::actingAsAdmin();
});

it('prints a quotation with each tax on its own line and a real PDF', function (): void {
    $levy = AccountHelper::taxWithAccounts(5);
    Tax::query()->whereKey($levy->getKey())->update(['name' => 'NHIL/GETFund 5%']);

    $vat = AccountHelper::taxWithAccounts(15);
    Tax::query()->whereKey($vat->getKey())->update(['name' => 'VAT 15%']);

    $order = SaleHelper::order();
    // The accounts helper's product: SaleHelper::product() sets is_storable,
    // a column only the inventories plugin adds.
    SaleHelper::line($order, AccountHelper::product(), qty: 2, priceUnit: 1000, taxes: [$levy->refresh(), $vat->refresh()]);
    $order = SaleHelper::compute($order)->refresh();

    $currency = $order->currency->name;
    $document = OrderDocument::data($order);
    $totals = collect($document['totals'])->pluck('value', 'label')->all();

    expect($document['title'])->toBe(__('support::documents.titles.quotation'))
        ->and($document['lines'][0]['amount'])->toBe(money(2000, $currency))
        ->and($totals['NHIL/GETFund 5%'])->toBe(money(100, $currency))
        ->and($totals['VAT 15%'])->toBe(money(300, $currency))
        ->and($totals[__('support::documents.total')])->toBe(money((float) $order->amount_total, $currency))
        ->and((float) $order->amount_total)->toBe(2400.0);

    $html = view('sales::sales.quotation', ['record' => $order])->render();

    expect($html)->toContain($order->name)
        ->and(Pdf::loadHTML($html)->output())->toStartWith('%PDF');
});
