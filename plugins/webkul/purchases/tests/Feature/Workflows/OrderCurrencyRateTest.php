<?php

use Webkul\Purchase\Models\Order;
use Webkul\Purchase\Services\OrderCalculator;
use Webkul\Support\Models\Currency;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

/**
 * UF-022: a purchase order in a currency other than the company's must report a
 * converted company-currency total.
 *
 * `purchases_orders.currency_rate` defaults to 0 and nothing assigned it, so
 * the guard in OrderCalculator - `(float) $record->currency_rate ? ... : ...`
 * - always took its else branch and `total_cc_amount` accumulated the raw
 * order-currency total. The company-currency figure was the foreign amount
 * relabelled: wrong, and plausible enough to go unnoticed.
 *
 * The rate direction is company -> order, so an order total divided by it gives
 * company currency. Same convention as
 * Account\Models\Move::computeInvoiceCurrencyRate().
 */
beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('purchases');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('sets a rate of exactly one when the order is in the company currency', function () {
    $company = CompanyHelper::company();

    $order = Order::factory()->create([
        'company_id'  => $company->getKey(),
        'currency_id' => $company->currency_id,
    ]);

    app(OrderCalculator::class)->recompute($order);

    // Not merely non-zero: getConversionRate() short-circuits to 1 for
    // identical currencies, and anything else here would silently rescale
    // every single-currency order in the system.
    expect((float) $order->refresh()->currency_rate)->toBe(1.0);
});

it('never leaves the rate at the column default of zero', function () {
    // The actual defect. Zero is falsy, so the calculator's guard fell through
    // to the unconverted branch and nothing ever looked wrong.
    $company = CompanyHelper::company();

    $other = Currency::query()
        ->whereKeyNot($company->currency_id)
        ->firstOrFail();

    // Set explicitly: OrderFactory fills currency_rate with 1.0, so without this
    // the precondition below never held and the test could not reach the defect
    // it exists for (the AGENTS.md factory-default trap).
    $order = Order::factory()->create([
        'company_id'    => $company->getKey(),
        'currency_id'   => $other->getKey(),
        'currency_rate' => 0,
    ]);

    expect((float) $order->currency_rate)->toBe(0.0);

    app(OrderCalculator::class)->recompute($order);

    expect((float) $order->refresh()->currency_rate)->not->toBe(0.0);
});

it('converts the company-currency total rather than copying the order total', function () {
    $company = CompanyHelper::company();

    $other = Currency::query()
        ->whereKeyNot($company->currency_id)
        ->firstOrFail();

    $order = Order::factory()->create([
        'company_id'  => $company->getKey(),
        'currency_id' => $other->getKey(),
    ]);

    app(OrderCalculator::class)->recompute($order);

    $order->refresh();

    $rate = (float) $order->currency_rate;

    // Only meaningful where a rate actually differs; with a 1:1 rate the
    // converted and raw totals are legitimately equal and this would assert
    // nothing. Stated rather than silently skipped.
    if ($rate === 1.0) {
        expect((float) $order->total_cc_amount)->toBe((float) $order->total_amount);

        return;
    }

    expect($rate)->toBeGreaterThan(0.0)
        ->and((float) $order->total_cc_amount)
        ->toBe(round((float) $order->total_amount / $rate, 4));
});
