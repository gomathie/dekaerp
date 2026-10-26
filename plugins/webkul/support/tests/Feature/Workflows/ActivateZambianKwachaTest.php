<?php

use Illuminate\Support\Facades\File;
use Webkul\Support\Models\Country;
use Webkul\Support\Models\Currency;

require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';

/**
 * The Zambian kwacha has always been seeded, but inactive - and `active` is what
 * every currency picker in the application filters on, so ZMW could not be
 * selected for a company or a document. The seed data now ships it active, and
 * a data migration activates it on installations seeded before that change.
 */
it('activates a kwacha row left inactive by an earlier installation', function () {
    $kwacha = Currency::findByCode('ZMW');

    expect($kwacha)->not->toBeNull()
        ->and($kwacha->iso_numeric)->toBe(967);

    // Simulate an installation seeded while the currency still shipped inactive.
    $kwacha->forceFill(['active' => false])->save();

    expect(Currency::active()->where('name', 'ZMW')->exists())->toBeFalse();

    $migration = require base_path('plugins/webkul/support/database/migrations/2026_10_26_000001_activate_zambian_kwacha_currency.php');

    $migration->up();

    expect($kwacha->fresh()->active)->toBeTrue()
        ->and(Currency::active()->where('name', 'ZMW')->exists())->toBeTrue();

    $migration->up();

    expect(Currency::active()->where('name', 'ZMW')->count())->toBe(1);
});

it('ships the kwacha active in the seed data and keeps Zambia pointed at it', function () {
    $currencies = json_decode(File::get(base_path('plugins/webkul/security/src/Data/currencies.json')), true);
    $countries = json_decode(File::get(base_path('plugins/webkul/security/src/Data/countries.json')), true);

    $position = null;
    $kwacha = null;

    foreach ($currencies as $index => $currency) {
        if ($currency['name'] === 'ZMW') {
            $position = $index + 1;
            $kwacha = $currency;
        }
    }

    // Zambia's currency_id is a hardcoded position into the currency seed order,
    // so a reorder of that file silently repoints the country.
    $zambia = collect($countries)->firstWhere('code', 'ZM');

    expect($kwacha)->not->toBeNull()
        ->and($kwacha['active'])->toBeTrue()
        ->and($kwacha['iso_numeric'])->toBe('967')
        ->and($zambia)->not->toBeNull()
        ->and((int) $zambia['currency_id'])->toBe($position)
        ->and(Country::query()->where('code', 'ZM')->value('currency_id'))->toBe(Currency::findByCode('ZMW')->getKey());
});
