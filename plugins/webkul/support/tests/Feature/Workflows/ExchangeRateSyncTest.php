<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Http;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\CurrencyRate;
use Webkul\Support\Services\ExchangeRateSync;

require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';

/**
 * Daily exchange rates from a public source (user, 2026-11-22). Rates are
 * per company, in units per one unit of the company's own currency, and a
 * rate typed by hand is never overwritten.
 */
beforeEach(function (): void {
    TestBootstrapHelper::ensureERPInstalled();

    config(['exchange-rates.enabled' => true, 'exchange-rates.provider' => 'open_er_api']);

    foreach (['USD', 'GHS', 'EUR'] as $code) {
        Currency::query()->updateOrCreate(['name' => $code], ['active' => true]);
    }
});

function exchangeRatesFake(array $rates = ['USD' => 1, 'GHS' => 15.5, 'EUR' => 0.92], string $result = 'success'): void
{
    Http::fake(['*' => Http::response(['result' => $result, 'base_code' => 'USD', 'rates' => $rates])]);
}

function exchangeRateCompany(string $code): Company
{
    return Company::factory()->create(['currency_id' => Currency::query()->where('name', $code)->value('id')]);
}

function exchangeRateFor(Company $company, string $code): ?CurrencyRate
{
    return CurrencyRate::query()
        ->where('company_id', $company->getKey())
        ->where('currency_id', Currency::query()->where('name', $code)->value('id'))
        ->whereDate('name', now()->toDateString())
        ->first();
}

it('stores rates per company in units per one unit of that company\'s currency', function () {
    exchangeRatesFake();
    $ghana = exchangeRateCompany('GHS');

    $this->artisan('currencies:sync-rates', ['--company' => [$ghana->getKey()]])->assertSuccessful();

    // 1 GHS = 1/15.5 USD; 1 GHS = 0.92/15.5 EUR.
    expect((float) exchangeRateFor($ghana, 'USD')->rate)->toBe(round(1 / 15.5, 6))
        ->and((float) exchangeRateFor($ghana, 'EUR')->rate)->toBe(round(0.92 / 15.5, 6))
        ->and(exchangeRateFor($ghana, 'GHS'))->toBeNull()
        ->and(exchangeRateFor($ghana, 'USD')->creator_id)->toBeNull();

    $usd = Currency::query()->where('name', 'USD')->first();
    $ghs = Currency::query()->where('name', 'GHS')->first();

    expect($ghs->convert(155, $usd, $ghana))->toBe(10.0);
});

it('never overwrites a rate typed by hand, and updates its own on a second run', function () {
    exchangeRatesFake();
    $ghana = exchangeRateCompany('GHS');
    $usd = Currency::query()->where('name', 'USD')->first();
    $user = User::factory()->create();

    CurrencyRate::query()->create([
        'name'        => now()->toDateString(),
        'rate'        => 0.07,
        'currency_id' => $usd->getKey(),
        'company_id'  => $ghana->getKey(),
        'creator_id'  => $user->getKey(),
    ]);

    app(ExchangeRateSync::class)->syncCompany($ghana, ['USD' => 1, 'GHS' => 15.5, 'EUR' => 0.92]);
    app(ExchangeRateSync::class)->syncCompany($ghana, ['USD' => 1, 'GHS' => 16.0, 'EUR' => 0.92]);

    expect(CurrencyRate::query()->where('company_id', $ghana->getKey())->where('currency_id', $usd->getKey())->count())->toBe(1)
        ->and((float) exchangeRateFor($ghana, 'USD')->rate)->toBe(0.07)
        ->and(CurrencyRate::query()->where('company_id', $ghana->getKey())->whereNull('creator_id')->count())->toBe(1)
        ->and((float) exchangeRateFor($ghana, 'EUR')->rate)->toBe(round(0.92 / 16.0, 6));
});

it('writes nothing and fails when the source answers with an error', function () {
    exchangeRatesFake([], 'error');
    $ghana = exchangeRateCompany('GHS');

    $this->artisan('currencies:sync-rates', ['--company' => [$ghana->getKey()]])->assertFailed();

    expect(CurrencyRate::query()->where('company_id', $ghana->getKey())->exists())->toBeFalse();
});

it('skips a company whose own currency the source does not quote', function () {
    $ghana = exchangeRateCompany('GHS');

    expect(app(ExchangeRateSync::class)->syncCompany($ghana, ['USD' => 1, 'EUR' => 0.92]))->toBe(0);
});

it('is scheduled daily', function () {
    $events = collect(app(Schedule::class)->events())
        ->filter(fn ($event): bool => str_contains((string) $event->command, 'currencies:sync-rates'));

    expect($events)->toHaveCount(1)
        ->and($events->first()->expression)->toBe('30 1 * * *');
});
