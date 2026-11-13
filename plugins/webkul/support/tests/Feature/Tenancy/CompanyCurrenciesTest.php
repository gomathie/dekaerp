<?php

use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensureERPInstalled();
});

/**
 * Company currencies (CR-6, 2026-11-13). Pinned: no list changes nothing (no
 * backfill, so existing companies keep invoicing in any active currency), and
 * a list never hides the company's own base currency.
 */
function companyCurrencyFixture(): array
{
    $base = Currency::factory()->create(['name' => 'ZZA', 'active' => true]);
    $usd = Currency::factory()->create(['name' => 'ZZB', 'active' => true]);
    $other = Currency::factory()->create(['name' => 'ZZC', 'active' => true]);

    $company = Company::factory()->create(['currency_id' => $base->getKey()]);

    return [$company, $base, $usd, $other];
}

it('allows every active currency while the company has no list', function () {
    [$company, $base, $usd, $other] = companyCurrencyFixture();

    $ids = Currency::query()->active()->permittedFor((int) $company->getKey())->pluck('id')->all();

    expect($ids)->toContain($base->getKey(), $usd->getKey(), $other->getKey());
});

it('narrows to the list and always keeps the base currency', function () {
    [$company, $base, $usd, $other] = companyCurrencyFixture();

    $company->permittedCurrencies()->sync([$usd->getKey()]);

    $ids = Currency::query()->active()->permittedFor((int) $company->getKey())->pluck('id')->all();

    expect($ids)->toContain($base->getKey(), $usd->getKey())
        ->and($ids)->not->toContain($other->getKey());
});

it('does not let one company list narrow another company', function () {
    [$company, , $usd, $other] = companyCurrencyFixture();
    $neighbour = Company::factory()->create();

    $company->permittedCurrencies()->sync([$usd->getKey()]);

    expect(Currency::query()->permittedFor((int) $neighbour->getKey())->pluck('id')->all())
        ->toContain($other->getKey());
});
