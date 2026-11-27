<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\Payment;
use Webkul\Account\Services\CreditNoteFromInvoice;
use Webkul\Account\Services\QuickPayment;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Support\Models\Company;
use Webkul\Support\Services\CompanyContext;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../Helpers/AccountHelper.php';

/**
 * Plan 10 D5 (user, 2026-11-27): a posted customer invoice becomes a posted
 * credit note in one step. Take it off the invoice, pay it back, or keep
 * it as credit. A refusal leaves no draft.
 */
beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');

    DB::table('plugins')->updateOrInsert(
        ['name' => 'accounts'],
        ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
    );

    Package::$plugins = Plugin::all()->keyBy('name');

    AccountHelper::actingAsAdmin();
});

function creditNoteSource(float $secondPrice = 0): Move
{
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE);

    AccountHelper::productLine($invoice, AccountHelper::account('income'), qty: 1, priceUnit: 100);

    if ($secondPrice > 0) {
        AccountHelper::productLine($invoice, AccountHelper::account('income'), qty: 1, priceUnit: $secondPrice);
    }

    return AccountHelper::post($invoice);
}

function creditNoteRefusal(Move $invoice, string $reason, string $disposition, array $lineIds = []): ValidationException
{
    try {
        app(CreditNoteFromInvoice::class)->issue($invoice, $reason, $disposition, $lineIds);
    } catch (ValidationException $exception) {
        return $exception;
    }

    throw new RuntimeException('The credit note was issued.');
}

it('takes the whole invoice off with a posted credit note', function () {
    $invoice = creditNoteSource();
    $open = (float) $invoice->amount_residual;

    $credit = app(CreditNoteFromInvoice::class)->issue($invoice, 'Returned goods', CreditNoteFromInvoice::APPLY);

    expect($credit->state)->toBe(MoveState::POSTED)
        ->and($credit->move_type)->toBe(MoveType::OUT_REFUND)
        ->and((float) $invoice->refresh()->amount_residual)->toBeLessThan($open)
        ->and((float) $invoice->amount_residual)->toBeLessThan(0.05);
});

it('credits only the selected line', function () {
    $invoice = creditNoteSource(40);
    $open = (float) $invoice->amount_residual;
    $kept = $invoice->invoiceLines()->orderBy('id')->get()->last();

    $credit = app(CreditNoteFromInvoice::class)->issue(
        $invoice,
        'One line back',
        CreditNoteFromInvoice::APPLY,
        [$kept->id],
    );

    expect($credit->state)->toBe(MoveState::POSTED)
        ->and($credit->invoiceLines()->count())->toBe(1)
        ->and((float) $invoice->refresh()->amount_residual)->toBeLessThan($open)
        ->and((float) $invoice->amount_residual)->toBeGreaterThan(0.05);
});

it('keeps the credit note unused and leaves the invoice open', function () {
    $invoice = creditNoteSource();
    $open = (float) $invoice->amount_residual;

    $credit = app(CreditNoteFromInvoice::class)->issue($invoice, 'Hold the credit', CreditNoteFromInvoice::CREDIT);

    expect($credit->state)->toBe(MoveState::POSTED)
        ->and((float) $credit->amount_residual)->toBeGreaterThan(0.05)
        ->and(abs((float) $invoice->refresh()->amount_residual - $open))->toBeLessThan(0.05);
});

it('pays the credit note back and leaves the invoice open', function () {
    SecurityHelper::grantPermissions(AccountHelper::actingAsAdmin(), [QuickPayment::PERMISSION]);

    AccountHelper::bankJournal();

    $invoice = creditNoteSource();
    $open = (float) $invoice->amount_residual;
    $before = Payment::withoutGlobalScopes()->where('company_id', $invoice->company_id)->count();

    $credit = app(CreditNoteFromInvoice::class)->issue($invoice, 'Paid back', CreditNoteFromInvoice::REFUND);

    $payment = Payment::withoutGlobalScopes()
        ->where('company_id', $invoice->company_id)
        ->latest('id')
        ->first();

    expect($credit->state)->toBe(MoveState::POSTED)
        ->and((float) $credit->amount_residual)->toBeLessThan(0.05)
        ->and(abs((float) $invoice->refresh()->amount_residual - $open))->toBeLessThan(0.05)
        ->and(Payment::withoutGlobalScopes()->where('company_id', $invoice->company_id)->count())->toBe($before + 1)
        ->and($payment->payment_type)->toBe(PaymentType::SEND);
});

it('refuses a blank reason and leaves no credit note', function () {
    $invoice = creditNoteSource();

    $exception = creditNoteRefusal($invoice, '   ', CreditNoteFromInvoice::APPLY);

    expect($exception->errors()['reason'][0])->toBe(__('accounts::filament/resources/invoice/actions/reverse.one-step.reason'))
        ->and(Move::withoutGlobalScopes()->where('reversed_entry_id', $invoice->id)->exists())->toBeFalse();
});

it('refuses a line that is not on this invoice', function () {
    $invoice = creditNoteSource();
    $other = creditNoteSource();
    $foreign = $other->invoiceLines()->first();

    $exception = creditNoteRefusal($invoice, 'Wrong line', CreditNoteFromInvoice::APPLY, [$foreign->id]);

    expect($exception->errors()['lines'][0])->toBe(__('accounts::filament/resources/invoice/actions/reverse.one-step.lines-invalid'))
        ->and(Move::withoutGlobalScopes()->where('reversed_entry_id', $invoice->id)->exists())->toBeFalse();
});

it('refuses an invoice in a company the user cannot write to', function () {
    $invoice = creditNoteSource();

    FilamentHelper::actingAsCompanyUser(Company::factory()->create(), ['update_account_invoice']);
    app()->forgetInstance(CompanyContext::class);

    $exception = creditNoteRefusal($invoice, 'Other company', CreditNoteFromInvoice::APPLY);

    expect($exception->errors()['company'][0])->toBe(__('accounts::filament/resources/invoice/actions/reverse.one-step.company'))
        ->and(Move::withoutGlobalScopes()->where('reversed_entry_id', $invoice->id)->exists())->toBeFalse();
});

it('refuses to pay the credit back without the payment permission', function () {
    $invoice = creditNoteSource();

    FilamentHelper::actingAsCompanyUser($invoice->company, ['update_account_invoice']);
    app()->forgetInstance(CompanyContext::class);

    $exception = creditNoteRefusal($invoice, 'Not allowed', CreditNoteFromInvoice::REFUND);

    expect($exception->errors()['disposition'][0])->toBe(__('accounts::filament/resources/invoice/actions/reverse.one-step.not-allowed'))
        ->and(Move::withoutGlobalScopes()->where('reversed_entry_id', $invoice->id)->exists())->toBeFalse();

    expect(QuickPayment::mayRecordPayment())->toBeFalse();
});
