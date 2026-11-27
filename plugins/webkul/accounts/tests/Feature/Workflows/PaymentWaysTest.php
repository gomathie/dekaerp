<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Models\Journal;
use Webkul\Account\Services\PaymentWaysProvisioner;
use Webkul\Account\Services\QuickPayment;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../Helpers/AccountHelper.php';

/**
 * Ready-made ways to pay and "Paid now" (user, 2026-11-19/20): plain names,
 * Mobile Money with its own account, an own way added from the Pay window,
 * and a new invoice confirmed and paid in one step.
 */
beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');

    DB::table('plugins')->updateOrInsert(
        ['name' => 'accounts'],
        ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
    );

    Package::$plugins = Plugin::all()->keyBy('name');

    // Recording a payment needs its permission (2026-11-26).
    SecurityHelper::grantPermissions(AccountHelper::actingAsAdmin(), ['create_invoice_payment']);
});

it('renames the default journals and adds Mobile Money with its own account, once', function () {
    $company = AccountHelper::company();
    $bank = AccountHelper::bankJournal();
    $bank->forceFill(['code' => 'BANK', 'name' => 'Bank Transactions'])->save();

    $provisioner = app(PaymentWaysProvisioner::class);
    $provisioner->provision($company);
    $provisioner->provision($company);

    $momo = Journal::withoutGlobalScopes()->where('company_id', $company->id)->where('code', 'MOMO')->get();

    expect($bank->refresh()->name)->toBe('Bank transfer')
        ->and($momo)->toHaveCount(1)
        ->and($momo->first()->name)->toBe('Mobile Money')
        ->and($momo->first()->default_account_id)->not->toBe($bank->default_account_id)
        ->and(DB::table('accounts_account_companies')->where('account_id', $momo->first()->default_account_id)->value('company_id'))->toBe($company->id)
        ->and(DB::table('accounts_payment_method_lines')->where('journal_id', $momo->first()->id)->count())
        ->toBe(DB::table('accounts_payment_method_lines')->where('journal_id', $bank->id)->count());
});

it('leaves a journal someone renamed alone', function () {
    $company = AccountHelper::company();
    $bank = AccountHelper::bankJournal();
    $bank->forceFill(['code' => 'BANK', 'name' => 'GCB Current Account'])->save();

    app(PaymentWaysProvisioner::class)->provision($company);

    expect($bank->refresh()->name)->toBe('GCB Current Account');
});

it('adds a company\'s own way to pay with its own account', function () {
    $company = AccountHelper::company();
    $bank = AccountHelper::bankJournal();
    $bank->forceFill(['code' => 'BANK'])->save();

    $journalId = app(PaymentWaysProvisioner::class)->addWay($company, 'Wave');

    $wave = Journal::withoutGlobalScopes()->findOrFail($journalId);

    expect($wave->name)->toBe('Wave')
        ->and($wave->company_id)->toBe($company->id)
        ->and($wave->default_account_id)->not->toBe($bank->default_account_id);
});

it('confirms a new invoice and pays it in full in one step', function () {
    $bank = AccountHelper::bankJournal();

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE);
    AccountHelper::productLine($invoice, AccountHelper::account('income'), qty: 1, priceUnit: 300);
    AccountHelper::compute($invoice);

    $paid = app(QuickPayment::class)->confirmAndPay($invoice, $bank->id);

    expect($paid->state)->toBe(MoveState::POSTED)
        ->and($paid->payment_state)->toBeIn([PaymentState::PAID, PaymentState::IN_PAYMENT]);
});

it('refuses another company\'s journal for paid now', function () {
    // Another client's journal, set up with nobody acting (UF-025 guard).
    $foreign = CompanyHelper::asNobody(fn () => Journal::factory()->bank()->create([
        'company_id' => Company::factory()->create()->id,
    ]));

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE);
    AccountHelper::productLine($invoice, AccountHelper::account('income'), qty: 1, priceUnit: 100);
    AccountHelper::compute($invoice);
    AccountHelper::bankJournal();

    expect(fn () => app(QuickPayment::class)->confirmAndPay($invoice, $foreign->id))
        ->toThrow(ValidationException::class, 'This way to pay cannot be used for this invoice.');
});

it('says what remains after a part payment and what an overpayment keeps', function () {
    $currency = AccountHelper::currency()->name;
    $balance = 'accounts::filament/resources/invoice/actions/pay-action.form.hints.balance-after';
    $credit = 'accounts::filament/resources/invoice/actions/pay-action.form.hints.kept-as-credit';

    expect(QuickPayment::remainderLines(200, 50, $currency))->toBe([
        __($balance, ['amount' => money(150, $currency)]),
    ])->and(QuickPayment::remainderLines(200, 250, $currency))->toBe([
        __($credit, ['amount' => money(50, $currency)]),
        __($balance, ['amount' => money(0, $currency)]),
    ]);
});

it('starts the next payment on the way this user last used for this company', function () {
    $cash = AccountHelper::bankJournal();
    $momoId = app(PaymentWaysProvisioner::class)->addWay(AccountHelper::company(), 'Wave');

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE);
    AccountHelper::productLine($invoice, AccountHelper::account('income'), qty: 1, priceUnit: 40);
    AccountHelper::compute($invoice);

    app(QuickPayment::class)->confirmAndPay($invoice, $momoId);

    $allowed = [$cash->id, $momoId];

    expect(QuickPayment::rememberedJournalId((int) AccountHelper::company()->id, $allowed, PaymentType::RECEIVE))->toBe($momoId)
        ->and(QuickPayment::rememberedJournalId((int) AccountHelper::company()->id, [$cash->id], PaymentType::RECEIVE))->toBeNull()
        ->and(QuickPayment::rememberedJournalId((int) AccountHelper::company()->id, $allowed, PaymentType::SEND))->toBeNull()
        ->and(QuickPayment::rememberedJournalId((int) Company::factory()->create()->id, $allowed, PaymentType::RECEIVE))->toBeNull();
});
