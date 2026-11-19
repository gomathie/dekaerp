<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions\PayAction;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\EditInvoice;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\PaymentRegister;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../Helpers/AccountHelper.php';

/**
 * TRACE365, 2026-11-19: paying an invoice in the company's own base
 * currency (GHS) opened a broken Pay window and "Call to a member function
 * isZero() on null"; the same company's USD invoice paid fine.
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

it('pays an invoice in a company whose base currency is not the installation default', function () {
    FilamentHelper::actingAs(['view_any_account_invoice', 'update_account_invoice']);

    $company = AccountHelper::company();
    $local = AccountHelper::otherCurrency();
    $local->forceFill(['active' => true])->save();
    DB::table('companies')->where('id', $company->id)->update(['currency_id' => $local->id]);
    $company->refresh();

    AccountHelper::bankJournal();

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, overrides: ['currency_id' => $local->id]);
    AccountHelper::productLine($invoice, AccountHelper::account('income'), qty: 1, priceUnit: 300);
    AccountHelper::compute($invoice);
    AccountHelper::post($invoice);

    $component = Livewire::test(EditInvoice::class, ['record' => $invoice->id])
        ->assertOk()
        ->callAction(PayAction::class);

    // Name the failing field when the payment form refuses to submit.
    expect($component->errors()->toArray())->toBe([]);

    expect($invoice->refresh()->payment_state)->toBe(PaymentState::PAID);
});

it('prepares a payment register for an invoice in the company currency without errors', function () {
    FilamentHelper::actingAs(['view_any_account_invoice', 'update_account_invoice']);

    $company = AccountHelper::company();
    $local = AccountHelper::otherCurrency();
    $local->forceFill(['active' => true])->save();
    DB::table('companies')->where('id', $company->id)->update(['currency_id' => $local->id]);
    $company->refresh();

    AccountHelper::bankJournal();

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, overrides: ['currency_id' => $local->id]);
    AccountHelper::productLine($invoice, AccountHelper::account('income'), qty: 1, priceUnit: 300);
    AccountHelper::compute($invoice);
    AccountHelper::post($invoice);
    $invoice->refresh();

    $register = new PaymentRegister;
    $register->lines = $invoice->lines;
    $register->company = $invoice->company;
    $register->currency = $invoice->currency;
    $register->currency_id = $invoice->currency_id;
    $register->payment_type = PaymentType::RECEIVE;
    $register->computeBatches();
    $register->computeAvailableJournalIds();
    $register->journal_id = $register->available_journal_ids[0] ?? null;
    $register->journal = Journal::find($register->journal_id);
    $register->computePaymentMethodLineId();
    $amounts = $register->getTotalAmountsToPay($register->batches);
    $register->amount = $amounts['amount_by_default'];
    $register->computeInstallmentsMode();

    expect((float) $register->amount)->toBe(300.0);
});
