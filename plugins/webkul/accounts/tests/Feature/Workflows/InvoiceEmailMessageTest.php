<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions\PrintAndSendAction;
use Webkul\Partner\Models\BankAccount;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/AccountHelper.php';

/**
 * Plan 10, item 4 (2026-11-25): "Print & Send" opened with a fixed English
 * message signed "Administrator", whoever sent it, and "for account N/A"
 * when the invoice had no bank account. It now speaks the sender's
 * language, is signed with the sender's name and the company, and names the
 * account only when there is one.
 */
beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');

    DB::table('plugins')->updateOrInsert(
        ['name' => 'accounts'],
        ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
    );

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    $this->user = AccountHelper::actingAsAdmin();
    $this->user->forceFill(['name' => 'Ama Sender'])->saveQuietly();
});

it('signs the invoice email with the sender and the company, without a bank sentence when no account is set', function () {
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, AccountHelper::partner());

    $message = PrintAndSendAction::defaultMessage($invoice->refresh());

    expect($message)->toContain('Ama Sender')
        ->and($message)->toContain(e($invoice->company->name))
        ->and($message)->toContain(e($invoice->name))
        ->and($message)->not->toContain('Administrator')
        ->and($message)->not->toContain('N/A')
        ->and($message)->not->toContain('When making the payment');
});

it('names the bank account in the invoice email when the invoice has one', function () {
    $partner = AccountHelper::partner();
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $partner);

    $account = BankAccount::factory()->create(['partner_id' => $partner->id, 'account_number' => 'GH-0042-7781']);
    $invoice->forceFill(['partner_bank_id' => $account->id])->saveQuietly();

    $message = PrintAndSendAction::defaultMessage($invoice->refresh());

    expect($message)->toContain('When making the payment')
        ->and($message)->toContain('GH-0042-7781');
});

it('writes the invoice email in the sender\'s language', function () {
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, AccountHelper::partner());

    app()->setLocale('fr');

    $message = PrintAndSendAction::defaultMessage($invoice->refresh());

    expect($message)->toContain('Cordialement')
        ->and($message)->toContain('Bonjour')
        ->and($message)->not->toContain('Best regards');
});

it('escapes a contact name in the invoice email', function () {
    $partner = AccountHelper::partner();
    $partner->forceFill(['name' => '<script>alert(1)</script>'])->saveQuietly();

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $partner);

    $message = PrintAndSendAction::defaultMessage($invoice->refresh());

    expect($message)->not->toContain('<script>')
        ->and($message)->toContain('&lt;script&gt;');
});
