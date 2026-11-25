<?php

use Illuminate\Support\Facades\App;

/**
 * User decision, 2026-11-25 (TRACE365): dashboard money shows the exact
 * amount below 100,000 and shortens only from 100,000 up. Before, every
 * amount was compact, so $3,750 of bills read "$3.8K" and a $1,000 supplier
 * "$1K", which looked wrong although the figures were right.
 */
beforeEach(function () {
    App::setLocale('en');
});

it('shows the exact amount below 100,000', function () {
    expect(compact_money(3750.0, 'USD'))->toBe(money(3750.0, 'USD'))
        ->and(compact_money(3750.0, 'USD'))->toBe('$3,750.00')
        ->and(compact_money(1312.5, 'USD'))->toBe('$1,312.50')
        ->and(compact_money(1000.0, 'USD'))->toBe('$1,000.00')
        ->and(compact_money(99999.0, 'USD'))->toBe('$99,999.00')
        ->and(compact_money(0.0, 'USD'))->toBe('$0.00');
});

it('shows a negative amount below 100,000 exactly too', function () {
    expect(compact_money(-2750.0, 'USD'))->toBe(money(-2750.0, 'USD'))
        ->and(compact_money(-2750.0, 'USD'))->not->toContain('K');
});

it('shortens amounts of 100,000 or more', function () {
    expect(compact_money(125000.0, 'USD'))->toBe('$125K')
        ->and(compact_money(1200000.0, 'USD'))->toBe('$1.2M')
        ->and(compact_money(-250000.0, 'USD'))->toContain('250K');
})->skip(fn (): bool => ! class_exists(MessageFormatter::class), 'ext-intl is not installed');
