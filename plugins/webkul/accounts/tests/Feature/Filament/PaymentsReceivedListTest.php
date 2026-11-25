<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Filament\Resources\PaymentResource\Pages\ListPayments;
use Webkul\Account\Filament\Resources\PaymentResource\Tables\PaymentsTable;
use Webkul\Account\Models\Payment;
use Webkul\Partner\Models\Partner;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../Helpers/AccountHelper.php';

/**
 * TRACE365, 2026-11-23 (plan #9 R-7): they compute their taxes from the
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
 * Plan #9 R-9 (2026-11-25): two-line cells, as on the invoice list. The
 * payment date under the number, and the company a contact works for under
 * their name; the separate date column is hidden by default.
 */
it('shows the payment date under the number and the contact\'s company under the contact', function () {
    FilamentHelper::actingAs(['view_any_account_payment']);

    $payment = paidInvoiceOn(500, '2026-03-10');

    $acme = Partner::factory()->create(['company_id' => current_company_id(), 'name' => 'Acme Holdings Ltd']);
    $payment->partner->forceFill(['parent_id' => $acme->getKey()])->save();

    Livewire::test(ListPayments::class)
        ->set('activeTableView', 'customer_payments')
        ->assertCanSeeTableRecords([$payment])
        ->assertSee(Carbon::parse('2026-03-10')->translatedFormat('d M Y'))
        ->assertSee('Acme Holdings Ltd')
        ->assertTableColumnExists('date', fn ($column): bool => $column->isToggledHiddenByDefault());
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

/**
 * Plan #9 R-7: the rest of Zoho's cases. A payment no longer matched to any
 * invoice is unused in full; a draft has no journal entry and shows "-",
 * not 0.
 */
it('shows a payment matched to nothing as wholly unused, and a draft as having no figure', function () {
    FilamentHelper::actingAs(['view_any_account_payment']);

    $payment = paidInvoiceOn(400, now()->toDateString());
    $invoice = $payment->invoices()->firstOrFail();

    expect(Payment::query()->withUnusedAmount()->findOrFail($payment->getKey())->unusedAmount())->toBe(0.0);

    AccountHelper::unreconcile($invoice);

    expect(Payment::query()->withUnusedAmount()->findOrFail($payment->getKey())->unusedAmount())->toBe(400.0)
        ->and((new Payment)->unusedAmount())->toBeNull();
});

/**
 * Plan #9 R-7: the unused amount is in the payment's own currency, like the
 * Amount column beside it, not converted to the company's.
 */
it('shows the unused amount of a foreign-currency payment in that currency', function () {
    AccountHelper::actingAsAdmin();

    $foreign = AccountHelper::otherCurrency();
    $foreign->rates()->delete();
    AccountHelper::setCurrencyRate($foreign, 2.0);

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, null, null, ['currency_id' => $foreign->id]);
    AccountHelper::productLine($invoice, AccountHelper::account('income'), qty: 2, priceUnit: 100);
    AccountHelper::compute($invoice);
    AccountHelper::post($invoice);
    AccountHelper::pay($invoice, 260.0);

    $payment = Payment::query()->withUnusedAmount()
        ->whereHas('invoices', fn ($query) => $query->whereKey($invoice->getKey()))
        ->firstOrFail();

    expect($payment->currency_id)->toBe($foreign->id)
        ->and($payment->unusedAmount())->toBe(60.0);
});
