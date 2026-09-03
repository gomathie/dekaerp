<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\TaxIncludeOverride;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/AccountHelper.php';

/**
 * Covers the branch of the v1.6.0 tax rework that nothing else reaches.
 *
 * TaxComputer::sharesBatch() decides whether two taxes compute against a
 * shared base, and it branches on amount type, price_include and
 * include_base_amount. TaxGroupTest exercises the tax-exclusive path only, so
 * a change in how tax-inclusive taxes batch would not have shown up anywhere -
 * and this is arithmetic that ends up on customer invoices.
 */
beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');

    DB::table('plugins')->updateOrInsert(
        ['name' => 'accounts'],
        ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
    );

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    AccountHelper::actingAsAdmin();

    $this->income = AccountHelper::account('income');
    $this->partner = AccountHelper::partner();
});

it('keeps a tax-inclusive line total equal to the price entered', function () {
    // 100 entered on a tax-inclusive 10% tax means the customer pays 100:
    // the tax is carved out of the price rather than added to it.
    $tax = AccountHelper::taxWithAccounts(
        10,
        include: TaxIncludeOverride::TAX_INCLUDED,
    );

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($invoice, $this->income, qty: 1, priceUnit: 100, taxes: [$tax]);

    AccountHelper::compute($invoice);

    expect((float) $invoice->refresh()->amount_total)->toBe(100.0)
        ->and((float) $invoice->amount_untaxed)->toBeLessThan(100.0)
        ->and((float) $invoice->amount_tax)->toBeGreaterThan(0.0);
});

it('batches two tax-inclusive taxes against one shared base', function () {
    // Both taxes are inclusive, so they share a batch and are both carved out
    // of the same 100. The total must still be exactly what was entered - if
    // batching regressed and one compounded onto the other, it would not be.
    $first = AccountHelper::taxWithAccounts(10, include: TaxIncludeOverride::TAX_INCLUDED);
    $second = AccountHelper::taxWithAccounts(5, include: TaxIncludeOverride::TAX_INCLUDED);

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($invoice, $this->income, qty: 1, priceUnit: 100, taxes: [$first, $second]);

    AccountHelper::compute($invoice);

    expect((float) $invoice->refresh()->amount_total)->toBe(100.0)
        ->and((float) $invoice->amount_untaxed + (float) $invoice->amount_tax)
        ->toBe((float) $invoice->amount_total);
});

it('does not mix inclusive and exclusive taxes into one batch', function () {
    // sharesBatch() refuses to batch taxes whose price_include differs, so the
    // exclusive tax must add on top while the inclusive one is carved out.
    // The total therefore exceeds the entered price, but only by the exclusive
    // tax.
    $inclusive = AccountHelper::taxWithAccounts(10, include: TaxIncludeOverride::TAX_INCLUDED);
    $exclusive = AccountHelper::taxWithAccounts(5, include: TaxIncludeOverride::TAX_EXCLUDED);

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($invoice, $this->income, qty: 1, priceUnit: 100, taxes: [$inclusive, $exclusive]);

    AccountHelper::compute($invoice);

    $invoice->refresh();

    expect((float) $invoice->amount_total)->toBeGreaterThan(100.0)
        ->and((float) $invoice->amount_untaxed + (float) $invoice->amount_tax)
        ->toBe((float) $invoice->amount_total);
});
