<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Enums\QuotationOutcome;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Services\QuotationOutcomeMarker;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../Helpers/SaleHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('sales');
    FilamentHelper::actingAs(['update_sale_order']);
});

it('marks an accepted quotation won without changing the sale that reports count', function (): void {
    $order = SaleHelper::order(['state' => OrderState::SALE, 'amount_total' => 40]);

    app(QuotationOutcomeMarker::class)->markWon($order);
    $order->refresh();

    expect($order->state)->toBe(OrderState::SALE)
        ->and($order->outcome)->toBe(QuotationOutcome::Won)
        ->and((float) $order->amount_total)->toBe(40.0)
        ->and(Order::query()->where('state', OrderState::SALE->value)->whereKey($order->id)->exists())->toBeTrue();
});

it('marks a declined or expired quotation lost and refuses an open one', function (): void {
    $open = SaleHelper::order(['state' => OrderState::DRAFT, 'validity_date' => now()->addDay()]);

    expect(fn () => app(QuotationOutcomeMarker::class)->markLost($open))
        ->toThrow(ValidationException::class);

    $declined = SaleHelper::order(['state' => OrderState::CANCEL]);
    app(QuotationOutcomeMarker::class)->markLost($declined);

    expect($declined->refresh()->state)->toBe(OrderState::CANCEL)
        ->and($declined->outcome)->toBe(QuotationOutcome::Lost);

    $expired = SaleHelper::order([
        'state'         => OrderState::SENT,
        'validity_date' => now()->subDay(),
    ]);
    app(QuotationOutcomeMarker::class)->markLost($expired);

    expect($expired->refresh()->state)->toBe(OrderState::SENT)
        ->and($expired->outcome)->toBe(QuotationOutcome::Lost);
});

it('refuses the mark when this user cannot update quotations in that company', function (): void {
    $order = SaleHelper::order(['state' => OrderState::SALE]);

    FilamentHelper::actingAs([]);

    expect(fn () => app(QuotationOutcomeMarker::class)->markWon($order))
        ->toThrow(AuthorizationException::class);

    expect($order->refresh()->outcome)->toBeNull();
});
