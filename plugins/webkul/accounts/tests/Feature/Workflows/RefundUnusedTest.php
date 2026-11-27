<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Filament\Resources\PaymentResource\Pages\ViewPayment;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Models\Payment;
use Webkul\Account\Services\QuickPayment;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Security\Models\Role;
use Webkul\Support\Models\Company;
use Webkul\Support\Services\CompanyContext;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../Helpers/AccountHelper.php';

/**
 * U100: a Company Admin pays back the unused amount of a customer payment.
 * The figure is the open credit, and the payment out is matched to it.
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

function overpaidCustomerPayment(): Payment
{
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE);
    AccountHelper::productLine($invoice, AccountHelper::account('income'), 1, 500);
    AccountHelper::compute($invoice);
    AccountHelper::post($invoice);
    AccountHelper::pay($invoice, 650);

    return Payment::query()->withUnusedAmount()
        ->whereHas('invoices', fn ($query) => $query->whereKey($invoice->getKey()))
        ->firstOrFail();
}

function actAsCompanyAdmin(int $companyId): void
{
    $user = FilamentHelper::actingAs(['view_any_account_payment', 'view_account_payment', 'update_account_payment']);
    $user->assignRole(Role::findOrCreate(Role::COMPANY_ADMIN, 'web'));
    $user->allowedCompanies()->sync([$companyId]);
    $user->forceFill(['default_company_id' => $companyId])->saveQuietly();
    session([CompanyContext::SESSION_KEY => [$companyId]]);
    app()->forgetInstance(CompanyContext::class);
}

/**
 * @return array<string, array<int, string>>
 */
function refundErrors(callable $refund): array
{
    try {
        $refund();
    } catch (ValidationException $exception) {
        return $exception->errors();
    }

    throw new RuntimeException('The refund was not refused.');
}

it('refunds the unused amount with one payment out and clears the credit', function () {
    $payment = overpaidCustomerPayment();
    actAsCompanyAdmin((int) $payment->company_id);

    expect($payment->unusedAmount())->toBe(150.0);

    $before = Payment::withoutGlobalScopes()->where('company_id', $payment->company_id)->count();
    $refund = QuickPayment::refundUnused($payment, (int) $payment->journal_id, now()->toDateString());
    $paidOut = MoveLine::withoutGlobalScopes()
        ->where('move_id', $refund->move_id)
        ->where('account_id', $refund->outstanding_account_id)
        ->first();

    expect(Payment::withoutGlobalScopes()->where('company_id', $payment->company_id)->count())->toBe($before + 1)
        ->and($refund->payment_type)->toBe(PaymentType::SEND)
        ->and(round((float) $refund->amount, 2))->toBe(150.0)
        ->and((int) $refund->source_payment_id)->toBe((int) $payment->id)
        ->and((int) $refund->company_id)->toBe((int) $payment->company_id)
        ->and((float) $paidOut->credit)->toBeGreaterThan(0.0)
        ->and(Payment::query()->withUnusedAmount()->findOrFail($payment->id)->unusedAmount())->toBe(0.0)
        ->and($payment->messages()->where('body', 'like', '%'.e(Auth::user()->name).'%')->exists())->toBeTrue();
});

it('refuses a clerk, a payment with nothing unused, another way to pay, and another company', function () {
    $payment = overpaidCustomerPayment();
    $journal = (int) $payment->journal_id;

    FilamentHelper::actingAs([QuickPayment::PERMISSION]);

    $clerk = refundErrors(fn () => QuickPayment::refundUnused($payment, $journal, now()->toDateString()));

    actAsCompanyAdmin((int) $payment->company_id);

    $otherJournal = AccountHelper::generalJournal();
    $journalErrors = refundErrors(fn () => QuickPayment::refundUnused($payment, $otherJournal->id, now()->toDateString()));

    $exact = (function () {
        $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE);
        AccountHelper::productLine($invoice, AccountHelper::account('income'), 1, 200);
        AccountHelper::compute($invoice);
        AccountHelper::post($invoice);
        AccountHelper::pay($invoice, 200);

        return Payment::query()->whereHas('invoices', fn ($query) => $query->whereKey($invoice->getKey()))->firstOrFail();
    })();

    $nothing = refundErrors(fn () => QuickPayment::refundUnused($exact, (int) $exact->journal_id, now()->toDateString()));

    actAsCompanyAdmin((int) Company::factory()->create()->id);

    $otherCompany = refundErrors(fn () => QuickPayment::refundUnused($payment, $journal, now()->toDateString()));

    expect($clerk['payment'][0])->toBe(__('accounts::filament/resources/payment/actions/refund-unused.not-allowed'))
        ->and($journalErrors['journal_id'][0])->toBe(__('accounts::filament/resources/payment/actions/refund-unused.journal'))
        ->and($nothing['payment'][0])->toBe(__('accounts::filament/resources/payment/actions/refund-unused.nothing'))
        ->and($otherCompany['payment'][0])->toBe(__('accounts::filament/resources/payment/actions/refund-unused.not-allowed'));
});

it('shows the refund only to a company admin on a payment that still has an unused amount', function () {
    $payment = overpaidCustomerPayment();

    FilamentHelper::actingAs(['view_any_account_payment', 'view_account_payment', 'update_account_payment']);

    Livewire::test(ViewPayment::class, ['record' => $payment->getKey()])
        ->assertActionHidden('refundUnused');

    actAsCompanyAdmin((int) $payment->company_id);

    Livewire::test(ViewPayment::class, ['record' => $payment->getKey()])
        ->assertActionVisible('refundUnused');
});
