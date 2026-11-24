<?php

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Account\Enums\MoveType;
use Webkul\Accounting\Filament\Clusters\Reporting\Pages\Exports\MultiCurrencyExport;
use Webkul\Accounting\Filament\Clusters\Reporting\Pages\MultiCurrency;
use Webkul\Accounting\Support\MultiCurrencyReport;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../accounts/tests/Helpers/AccountHelper.php';

/**
 * Multi-currency report (plan #11 MC-3, TRACE365, user 2026-11-24): known
 * totals for two currencies, the open amount at today's rate, and a
 * currency with no rate today left out of that total.
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

function multiCurrencyInvoice(?Currency $currency, float $price, string $date): void
{
    $move = AccountHelper::invoice(MoveType::OUT_INVOICE, test()->partner, null, array_filter([
        'currency_id'  => $currency?->getKey(),
        'invoice_date' => $date,
        'date'         => $date,
    ]));

    AccountHelper::productLine($move, test()->income, qty: 1, priceUnit: $price);

    AccountHelper::post($move);
}

it('generates the page permission the multi-currency report checks', function () {
    FilamentHelper::bootAdminPanel();

    $generated = collect(FilamentShield::getPages())
        ->flatMap(fn (array $page): array => array_keys($page['permissions']))
        ->all();

    expect($generated)->toContain('page_accounting_multi_currency');
});

it('renders the multi-currency report page', function () {
    Livewire::test(MultiCurrency::class)->assertOk();
});

it('totals each currency at the booked rates and values what is open at today\'s rate', function () {
    $company = AccountHelper::company()->load('currency');
    $foreign = Currency::factory()->create(['active' => true]);

    // Booked at 11.8 per company unit; today's rate is 10.
    AccountHelper::setCurrencyRate($foreign, 11.8, now()->subDays(5)->toDateString());
    AccountHelper::setCurrencyRate($foreign, 10.0, now()->toDateString());

    multiCurrencyInvoice(null, 1000, now()->subDays(3)->toDateString());
    multiCurrencyInvoice($foreign, 118, now()->subDays(3)->toDateString());

    $data = app(MultiCurrencyReport::class)->build(now()->subDays(10)->startOfDay(), now()->endOfDay(), MultiCurrencyReport::SALES, null, $company->getKey());

    $byCode = collect($data['currencies'])->keyBy('code');
    $base = $byCode[$company->currency->name];
    $usd = $byCode[$foreign->name];

    expect($data['currencies'][0]['is_base'])->toBeTrue()
        ->and($base['invoiced'])->toBe(1000.0)
        ->and($base['invoiced_base'])->toBe(1000.0)
        ->and($usd['invoiced'])->toBe(118.0)
        ->and($usd['invoiced_base'])->toBe(10.0)
        ->and($usd['open'])->toBe(118.0)
        ->and($usd['open_base'])->toBe(10.0)
        // 118 at today's 10 is 11.80: worth 1.80 more than booked.
        ->and($usd['open_base_today'])->toBe(11.8)
        ->and($usd['difference'])->toBe(1.8)
        ->and($usd['average_rate'])->toBe('1 '.$company->currency->name.' = 11.8 '.$foreign->name)
        ->and($data['totals']['invoiced_base'])->toBe(1010.0)
        ->and($data['totals']['difference'])->toBe(1.8)
        ->and($data['documents'])->toHaveCount(2);

    // The currency filter narrows every figure to that currency.
    $only = app(MultiCurrencyReport::class)->build(now()->subDays(10)->startOfDay(), now()->endOfDay(), MultiCurrencyReport::SALES, $foreign->getKey(), $company->getKey());

    expect(collect($only['currencies'])->pluck('code')->all())->toBe([$foreign->name])
        ->and($only['documents'])->toHaveCount(1);

    // Another company sees none of it.
    $stranger = Company::factory()->create();

    expect(app(MultiCurrencyReport::class)->build(now()->subDays(10)->startOfDay(), now()->endOfDay(), MultiCurrencyReport::SALES, null, $stranger->getKey())['currencies'])->toBe([]);

    // The export carries the same rows as the page.
    $sheet = (new MultiCurrencyExport($data))->array();

    expect($sheet[0][0])->toBe($company->currency->name)
        ->and($sheet[1][0])->toBe($foreign->name)
        ->and($sheet[1][6])->toBe(11.8);
});

it('shows a dash and leaves a currency out of today\'s total when it has no rate', function () {
    $company = AccountHelper::company()->load('currency');
    $none = Currency::factory()->create(['active' => true]);

    multiCurrencyInvoice($none, 50, now()->subDays(3)->toDateString());

    $data = app(MultiCurrencyReport::class)->build(now()->subDays(10)->startOfDay(), now()->endOfDay(), MultiCurrencyReport::SALES, $none->getKey(), $company->getKey());

    // A missing rate is never 1 (DB-7): no value today, nothing added.
    expect($data['currencies'][0]['open_base_today'])->toBeNull()
        ->and($data['currencies'][0]['difference'])->toBeNull()
        ->and($data['totals']['open_base_today'])->toBe(0.0)
        ->and($data['totals']['difference'])->toBe(0.0);
});
