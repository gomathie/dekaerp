<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\CreateInvoice;
use Webkul\Account\Models\CompanySetting;
use Webkul\Account\Models\Move;
use Webkul\Account\Services\SaleCurrencyQuestion;
use Webkul\Partner\Models\Partner;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/AccountHelper.php';

/**
 * "Ask which currency each sale is in" (plan #11 MC-2, TRACE365, user
 * 2026-11-24).
 */
beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');

    DB::table('plugins')->updateOrInsert(
        ['name' => 'accounts'],
        ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
    );

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    AccountHelper::actingAsAdmin();

    $this->income = AccountHelper::account('income');
    $this->partner = AccountHelper::partner();
});

/**
 * Lets the company choose between its base currency and $foreign only.
 */
function allowSaleCurrencies(Company $company, Currency $foreign, bool $ask = true): void
{
    $foreign->forceFill(['active' => true])->save();

    DB::table('company_currencies')->where('company_id', $company->getKey())->delete();
    DB::table('company_currencies')->insert([
        ['company_id' => $company->getKey(), 'currency_id' => $company->currency_id],
        ['company_id' => $company->getKey(), 'currency_id' => $foreign->getKey()],
    ]);

    CompanySetting::withoutGlobalScopes()->updateOrCreate(['company_id' => $company->getKey()], ['ask_sale_currency' => $ask]);
}

it('keeps a confirmed rate through every recompute and drops it when the currency changes alone', function () {
    $foreign = AccountHelper::otherCurrency();
    AccountHelper::setCurrencyRate($foreign, 10.0, now()->subDay()->toDateString());

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner, null, ['currency_id' => $foreign->getKey()]);

    expect((float) $invoice->invoice_currency_rate)->toBe(10.0);

    $invoice->forceFill([
        'invoice_currency_rate'      => 12.5,
        'currency_rate_source'       => Move::RATE_MANUAL,
        'currency_rate_confirmed_by' => auth()->id(),
    ])->save();

    AccountHelper::productLine($invoice, $this->income, qty: 1, priceUnit: 125);
    AccountHelper::compute($invoice);

    // Saving again (a date change) used to re-read the rate table.
    $invoice->refresh()->update(['invoice_date' => now()->subDays(2)->toDateString()]);

    $line = $invoice->refresh()->lines->firstWhere('display_type', DisplayType::PRODUCT);

    expect((float) $invoice->invoice_currency_rate)->toBe(12.5)
        ->and($invoice->currency_rate_source)->toBe(Move::RATE_MANUAL)
        // 125 in the invoice's currency at 12.5 per company unit is 10.
        ->and(abs((float) $line->balance))->toBe(10.0);

    // Another currency without a new rate: the old rate must not price it.
    $invoice->update(['currency_id' => AccountHelper::currency()->getKey()]);
    $invoice->refresh();

    expect((float) $invoice->invoice_currency_rate)->toBe(1.0)
        ->and($invoice->currency_rate_source)->toBeNull()
        ->and($invoice->currency_rate_confirmed_by)->toBeNull();
});

it('lets no payload pin a rate: the source and the confirming user are not fillable', function () {
    $move = new Move(['currency_rate_source' => Move::RATE_MANUAL, 'currency_rate_confirmed_by' => 1]);

    expect($move->currency_rate_source)->toBeNull()
        ->and($move->currency_rate_confirmed_by)->toBeNull();
});

it('asks only when switched on and the company has more than one currency, base first', function () {
    $company = AccountHelper::company();
    $foreign = AccountHelper::otherCurrency();
    $question = app(SaleCurrencyQuestion::class);

    allowSaleCurrencies($company, $foreign, ask: false);

    expect($question->isAsked($company->getKey()))->toBeFalse();

    CompanySetting::withoutGlobalScopes()->where('company_id', $company->getKey())->update(['ask_sale_currency' => true]);

    expect($question->isAsked($company->getKey()))->toBeTrue()
        ->and(array_key_first($question->options($company->getKey())))->toBe((int) $company->currency_id);

    // Only its own currency left: nothing to ask.
    DB::table('company_currencies')->where('company_id', $company->getKey())->where('currency_id', $foreign->getKey())->delete();

    expect($question->isAsked($company->getKey()))->toBeFalse()
        ->and($question->isAsked(null))->toBeFalse();
});

it('shows a rate the way people read it, warns when it is old, and offers none when there is none', function () {
    $company = AccountHelper::company()->load('currency');
    $question = app(SaleCurrencyQuestion::class);

    $old = Currency::factory()->create(['active' => true]);
    AccountHelper::setCurrencyRate($old, 0.05, now()->subDays(10)->toDateString());

    $none = Currency::factory()->create(['active' => true]);

    $feed = $question->feedRate($old, $company);
    $shown = $question->display($feed['rate'], $company->currency, $old);

    expect($feed['rate'])->toBe(0.05)
        ->and($feed['stale'])->toBeTrue()
        // 0.05 per company unit reads as "1 foreign = 20 company".
        ->and($shown)->toMatchArray(['from' => $old->name, 'to' => $company->currency->name, 'amount' => 20.0, 'baseFirst' => false])
        ->and($question->fromDisplay(20.0, false))->toBe(0.05)
        ->and($question->fromDisplay(11.8, true))->toBe(11.8)
        ->and($question->feedRate($none, $company))->toBeNull();

    expect(fn () => $question->fromDisplay(0.0, true))->toThrow(InvalidArgumentException::class);
});

