<?php

use Webkul\PlatformBilling\Services\SubscriptionPeriod;

it('derives month end and leap year boundaries from the original anchor', function () {
    $calculator = new SubscriptionPeriod;
    $anchor = new DateTimeImmutable('2024-01-31 12:00:00', new DateTimeZone('UTC'));

    expect($calculator->boundary($anchor, 1, 1)->format('Y-m-d'))->toBe('2024-02-29')
        ->and($calculator->boundary($anchor, 1, 2)->format('Y-m-d'))->toBe('2024-03-31')
        ->and($calculator->boundary($anchor, 1, 13)->format('Y-m-d'))->toBe('2025-02-28');
});

it('supports each approved interval without period drift', function (int $months) {
    $calculator = new SubscriptionPeriod;
    $anchor = new DateTimeImmutable('2025-01-30 12:00:00', new DateTimeZone('UTC'));
    $first = $calculator->period($anchor, $months, 0);
    $second = $calculator->period($anchor, $months, 1);

    expect($first['end'])->toEqual($second['start'])
        ->and($second['end']->format('j'))->toBe('30');
})->with([1, 3, 6, 12]);

it('keeps local wall time across daylight saving changes', function () {
    $calculator = new SubscriptionPeriod;
    $anchor = new DateTimeImmutable('2026-02-15 12:00:00', new DateTimeZone('America/New_York'));
    $period = $calculator->period($anchor, 1, 0);

    expect($period['start']->format('H:i'))->toBe('17:00')
        ->and($period['end']->format('H:i'))->toBe('16:00');
});
