<?php

use Webkul\Account\Enums\MoveType;
use Webkul\Account\Models\Move;
use Webkul\Sale\Listeners\SendSMSNotificationListener;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Services\Msg91Service;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

/**
 * Dashboard plan DB-7 (2026-11-23): the payment SMS added each invoice's
 * own-currency amount and labelled the sum with the order's currency, and
 * counted a credit note as more invoiced.
 */
function smsTotals(array $moves, Order $order): array
{
    $listener = new SendSMSNotificationListener(Mockery::mock(Msg91Service::class));
    $method = new ReflectionMethod($listener, 'totals');

    return $method->invoke($listener, collect($moves), $order);
}

function smsMove(array $attributes, string $currency): Move
{
    return (new Move)->forceFill($attributes)->setRelation('currency', (new Currency)->forceFill(['name' => $currency]));
}

it('deducts a credit note and keeps the documents\' own currency when they share one', function () {
    $order = (new Order)->setRelation('currency', (new Currency)->forceFill(['name' => 'GHS']));

    [$invoiced, $remaining, $currency] = smsTotals([
        smsMove(['currency_id' => 2, 'move_type' => MoveType::OUT_INVOICE, 'amount_total' => 1000, 'amount_residual' => 600], 'USD'),
        smsMove(['currency_id' => 2, 'move_type' => MoveType::OUT_REFUND, 'amount_total' => 100, 'amount_residual' => 100], 'USD'),
    ], $order);

    expect($invoiced)->toBe(900.0)
        ->and($remaining)->toBe(500.0)
        // The invoices' currency, not the order's.
        ->and($currency)->toBe('USD');
});

it('adds documents in different currencies in the company currency, each at its own rate', function () {
    $company = (new Company)->setRelation('currency', (new Currency)->forceFill(['name' => 'GHS']));
    $order = (new Order)->setRelation('company', $company);

    [$invoiced, $remaining, $currency] = smsTotals([
        // 100 USD at 15.5 = 1,550 GHS, unpaid; 50 GHS, paid.
        smsMove(['currency_id' => 2, 'move_type' => MoveType::OUT_INVOICE, 'amount_total' => 100, 'amount_residual' => 100, 'amount_total_signed' => 1550, 'amount_residual_signed' => 1550], 'USD'),
        smsMove(['currency_id' => 3, 'move_type' => MoveType::OUT_INVOICE, 'amount_total' => 50, 'amount_residual' => 0, 'amount_total_signed' => 50, 'amount_residual_signed' => 0], 'GHS'),
    ], $order);

    expect($invoiced)->toBe(1600.0)
        ->and($remaining)->toBe(1550.0)
        ->and($currency)->toBe('GHS');
});