it('opens the question with a new invoice and keeps a corrected rate on the created invoice', function () {
    $company = Company::query()->findOrFail(current_company_id());
    $foreign = AccountHelper::otherCurrency();
    allowSaleCurrencies($company, $foreign);
    AccountHelper::setCurrencyRate($foreign, 10.0, now()->toDateString());

    $customer = Partner::factory()->create(['company_id' => $company->getKey()]);

    $component = Livewire::test(CreateInvoice::class);

    expect($component->get('defaultAction'))->toBe('askSaleCurrency');

    $component
        ->callAction('askSaleCurrency', data: ['currency_id' => $foreign->getKey(), 'rate' => 12.5])
        ->assertHasNoActionErrors()
        ->assertSet('data.currency_id', $foreign->getKey())
        ->assertSet('saleCurrencyRate', 12.5)
        ->fillForm(['partner_id' => $customer->getKey()])
        ->call('create')
        ->assertHasNoFormErrors();

    $invoice = Move::query()->withoutGlobalScopes()->latest('id')->firstOrFail();

    expect((float) $invoice->invoice_currency_rate)->toBe(12.5)
        ->and($invoice->currency_rate_source)->toBe(Move::RATE_MANUAL)
        ->and((int) $invoice->currency_rate_confirmed_by)->toBe((int) auth()->id());
});

/**
 * MC-2 rate bar (2026-11-24): the confirmed rate stays visible under the
 * currency, and says so when a currency change has dropped it.
 */
it('shows the kept rate under the currency, who confirmed it, and when it no longer applies', function () {
    $company = AccountHelper::company()->load('currency');
    $foreign = AccountHelper::otherCurrency();
    $question = app(SaleCurrencyQuestion::class);
    $lang = 'accounts::filament/resources/invoice/pages/create-invoice.sale-currency';

    $label = $question->label(12.5, $company->currency, $foreign);

    expect($label)->toBe('1 '.$company->currency->name.' = 12.5 '.$foreign->name)
        // Following the feed: nothing to say.
        ->and($question->keptRateText($foreign->getKey(), $company))->toBeNull()
        ->and($question->keptRateText($foreign->getKey(), $company, null, [$foreign->getKey(), 12.5]))->toBe(__($lang.'.kept', ['rate' => $label]))
        ->and($question->keptRateText($company->currency_id, $company, null, [$foreign->getKey(), 12.5]))->toBe(__($lang.'.dropped'));

    AccountHelper::setCurrencyRate($foreign, 10.0, now()->subDay()->toDateString());
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner, null, ['currency_id' => $foreign->getKey()]);
    $invoice->forceFill([
        'invoice_currency_rate'      => 12.5,
        'currency_rate_source'       => Move::RATE_MANUAL,
        'currency_rate_confirmed_by' => auth()->id(),
    ])->save();

    expect($question->keptRateText($foreign->getKey(), $company, $invoice->refresh()))
        ->toBe(__($lang.'.kept-by', ['rate' => $label, 'name' => auth()->user()->name]));
});

it('offers "Currency and rate" on a new invoice once the question is on', function () {
    $company = Company::query()->findOrFail(current_company_id());
    $foreign = AccountHelper::otherCurrency();
    allowSaleCurrencies($company, $foreign);
    AccountHelper::setCurrencyRate($foreign, 10.0, now()->toDateString());

    Livewire::test(CreateInvoice::class)
        ->assertActionVisible('askSaleCurrency')
        ->callAction('askSaleCurrency', data: ['currency_id' => $foreign->getKey(), 'rate' => 12.5])
        ->assertSee(__('accounts::filament/resources/invoice/pages/create-invoice.sale-currency.kept', [
            'rate' => app(SaleCurrencyQuestion::class)->label(12.5, $company->currency, $foreign),
        ]));
});

it('leaves the feed rate unpinned and asks nothing when the switch is off', function () {
    $company = Company::query()->findOrFail(current_company_id());
    $foreign = AccountHelper::otherCurrency();
    allowSaleCurrencies($company, $foreign);
    AccountHelper::setCurrencyRate($foreign, 10.0, now()->toDateString());

    Livewire::test(CreateInvoice::class)
        ->callAction('askSaleCurrency', data: ['currency_id' => $foreign->getKey(), 'rate' => 10.0])
        ->assertSet('saleCurrencyRate', null)
        ->assertSet('data.currency_id', $foreign->getKey());

    CompanySetting::withoutGlobalScopes()->where('company_id', $company->getKey())->update(['ask_sale_currency' => false]);

    expect(Livewire::test(CreateInvoice::class)->get('defaultAction'))->toBeNull();
});
