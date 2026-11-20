<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Models\Journal;
use Webkul\Account\Services\PaymentWaysProvisioner;
use Webkul\Account\Services\QuickPayment;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
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

    AccountHelper::actingAsAdmin();
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
