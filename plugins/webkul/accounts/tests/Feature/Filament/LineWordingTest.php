<?php

/**
 * Plan 10, piece 6 (2026-11-25, wording for everyone): invoice, bill, credit
 * note and refund lines say "Add item", "Item / service" and "Qty". Most
 * clients sell services as well as goods, and "Add Product" read as if only
 * stock could be invoiced.
 */
it('names invoice and bill lines items and services, with a short quantity heading', function (string $resource) {
    $base = "accounts::filament/resources/{$resource}.form.tabs.invoice-lines.repeater.products";

    expect(__("{$base}.add-product"))->toBe('Add item')
        ->and(__("{$base}.fields.product"))->toBe('Item / service')
        ->and(__("{$base}.fields.quantity"))->toBe('Qty');
})->with(['invoice', 'bill', 'credit-note', 'refund']);

it('uses the same line wording in the other languages', function () {
    app()->setLocale('fr');

    expect(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.repeater.products.add-product'))->toBe('Ajouter un article')
        ->and(__('accounts::filament/resources/bill.form.tabs.invoice-lines.repeater.products.fields.product'))->toBe('Article / service');
});
