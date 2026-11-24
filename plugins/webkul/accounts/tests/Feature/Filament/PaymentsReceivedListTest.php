<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Filament\Resources\PaymentResource\Pages\ListPayments;
use Webkul\Account\Filament\Resources\PaymentResource\Tables\PaymentsTable;
use Webkul\Account\Models\Payment;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../Helpers/AccountHelper.php';

/**
 * TRACE365, 2026-11-23 (plan #10 R-7): they compute their taxes from the
 * payments received in a month or a year, and the Payments list showed
 * neither the invoice a payment settled nor a way to pick a month.
 */
beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');

    DB::table('plugins')->updateOrInsert(
        ['name' => 'accounts'],
        ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
    );

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');
});

/** A posted invoice of $amount, paid in full on $date. */
function paidInvoiceOn(float $amount, string $date): Payment
{
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE);
    AccountHelper::productLine($invoice, AccountHelper::account('income'), qty: 1, priceUnit: $amount);
    AccountHelper::compute($invoice);
    AccountHelper::post($invoice);
    AccountHelper::pay($invoice);

    $payment = Payment::query()->whereHas('invoices', fn ($query) => $query->whereKey($invoice->getKey()))->firstOrFail();
    $payment->forceFill(['date' => $date])->saveQuietly();

    return $payment->refresh();
}

it('turns a year and a month into the first and last day of that period', function () {
    [$monthStart, $monthEnd] = PaymentsTable::periodRange(2026, 2);
    [$yearStart, $yearEnd] = PaymentsTable::periodRange('2026', null);

    expect($monthStart->toDateString())->toBe('2026-02-01')
        ->and($monthEnd->toDateString())->toBe('2026-02-28')
        ->and($yearStart->toDateString())->toBe('2026-01-01')
        ->and($yearEnd->toDateString())->toBe('2026-12-31')
        ->and(PaymentsTable::periodRange(null, null))->toBe([null, null]);
});

it('lists the payments received in the chosen month, with the invoice each one settled', function () {
    FilamentHelper::actingAs(['view_any_account_payment']);

    $march = paidInvoiceOn(500, '2026-03-10');
    $april = paidInvoiceOn(700, '2026-04-02');

    $invoiceName = $march->invoices()->first()->name;

    Livewire::test(ListPayments::class)
        ->set('activeTableView', 'customer_payments')
        ->filterTable('period', ['year' => 2026, 'month' => 3])
        ->assertCanSeeTableRecords([$march])
        ->assertCanNotSeeTableRecords([$april])
        ->assertSee($invoiceName);

    Livewire::test(ListPayments::class)
        ->set('activeTableView', 'customer_payments')
        ->filterTable('period', ['year' => 2026, 'month' => null])
        ->assertCanSeeTableRecords([$march, $april]);
});

/**
 * TRACE365, 2026-11-24 (Zoho's "Unused amount"): what a payment has not yet
 * settled is the open part of its customer line; a payment that settled its
 * invoice exactly has nothing left.
 */
it('shows what a payment has not yet settled', function () {
    FilamentHelper::actingAs(['view_any_account_payment']);

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE);
    AccountHelper::productLine($invoice, AccountHelper::account('income'), qty: 1, priceUnit: 500);
    AccountHelper::compute($invoice);
    AccountHelper::post($invoice);
    AccountHelper::pay($invoice, 650.0);

    $over = Payment::query()->whereHas('invoices', fn ($query) => $query->whereKey($invoice->getKey()))->firstOrFail();
    $exact = paidInvoiceOn(300, now()->toDateString());

    $rows = Payment::query()->withUnusedAmount()->whereKey([$over->getKey(), $exact->getKey()])->get()->keyBy('id');

    expect($rows[$over->getKey()]->unusedAmount())->toBe(150.0)
        ->and($rows[$exact->getKey()]->unusedAmount())->toBe(0.0)
        ->and($rows[$over->getKey()]->relationLoaded('entryLines'))->toBeTrue();

    Livewire::test(ListPayments::class)
        ->set('activeTableView', 'customer_payments')
        ->assertCanRenderTableColumn('unused_amount')
        ->assertCanSeeTableRecords([$over, $exact]);
});
