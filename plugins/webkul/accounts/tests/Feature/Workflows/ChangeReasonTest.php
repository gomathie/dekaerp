<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Chatter\Models\Message;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/AccountHelper.php';

/**
 * Accounting plan #7, A2b (user, 2026-11-13, "like Zoho Books; customers ask
 * to reduce or increase a price"): reopening a confirmed document needs a
 * reason, enforced in MoveWorkflow, and the reason is kept in the
 * document's history under the document's company.
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
});

function changeReasonInvoice(): Webkul\Account\Models\Move
{
    $move = AccountHelper::invoice(MoveType::OUT_INVOICE);

    AccountHelper::productLine($move, test()->income, qty: 1, priceUnit: 500);

    return AccountHelper::compute($move);
}

it('refuses to reset a posted invoice without a reason', function () {
    $posted = AccountHelper::post(changeReasonInvoice());

    expect(fn () => AccountFacade::resetToDraftMove($posted->refresh(), '   '))
        ->toThrow(Exception::class, 'Give a reason for changing a confirmed document.');

    expect($posted->refresh()->state)->toBe(MoveState::POSTED);
});

it('keeps the reason in the document history under the document company', function () {
    $posted = AccountHelper::post(changeReasonInvoice());

    AccountFacade::resetToDraftMove($posted->refresh(), 'Customer asked for a 10% discount');

    $message = Message::query()
        ->withoutGlobalScopes()
        ->where('messageable_id', $posted->id)
        ->where('body', 'like', '%Customer asked for a 10% discount%')
        ->first();

    expect($posted->refresh()->state)->toBe(MoveState::DRAFT)
        ->and($message)->not->toBeNull()
        ->and($message->company_id)->toBe($posted->company_id)
        ->and($message->body)->toContain('Reset to draft. Reason:');
});

it('escapes the reason, so it cannot inject markup into the history', function () {
    $posted = AccountHelper::post(changeReasonInvoice());

    AccountFacade::resetToDraftMove($posted->refresh(), '<script>alert(1)</script>');

    $body = Message::query()->withoutGlobalScopes()->where('messageable_id', $posted->id)
        ->where('body', 'like', '%Reset to draft%')->value('body');

    expect($body)->not->toContain('<script>')
        ->and($body)->toContain('&lt;script&gt;');
});

it('needs no reason for a draft or an already cancelled document', function () {
    $draft = changeReasonInvoice();

    expect(AccountFacade::cancelMove($draft->refresh())->state)->toBe(MoveState::CANCEL)
        ->and(AccountFacade::resetToDraftMove($draft->refresh())->state)->toBe(MoveState::DRAFT);
});
