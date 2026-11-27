<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions\ConfirmAction;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions\PayAction;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\EditInvoice;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Models\Partner;
use Webkul\Account\Models\Payment;
use Webkul\Account\Services\QuickPayment;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../Helpers/AccountHelper.php';

/**
 * U97: a customer who already has unused credit is asked, on confirm, and
 * Apply is the answer that settles the invoice from that credit.
 */
beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');

    DB::table('plugins')->updateOrInsert(
        ['name' => 'accounts'],
        ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
    );

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    FilamentHelper::actingAs(['view_any_account_invoice', 'update_account_invoice']);
});

function unusedCreditPayment(Partner $partner, float $amount): Payment
{
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $partner);
    AccountHelper::productLine($invoice, AccountHelper::account('income'), qty: 1, priceUnit: $amount);
    AccountHelper::compute($invoice);
    AccountHelper::post($invoice);
    AccountHelper::pay($invoice);
    AccountHelper::unreconcile($invoice);

    return Payment::query()->withUnusedAmount()
        ->whereHas('invoices', fn ($query) => $query->whereKey($invoice->getKey()))
        ->firstOrFail();
}

function draftInvoiceFor(Partner $partner, float $amount): Move
{
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $partner);
    AccountHelper::productLine($invoice, AccountHelper::account('income'), qty: 1, priceUnit: $amount);
    AccountHelper::compute($invoice);

    return $invoice->refresh();
}

it('asks to apply unused credit when the invoice is confirmed, and Apply settles it', function () {
    $partner = AccountHelper::partner();
    $payment = unusedCreditPayment($partner, 400);
    $invoice = draftInvoiceFor($partner, 150);

    $question = __('accounts::filament/resources/invoice/actions/confirm-action.unused-credit.question', [
        'amount' => money(400, $invoice->currency->name),
    ]);

    expect(QuickPayment::unusedCreditQuestion($invoice))->toBe($question)
        ->and($payment->unusedAmount())->toBe(400.0);

    Livewire::test(EditInvoice::class, ['record' => $invoice->id])
        ->mountAction(ConfirmAction::class)
        ->assertActionMounted(ConfirmAction::class)
        ->assertMountedActionModalSee($question)
        ->assertMountedActionModalSee(__('accounts::filament/resources/invoice/actions/confirm-action.unused-credit.apply'))
        ->callMountedAction();

    expect($invoice->refresh()->state)->toBe(MoveState::POSTED)
        ->and(round(abs((float) $invoice->amount_residual), 2))->toBe(0.0)
        ->and(Payment::query()->withUnusedAmount()->findOrFail($payment->getKey())->unusedAmount())->toBe(250.0);
});

it('leaves the credit unused when confirm is told not to apply it', function () {
    $partner = AccountHelper::partner();
    $payment = unusedCreditPayment($partner, 400);
    $invoice = draftInvoiceFor($partner, 150);

    $question = __('accounts::filament/resources/invoice/actions/confirm-action.unused-credit.question', [
        'amount' => money(400, $invoice->currency->name),
    ]);

    Livewire::test(EditInvoice::class, ['record' => $invoice->id])
        ->mountAction(ConfirmAction::class)
        ->assertMountedActionModalSee($question)
        ->callAction('confirmWithoutCredit');

    expect($invoice->refresh()->state)->toBe(MoveState::POSTED)
        ->and(round(abs((float) $invoice->amount_residual), 2))->toBe(150.0)
        ->and(Payment::query()->withUnusedAmount()->findOrFail($payment->getKey())->unusedAmount())->toBe(400.0);
});

it('does not offer another customer, a bill, or another company\'s credit', function () {
    $partner = AccountHelper::partner();
    $payment = unusedCreditPayment($partner, 400);
    $creditLineId = QuickPayment::unusedCustomerCredit(draftInvoiceFor($partner, 150))['line_ids'][0];

    $someoneElse = Partner::factory()->create([
        'company_id'                     => $partner->company_id,
        'property_account_receivable_id' => AccountHelper::account('receivable')->id,
        'property_account_payable_id'    => AccountHelper::account('payable')->id,
    ]);

    expect(QuickPayment::unusedCreditQuestion(draftInvoiceFor($someoneElse, 150)))->toBeNull();

    $bill = AccountHelper::invoice(MoveType::IN_INVOICE, $partner);
    AccountHelper::productLine($bill, AccountHelper::account('expense'), qty: 1, priceUnit: 150);
    AccountHelper::compute($bill);

    expect(QuickPayment::unusedCreditQuestion($bill->refresh()))->toBeNull();

    $otherCompany = Company::factory()->create();
    MoveLine::withoutGlobalScopes()->whereKey($creditLineId)->update(['company_id' => $otherCompany->id]);

    expect(QuickPayment::unusedCreditQuestion(draftInvoiceFor($partner, 150)))->toBeNull()
        ->and($payment->company_id)->not->toBe($otherCompany->id);
});

it('offers the unused credit in record payment and applies it before taking more money', function () {
    FilamentHelper::actingAs([
        'view_any_account_invoice',
        'update_account_invoice',
        QuickPayment::PERMISSION,
    ]);

    $partner = AccountHelper::partner();
    $payment = unusedCreditPayment($partner, 400);
    $invoice = draftInvoiceFor($partner, 150);
    AccountHelper::post($invoice);
    $paymentsBefore = Payment::query()->withoutGlobalScopes()->count();

    $question = __('accounts::filament/resources/invoice/actions/confirm-action.unused-credit.question', [
        'amount' => money(400, $invoice->currency->name),
    ]);

    Livewire::test(EditInvoice::class, ['record' => $invoice->id])
        ->mountAction(PayAction::class)
        ->assertMountedActionModalSee($question)
        ->callMountedAction();

    expect(round(abs((float) $invoice->refresh()->amount_residual), 2))->toBe(0.0)
        ->and(Payment::query()->withoutGlobalScopes()->count())->toBe($paymentsBefore)
        ->and(Payment::query()->withUnusedAmount()->findOrFail($payment->getKey())->unusedAmount())->toBe(250.0);
});
