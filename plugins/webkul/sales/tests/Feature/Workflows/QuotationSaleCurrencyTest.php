<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Account\Enums\InvoicePolicy;
use Webkul\Account\Models\CompanySetting;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\PaymentTerm;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages\CreateQuotation;
use Webkul\Sale\Models\Order;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\CurrencyRate;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/SaleHelper.php';

/**
 * "Ask which currency each sale is in" on quotations and sales orders (plan
 * #10 MC-2b, TRACE365, user 2026-11-24). Same dialog and rules as invoices
 * (accounts SaleCurrencyQuestionTest); the order keeps its confirmed rate in
 * sales_orders.currency_rate, and its invoice inherits it.
 */
beforeEach(function () {
    foreach (['inventories', 'sales', 'accounts'] as $plugin) {
        TestBootstrapHelper::ensurePluginInstalled($plugin);

        DB::table('plugins')->updateOrInsert(
            ['name' => $plugin],
            ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
        );
    }

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    SaleHelper::actingAsAdmin();

    $this->company = SaleHelper::company();
    $this->foreign = Currency::query()->where('id', '!=', $this->company->currency_id)->firstOrFail();
    $this->foreign->forceFill(['active' => true])->save();

    CurrencyRate::factory()->create([
        'currency_id' => $this->foreign->id,
        'company_id'  => $this->company->id,
        'rate'        => 10.0,
        'name'        => now()->subDay()->toDateString(),
    ]);

    $this->product = SaleHelper::product(['invoice_policy' => InvoicePolicy::ORDER->value]);
});

/** An order in the foreign currency, dated today, with a rate the person confirmed. */
function confirmedRateOrder(float $rate): Order
{
    $order = SaleHelper::order(['currency_id' => test()->foreign->id, 'date_order' => now()]);

    $order->forceFill([
        'currency_rate'              => $rate,
        'currency_rate_source'       => Move::RATE_MANUAL,
        'currency_rate_confirmed_by' => auth()->id(),
    ])->save();

    return $order;
}

it('keeps a confirmed rate on a quotation through every recompute and drops it when the currency changes alone', function () {
    $order = confirmedRateOrder(12.5);
    SaleHelper::line($order, $this->product, 2, 125);
    SaleHelper::compute($order);

    expect((float) $order->refresh()->currency_rate)->toBe(12.5)
        ->and($order->currency_rate_source)->toBe(Move::RATE_MANUAL)
        // 250 in the order's currency at 12.5 per company unit is 20.
        ->and(round((float) $order->amount_total / (float) $order->currency_rate, 2))->toBe(20.0);

    // Another currency without a new rate: the old rate must not price it.
    $order->update(['currency_id' => $this->company->currency_id]);
    SaleHelper::compute($order);

    expect((float) $order->refresh()->currency_rate)->toBe(1.0)
        ->and($order->currency_rate_source)->toBeNull()
        ->and($order->currency_rate_confirmed_by)->toBeNull();
});

it('lets no payload pin a rate on an order: the source and the confirming user are not fillable', function () {
    $order = new Order(['currency_rate_source' => Move::RATE_MANUAL, 'currency_rate_confirmed_by' => 1]);

    expect($order->currency_rate_source)->toBeNull()
        ->and($order->currency_rate_confirmed_by)->toBeNull();
});

it('gives the invoice made from a quotation the rate confirmed on it', function () {
    $order = confirmedRateOrder(12.5);
    SaleHelper::line($order, $this->product, 2, 125);
    SaleHelper::confirm($order);

    $invoice = SaleHelper::createInvoice($order);

    expect((float) $invoice->invoice_currency_rate)->toBe(12.5)
        ->and($invoice->currency_rate_source)->toBe(Move::RATE_MANUAL)
        ->and((int) $invoice->currency_rate_confirmed_by)->toBe((int) auth()->id());
});

it('asks the currency on a new quotation and keeps a corrected rate, noted on its history', function () {
    $company = Company::query()->findOrFail(current_company_id());

    DB::table('company_currencies')->where('company_id', $company->id)->delete();
    DB::table('company_currencies')->insert([
        ['company_id' => $company->id, 'currency_id' => $company->currency_id],
        ['company_id' => $company->id, 'currency_id' => $this->foreign->id],
    ]);
    CompanySetting::withoutGlobalScopes()->updateOrCreate(['company_id' => $company->id], ['ask_sale_currency' => true]);

    CurrencyRate::factory()->create([
        'currency_id' => $this->foreign->id,
        'company_id'  => $company->id,
        'rate'        => 10.0,
        'name'        => now()->toDateString(),
    ]);

    $component = Livewire::test(CreateQuotation::class);

    expect($component->get('defaultAction'))->toBe('askSaleCurrency');

    $component
        ->assertActionVisible('askSaleCurrency')
        ->callAction('askSaleCurrency', data: ['currency_id' => $this->foreign->id, 'rate' => 12.5])
        ->assertSet('data.currency_id', $this->foreign->id)
        ->assertSet('saleCurrencyRate', 12.5)
        ->fillForm([
            'partner_id'    => SaleHelper::partner()->id,
            'date_order'    => now(),
            'validity_date'   => now()->addDays(30),
            'payment_term_id' => PaymentTerm::query()->value('id'),
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $order = Order::query()->withoutGlobalScopes()->latest('id')->firstOrFail();

    expect((float) $order->currency_rate)->toBe(12.5)
        ->and($order->currency_rate_source)->toBe(Move::RATE_MANUAL)
        ->and((int) $order->currency_rate_confirmed_by)->toBe((int) auth()->id())
        ->and($order->messages()->where('type', 'note')->count())->toBe(1);

    CompanySetting::withoutGlobalScopes()->where('company_id', $company->id)->update(['ask_sale_currency' => false]);

    expect(Livewire::test(CreateQuotation::class)->get('defaultAction'))->toBeNull();
});
