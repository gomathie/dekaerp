<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Filament\Resources\CreditNoteResource\Pages\CreateCreditNote;
use Webkul\Account\Filament\Resources\InvoiceResource;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\CreateInvoice;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\Payment;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../Helpers/AccountHelper.php';

/**
 * Plan 10 D1 (user, 2026-11-27): "Create and confirm" is the main button on
 * a new customer invoice. "Save as draft" is what Create used to do. Paid
 * now still confirms once, inside confirmAndPay.
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

/**
 * One typed line, the way the invoice form adds it.
 *
 * @return array{0: mixed, 1: string}
 */
function createAndConfirmLine($component): array
{
    $component->callFormComponentAction('products', 'add');

    $key = (string) array_key_last($component->get('data.products'));

    $component
        ->set("data.products.{$key}.product_id", InvoiceResource::TYPED_LINE_PREFIX.'Site survey')
        ->set("data.products.{$key}.quantity", 1)
        ->set("data.products.{$key}.price_unit", 80);

    return [$component, $key];
}

it('posts a new customer invoice from Create and confirm and leaves Save as draft a draft', function () {
    FilamentHelper::actingAs(['view_any_account_invoice', 'create_account_invoice']);

    $partner = AccountHelper::partner();
    $journal = AccountHelper::saleJournal();

    $page = Livewire::test(CreateInvoice::class);
    $names = collect(invade($page->instance())->getFormActions())->map->getName();
    $create = collect(invade($page->instance())->getFormActions())->first(fn ($action) => $action->getName() === 'create');

    expect($names->all())->toContain('create', 'saveDraft')
        ->and($create->getLabel())->toBe(__('accounts::filament/resources/invoice/pages/create-invoice.form.actions.create-and-confirm'));

    [$component] = createAndConfirmLine(
        $page->fillForm(['partner_id' => $partner->id, 'journal_id' => $journal->id]),
    );

    $component->call('createAndConfirm')->assertHasNoFormErrors();

    $posted = Move::query()->where('partner_id', $partner->id)->where('move_type', MoveType::OUT_INVOICE)->latest('id')->firstOrFail();

    expect($posted->state)->toBe(MoveState::POSTED);

    [$draftForm] = createAndConfirmLine(
        Livewire::test(CreateInvoice::class)
            ->fillForm(['partner_id' => $partner->id, 'journal_id' => $journal->id]),
    );

    $draftForm->call('create')->assertHasNoFormErrors();

    $draft = Move::query()
        ->where('partner_id', $partner->id)
        ->where('move_type', MoveType::OUT_INVOICE)
        ->whereKeyNot($posted->id)
        ->latest('id')
        ->firstOrFail();

    expect($draft->state)->toBe(MoveState::DRAFT);
});

it('rolls the invoice back when Create and confirm is refused for having no lines', function () {
    FilamentHelper::actingAs(['view_any_account_invoice', 'create_account_invoice']);

    $partner = AccountHelper::partner();
    $before = Move::query()->withoutGlobalScopes()->count();

    Livewire::test(CreateInvoice::class)
        ->fillForm(['partner_id' => $partner->id, 'journal_id' => AccountHelper::saleJournal()->id])
        ->call('createAndConfirm')
        ->assertNotified(__('accounts::filament/resources/invoice/actions/confirm-action.notification.error.title'));

    expect(Move::query()->withoutGlobalScopes()->count())->toBe($before)
        ->and(Livewire::test(CreateInvoice::class)->instance()->confirmAfterCreate)->toBeFalse();
});

it('confirms a paid-now invoice once, through the payment, not a second time', function () {
    SecurityHelper::grantPermissions(AccountHelper::actingAsAdmin(), ['create_invoice_payment']);

    $partner = AccountHelper::partner();
    $bank = AccountHelper::bankJournal();
    $payments = Payment::query()->withoutGlobalScopes()->count();

    [$component] = createAndConfirmLine(
        Livewire::test(CreateInvoice::class)
            ->fillForm([
                'partner_id'      => $partner->id,
                'journal_id'      => AccountHelper::saleJournal()->id,
                'paid_now'        => true,
                'paid_journal_id' => $bank->id,
            ]),
    );

    $component->call('createAndConfirm')->assertHasNoFormErrors();

    $invoice = Move::query()->where('partner_id', $partner->id)->where('move_type', MoveType::OUT_INVOICE)->latest('id')->firstOrFail();

    expect($invoice->state)->toBe(MoveState::POSTED)
        ->and(Payment::query()->withoutGlobalScopes()->count())->toBe($payments + 1);
});

it('keeps Create on a credit note and does not offer Save as draft there', function () {
    FilamentHelper::actingAs(['view_any_account_credit::note', 'create_account_credit::note']);

    $names = collect(invade(Livewire::test(CreateCreditNote::class)->instance())->getFormActions())->map->getName();

    expect($names->all())->toContain('create')
        ->and($names->all())->not->toContain('saveDraft');
});
