<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Models\Move;
use Webkul\Accounting\Filament\Clusters\Reporting\Pages\MultiCurrency;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Support\ReportPdf;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/ReportPdfHelper.php';
require_once __DIR__.'/../../../../accounts/tests/Helpers/AccountHelper.php';

/**
 * "Export to PDF" on the multi-currency report (plan #9 R-2, 2026-11-25):
 * the same figures as the screen for the chosen filters, the filters in
 * words, only for someone the page opens for, never another company's.
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

    FilamentHelper::actingAs(['page_accounting_multi_currency']);

    $this->income = AccountHelper::account('income');
    $this->partner = AccountHelper::partner();
});

function multiCurrencyPdfInvoice(?Currency $currency, float $price): Move
{
    $move = AccountHelper::invoice(MoveType::OUT_INVOICE, test()->partner, null, array_filter([
        'currency_id'  => $currency?->getKey(),
        'invoice_date' => now()->toDateString(),
        'date'         => now()->toDateString(),
    ]));

    AccountHelper::productLine($move, test()->income, qty: 1, priceUnit: $price);

    return AccountHelper::post($move)->refresh();
}

it('offers Export to PDF on the multi-currency report and downloads a PDF', function () {
    multiCurrencyPdfInvoice(null, 100);

    Livewire::test(MultiCurrency::class)
        ->assertActionVisible('pdf')
        ->callAction('pdf')
        ->assertFileDownloaded(contentType: 'application/pdf');
});

it('prints the chosen currency only, says which filters, and no other company’s documents', function () {
    $foreign = Currency::factory()->create(['active' => true]);
    AccountHelper::setCurrencyRate($foreign, 11.8, now()->subDays(5)->toDateString());

    $base = multiCurrencyPdfInvoice(null, 1000);
    $abroad = multiCurrencyPdfInvoice($foreign, 118);

    // A posted invoice in that currency, but another company's.
    $stranger = Company::factory()->create();
    DB::table('accounts_account_moves')->insert([
        'company_id'          => $stranger->getKey(),
        'currency_id'         => $foreign->getKey(),
        'name'                => 'STRANGER/PDF/1',
        'move_type'           => 'out_invoice',
        'state'               => 'posted',
        'date'                => now()->toDateString(),
        'invoice_date'        => now()->toDateString(),
        'amount_total'        => 50,
        'amount_total_signed' => 50,
        'created_at'          => now(),
        'updated_at'          => now(),
    ]);

    $component = Livewire::test(MultiCurrency::class)->set('data.currency_id', $foreign->getKey());

    $html = ReportPdf::html($component->instance()->pdfReport());

    $lang = 'accounting::filament/clusters/reporting.pages.multi-currency';

    expect($html)->toContain(e($abroad->name))
        ->not->toContain(e($base->name))
        ->not->toContain('STRANGER/PDF/1')
        ->toContain(e(__($lang.'.filters.currency').': '.$foreign->name))
        ->toContain(e(__($lang.'.sections.by-currency')))
        ->toContain(e(__($lang.'.totals.total', ['base' => AccountHelper::company()->load('currency')->currency->name])));
});

it('refuses the multi-currency PDF once the viewer may no longer open the report', function () {
    multiCurrencyPdfInvoice(null, 100);

    $component = Livewire::test(MultiCurrency::class);

    FilamentHelper::actingAs([]);

    $component->assertActionHidden('pdf');

    expect(ReportPdfHelper::downloaded($component))->toBeFalse();
});
