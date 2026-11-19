<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\EditInvoice;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Support\Models\Currency;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../Helpers/AccountHelper.php';

/**
 * Client report, 2026-11-19: switching an invoice's currency changed only the
 * label (lines kept the old prices, the summary its old symbol), and a
 * currency without a rate converted at 1:1, so a local price landed unchanged
 * on a USD invoice.
 */
beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');

    DB::table('plugins')->updateOrInsert(
        ['name' => 'accounts'],
        ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
    );

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    FilamentHelper::actingAs(['view_any_account_invoice', 'update_account_invoice']);

    $this->company = AccountHelper::company();
    $this->base = $this->company->currency;

    $this->foreign = AccountHelper::otherCurrency();
    $this->foreign->rates()->delete();
    AccountHelper::setCurrencyRate($this->foreign, 2.0);

    $this->rateless = Currency::query()
        ->whereNotIn('id', [$this->base->id, $this->foreign->id])
        ->firstOrFail();
    $this->rateless->rates()->delete();
});

function invoiceLineCurrencyComponent(float $priceUnit)
{
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE);
    AccountHelper::productLine($invoice, AccountHelper::account('income'), qty: 1, priceUnit: $priceUnit);
    AccountHelper::compute($invoice);

    return Livewire::test(EditInvoice::class, ['record' => $invoice->id]);
}

function firstLinePrice($component): float
{
    return (float) collect($component->get('data.products'))->first()['price_unit'];
}

it('knows when a real exchange rate exists', function () {
    expect($this->base->canConvertTo($this->base, $this->company))->toBeTrue()
        ->and($this->base->canConvertTo($this->foreign, $this->company))->toBeTrue()
        ->and($this->base->canConvertTo($this->rateless, $this->company))->toBeFalse()
        ->and($this->rateless->canConvertTo($this->base, $this->company))->toBeFalse();
});

it('converts every line when the invoice currency is switched', function () {
    $component = invoiceLineCurrencyComponent(100);

    expect(firstLinePrice($component))->toBe(100.0);

    $component->set('data.currency_id', $this->foreign->id);

    expect(firstLinePrice($component))->toBe(200.0)
        ->and((float) collect($component->get('data.products'))->first()['price_subtotal'])->toBe(200.0);
});

it('converts back when the currency is switched back', function () {
    $component = invoiceLineCurrencyComponent(100);

    $component->set('data.currency_id', $this->foreign->id);
    $component->set('data.currency_id', $this->base->id);

    expect(firstLinePrice($component))->toBe(100.0);
});

it('leaves prices unconverted and warns when there is no exchange rate', function () {
    $component = invoiceLineCurrencyComponent(100);

    $component->set('data.currency_id', $this->rateless->id);

    expect(firstLinePrice($component))->toBe(100.0);

    $component->assertNotified(__('accounts::filament/resources/invoice.form.notifications.missing-rate.title'));
});
