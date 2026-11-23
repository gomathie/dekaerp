<?php

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions\PreviewAction;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\ViewInvoice;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\Tax;
use Webkul\Account\Support\MoveDocument;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Support\Support\PrintedDocument;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../Helpers/AccountHelper.php';

/**
 * TRACE365, 2026-11-23 (plan #10 R-8): the printed invoice compared badly
 * with their Zoho one. The old template showed the product name only, one
 * "Tax" line for NHIL/GETFund and VAT together, a "Discount" that added the
 * lines' discount percentages as money, and no balance due.
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

/** A posted invoice: 2 × 1,000 with NHIL/GETFund 5% and VAT 15%. */
function printedInvoice(array $overrides = []): Move
{
    // Renamed with a query: saving the model re-runs its repartition
    // validation, which is not what this test is about.
    $levy = AccountHelper::taxWithAccounts(5);
    Tax::query()->whereKey($levy->getKey())->update(['name' => 'NHIL/GETFund 5%']);

    $vat = AccountHelper::taxWithAccounts(15);
    Tax::query()->whereKey($vat->getKey())->update(['name' => 'VAT 15%']);

    $levy->refresh();
    $vat->refresh();

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, overrides: $overrides);
    AccountHelper::productLine($invoice, AccountHelper::account('income'), qty: 2, priceUnit: 1000, taxes: [$levy, $vat]);
    AccountHelper::compute($invoice);

    return AccountHelper::post($invoice)->refresh();
}

it('prints each tax on its own line, the line amounts and the balance due', function () {
    $invoice = printedInvoice();
    $currency = $invoice->currency->name;

    $document = MoveDocument::data($invoice);
    $totals = collect($document['totals'])->pluck('value', 'label')->all();

    expect($document['lines'][0]['amount'])->toBe(money(2000, $currency))
        ->and($document['lines'][0]['quantity'])->toBe('2')
        ->and($totals['NHIL/GETFund 5%'])->toBe(money(100, $currency))
        ->and($totals['VAT 15%'])->toBe(money(300, $currency))
        ->and($totals[__('support::documents.total')])->toBe(money(2400, $currency))
        ->and($totals[__('support::documents.balance-due')])->toBe(money(2400, $currency))
        // No discount on any line: no Discount column and no discount total.
        ->and($document['show_discount'])->toBeFalse()
        ->and($totals)->not->toHaveKey(__('accounts::account-manager.documents.labels.discount'));
});

it('shows what was paid and what is left after a part payment', function () {
    $invoice = printedInvoice();
    $currency = $invoice->currency->name;

    AccountHelper::pay($invoice, 1000);

    $totals = collect(MoveDocument::data($invoice->refresh())['totals'])->pluck('value', 'label')->all();

    expect($totals[__('support::documents.amount-paid')])->toBe(money(-1000, $currency))
        ->and($totals[__('support::documents.balance-due')])->toBe(money(1400, $currency));
});

it('renders the document to a real PDF', function () {
    $invoice = printedInvoice();

    $html = view('accounts::invoice/actions/preview.index', ['record' => $invoice])->render();

    expect($html)->toContain($invoice->name)
        ->and($html)->toContain('NHIL/GETFund 5%')
        ->and(Pdf::loadHTML($html)->output())->toStartWith('%PDF');
});

it('strips script from the notes, which the preview renders in the page', function () {
    $invoice = printedInvoice(['narration' => '<p>Thank you for your business</p><script>alert(1)</script>']);

    $html = view('accounts::invoice/actions/preview.index', ['record' => $invoice])->render();

    expect($html)->toContain('Thank you for your business')
        ->and($html)->not->toContain('<script>alert(1)</script>');
});

it('leaves out the notes heading when the note is empty rich text', function () {
    $invoice = printedInvoice(['narration' => '<p></p>']);

    $html = view('accounts::invoice/actions/preview.index', ['record' => $invoice])->render();

    expect($html)->not->toContain('>'.__('support::documents.notes').'<');
});

/**
 * v1.16.9 shipped Print returning dompdf's ->download() Response. Livewire
 * JSON-encodes a plain Response from an action, so the PDF bytes failed with
 * "Malformed UTF-8 characters" (Sentry DEKAERP-H, 2026-11-23). It must be a
 * streamed download, as the Logistics waybill test also pins.
 */
it('delivers Print from the invoice preview as a PDF download', function () {
    FilamentHelper::actingAs(['view_any_account_invoice', 'view_account_invoice']);

    $invoice = printedInvoice();

    Livewire::test(ViewInvoice::class, ['record' => $invoice->getKey()])
        ->callAction(['customers.invoice.preview', 'print'])
        ->assertFileDownloaded(contentType: 'application/pdf');
});

it('names the printed file after the document, not the day it was printed', function () {
    $invoice = printedInvoice();
    $invoice->forceFill(['name' => 'INV/2026/00007']);

    expect(PreviewAction::fileName($invoice))->toBe('invoice-INV-2026-00007');
});

it('only prints a branding colour that is a plain hex colour', function () {
    $company = AccountHelper::company();

    $company->forceFill(['color' => '#12ab34']);
    expect(PrintedDocument::accent($company))->toBe('#12ab34');

    // Printed into a style rule: anything else falls back to the default.
    $company->forceFill(['color' => 'red;} body { display:none']);
    expect(PrintedDocument::accent($company))->toBe('#1f2937');

    // The header text is white: a pale branding colour would hide it.
    $company->forceFill(['color' => '#d4d4d4']);
    expect(PrintedDocument::accent($company))->toBe('#1f2937');
});
