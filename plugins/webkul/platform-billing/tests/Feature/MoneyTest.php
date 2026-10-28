<?php

use Webkul\PlatformBilling\Support\Money;

/**
 * SAAS-08: money is integer minor units, with a currency, and cannot drift.
 *
 * The point of these tests is not that arithmetic works - it is that the wrong
 * things are *refused*: a float, a currency mixed with another, an amount that
 * does not fit, and a rate with too much precision.
 */
it('stores an amount as minor units and prints it back exactly', function () {
    $money = Money::fromDecimal('1234.56', 'USD');

    expect($money->minor)->toBe(123456)
        ->and($money->currency)->toBe('USD')
        ->and($money->toDecimal())->toBe('1234.56');
});

it('does not drift the way float arithmetic does', function () {
    $sum = Money::fromDecimal('0.10', 'USD')->add(Money::fromDecimal('0.20', 'USD'));

    expect($sum->minor)->toBe(30)
        ->and($sum->toDecimal())->toBe('0.30');
});

it('refuses more decimal places than the currency scale', function () {
    // A third decimal is a rounding decision someone has to make deliberately.
    expect(fn () => Money::fromDecimal('1.005', 'USD'))
        ->toThrow(InvalidArgumentException::class);
});

it('refuses text that is not a decimal amount', function () {
    expect(fn () => Money::fromDecimal('12,50', 'USD'))->toThrow(InvalidArgumentException::class);
    expect(fn () => Money::fromDecimal('abc', 'USD'))->toThrow(InvalidArgumentException::class);
});

it('refuses to combine two currencies', function () {
    $usd = Money::fromDecimal('10.00', 'USD');
    $zmw = Money::fromDecimal('10.00', 'ZMW');

    expect(fn () => $usd->add($zmw))->toThrow(InvalidArgumentException::class);
    expect(fn () => $usd->subtract($zmw))->toThrow(InvalidArgumentException::class);
});

it('refuses an amount that does not fit in an integer', function () {
    // One more than PHP_INT_MAX in minor units.
    expect(fn () => Money::fromDecimal('92233720368547758.08', 'USD'))
        ->toThrow(OverflowException::class);
});

it('refuses multiplication that would overflow', function () {
    $money = Money::of(PHP_INT_MAX, 'USD');

    expect(fn () => $money->multiply(2))->toThrow(OverflowException::class);
});

it('applies a percentage rate once, rounded half up', function () {
    // 15% of 100.00 is 15.00; 15% of 0.05 exercises the rounding.
    expect(Money::fromDecimal('100.00', 'USD')->percentage('15')->toDecimal())->toBe('15.00')
        ->and(Money::fromDecimal('0.05', 'USD')->percentage('15')->minor)->toBe(1);
});

it('keeps negative amounts, because credit notes exist', function () {
    $credit = Money::fromDecimal('-25.50', 'USD');

    expect($credit->minor)->toBe(-2550)
        ->and($credit->isNegative())->toBeTrue()
        ->and($credit->toDecimal())->toBe('-25.50')
        ->and($credit->negate()->toDecimal())->toBe('25.50');
});

it('normalises the currency code and refuses a malformed one', function () {
    expect(Money::of(100, 'usd')->currency)->toBe('USD');

    expect(fn () => Money::of(100, 'US'))->toThrow(InvalidArgumentException::class);
});
