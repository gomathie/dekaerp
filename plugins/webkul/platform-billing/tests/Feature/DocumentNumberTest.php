<?php

use Webkul\PlatformBilling\Enums\PlatformDocumentType;
use Webkul\PlatformBilling\Services\DocumentNumberGenerator;

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

/**
 * SAAS-08: platform document numbering (D-SAAS-05).
 *
 * `DEKA-INV-{compact year}-{six digits}`, one sequence per document type per
 * year, never reused.
 */
beforeEach(function (): void {
    TestBootstrapHelper::ensureERPInstalled();
});

it('formats the compact year the way the decision says', function () {
    $generator = app(DocumentNumberGenerator::class);

    expect($generator->compactYear(2026))->toBe('226')
        ->and($generator->compactYear(2027))->toBe('227')
        // Two different years must never share a compact form.
        ->and($generator->compactYear(2030))->not->toBe($generator->compactYear(2003));
});

it('issues the first invoice number in the documented shape', function () {
    $number = app(DocumentNumberGenerator::class)->next(PlatformDocumentType::Invoice, 2026);

    expect($number)->toBe('DEKA-INV-226-000001');
});

it('counts up inside a year and starts again in the next one', function () {
    $generator = app(DocumentNumberGenerator::class);

    expect($generator->next(PlatformDocumentType::Invoice, 2026))->toBe('DEKA-INV-226-000001')
        ->and($generator->next(PlatformDocumentType::Invoice, 2026))->toBe('DEKA-INV-226-000002')
        ->and($generator->next(PlatformDocumentType::Invoice, 2027))->toBe('DEKA-INV-227-000001');
});

it('keeps credit notes on their own sequence', function () {
    $generator = app(DocumentNumberGenerator::class);

    expect($generator->next(PlatformDocumentType::Invoice, 2026))->toBe('DEKA-INV-226-000001')
        ->and($generator->next(PlatformDocumentType::CreditNote, 2026))->toBe('DEKA-CN-226-000001')
        ->and($generator->next(PlatformDocumentType::CreditNote, 2026))->toBe('DEKA-CN-226-000002');
});

it('never issues the same number twice in a row of calls', function () {
    $generator = app(DocumentNumberGenerator::class);

    $numbers = collect(range(1, 10))
        ->map(fn (): string => $generator->next(PlatformDocumentType::Invoice, 2026));

    expect($numbers->unique())->toHaveCount(10)
        ->and($numbers->last())->toBe('DEKA-INV-226-000010');
});
