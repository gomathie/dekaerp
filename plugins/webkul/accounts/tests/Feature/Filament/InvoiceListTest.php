<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\ListInvoices;
use Webkul\Account\Models\Move;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../Helpers/AccountHelper.php';

/**
 * TRACE365 reference lists, 2026-11-23 (plan #10 R-9): status tabs carry how
 * many documents they hold, and the list shows what was paid and what is due
 * in each invoice's own currency.
 */
beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');

    DB::table('plugins')->updateOrInsert(
        ['name' => 'accounts'],
        ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
    );

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    FilamentHelper::actingAs(['view_any_account_invoice']);
});

function listedInvoice(float $amount, array $overrides = [], bool $post = true): Move
{
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, overrides: $overrides);
    AccountHelper::productLine($invoice, AccountHelper::account('income'), qty: 1, priceUnit: $amount);
    AccountHelper::compute($invoice);

    return $post ? AccountHelper::post($invoice)->refresh() : $invoice->refresh();
}

it('counts the documents on the draft, to pay and overdue tabs', function () {
    listedInvoice(100, post: false);
    listedInvoice(200, ['invoice_date_due' => now()->addDays(10)->toDateString()]);
    listedInvoice(300, ['invoice_date_due' => now()->subDays(5)->toDateString()]);
    AccountHelper::pay(listedInvoice(400));

    $tabs = Livewire::test(ListInvoices::class)->instance()->getPresetTableViews();

    expect($tabs['draft']->getBadge())->toBe('1')
        ->and($tabs['to_pay']->getBadge())->toBe('2')
        ->and($tabs['overdue']->getBadge())->toBe('1');
});

it('shows no count on a tab with nothing in it', function () {
    listedInvoice(200, ['invoice_date_due' => now()->addDays(10)->toDateString()]);

    $tabs = Livewire::test(ListInvoices::class)->instance()->getPresetTableViews();

    expect($tabs['overdue']->getBadge())->toBeNull();
});

it('shows what was paid and what is still due on each invoice', function () {
    $invoice = listedInvoice(1000);
    AccountHelper::pay($invoice, 400);
    $invoice->refresh();

    $currency = $invoice->currency->name;

    Livewire::test(ListInvoices::class)
        ->set('activeTableView', 'posted')
        ->assertTableColumnStateSet('amount_paid', 400.0, $invoice)
        ->assertTableColumnStateSet('amount_residual_signed', 600.0, $invoice)
        ->assertSee(money(400, $currency));
});
