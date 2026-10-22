<?php

use Webkul\Support\Models\Country;
use Webkul\Support\Models\Currency;

it('prefers the selected country currency over the configured fallback', function () {
    $configuredCurrency = Currency::factory()->create(['name' => 'XCF']);
    $countryCurrency = Currency::factory()->create(['name' => 'XCC']);
    $country = Country::factory()->create(['currency_id' => $countryCurrency->id]);

    config(['app.currency' => $configuredCurrency->name]);

    $resolved = Currency::resolveDefault($country);

    expect($resolved)->not->toBeNull()
        ->and($resolved->is($countryCurrency))->toBeTrue();
});

it('uses the configured currency when the country has none', function () {
    $configuredCurrency = Currency::factory()->create(['name' => 'XFB']);
    $country = Country::factory()->create(['currency_id' => null]);

    config(['app.currency' => $configuredCurrency->name]);

    $resolved = Currency::resolveDefault($country);

    expect($resolved)->not->toBeNull()
        ->and($resolved->is($configuredCurrency))->toBeTrue();
});

it('finds currencies by code and rejects blank codes', function () {
    $currency = Currency::factory()->create(['name' => 'XBC']);

    expect(Currency::findByCode($currency->name)?->is($currency))->toBeTrue()
        ->and(Currency::findByCode(null))->toBeNull()
        ->and(Currency::findByCode(''))->toBeNull();
});
