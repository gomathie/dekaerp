<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Webkul\Account\Enums\MoveType;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Support\Models\Sequence;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/AccountHelper.php';

/**
 * Covers the v1.6.0 sequence port.
 *
 * Numbering used to be "{prefix}/{database id}", which left gaps whenever a
 * row was deleted and could not be reset or configured. It is now drawn from
 * the sequences table. Nothing else in the suite asserts document numbering,
 * so a regression here would otherwise ship silently - and an invoice number
 * is not a cosmetic detail.
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

it('numbers a posted invoice from a sequence rather than its database id', function () {
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($invoice, $this->income, qty: 1, priceUnit: 100);

    AccountHelper::post($invoice);

    $name = $invoice->refresh()->name;

    expect($name)->not->toBeEmpty()
        ->and($name)->not->toBe('/')
        // The id is what the old scheme used; a number drawn from the sequence
        // must not simply be it.
        ->and($name)->not->toEndWith('/'.$invoice->id);
});

it('creates a sequence scoped to the journal and its company', function () {
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($invoice, $this->income, qty: 1, priceUnit: 100);

    AccountHelper::post($invoice);

    $journal = $invoice->refresh()->journal;

    $sequence = Sequence::withoutGlobalScopes()
        ->where('scope_type', $journal->getMorphClass())
        ->where('scope_id', $journal->id)
        ->first();

    expect($sequence)->not->toBeNull()
        ->and($sequence->company_id)->toBe($journal->company_id);
});

it('advances the counter so two invoices never share a number', function () {
    $first = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($first, $this->income, qty: 1, priceUnit: 100);
    AccountHelper::post($first);

    $second = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($second, $this->income, qty: 1, priceUnit: 100);
    AccountHelper::post($second);

    expect($first->refresh()->name)->not->toBe($second->refresh()->name);
});

it('continues from documents that already exist rather than restarting at one', function () {
    // The production case: invoices numbered under the old scheme are already
    // in the table when the sequence is first created. initialFromNames()
    // reads the highest trailing number and starts above it, so a fresh
    // sequence must not hand back a number an existing document already uses.
    $existing = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($existing, $this->income, qty: 1, priceUnit: 100);
    AccountHelper::post($existing);

    $existingName = $existing->refresh()->name;

    // Drop the sequence so the next post has to build one from scratch,
    // exactly as it will on the first post after this port is deployed.
    Sequence::withoutGlobalScopes()->delete();

    $next = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($next, $this->income, qty: 1, priceUnit: 100);
    AccountHelper::post($next);

    expect($next->refresh()->name)->not->toBe($existingName);
});
