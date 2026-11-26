<?php

use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Filament\Resources\BillResource\Pages\CreateBill;
use Webkul\Account\Filament\Resources\BillResource\Pages\EditBill;
use Webkul\Account\Filament\Resources\BillResource\Pages\ListBills;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions\CancelAction;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions\ConfirmAction;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions\PayAction;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions\ResetToDraftAction;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions\ReverseAction;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions\SetAsCheckedAction;
use Webkul\Account\Models\Move;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../Helpers/AccountHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');

    DB::table('plugins')->updateOrInsert(
        ['name' => 'accounts'],
        ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
    );

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');
});

it('forbids listing bills without permission', function () {
    FilamentHelper::actingAs([]);

    Livewire::test(ListBills::class)->assertForbidden();
});

it('lists bills with their key columns for authorized users', function () {
    FilamentHelper::actingAs(['view_any_account_bill']);

    Livewire::test(ListBills::class)
        ->assertOk()
        ->assertCanRenderTableColumn('name')
        ->assertCanRenderTableColumn('state');
});

it('renders the bill create page', function () {
    FilamentHelper::actingAs(['view_any_account_bill', 'create_account_bill']);

    Livewire::test(CreateBill::class)->assertOk();
});

it('creates a draft bill with a number through the create form', function () {
    FilamentHelper::actingAs(['view_any_account_bill', 'create_account_bill']);

    $partner = AccountHelper::partner();

    Livewire::test(CreateBill::class)
        ->fillForm([
            'partner_id'   => $partner->id,
            'invoice_date' => now(),
            'journal_id'   => AccountHelper::purchaseJournal()->id,
            'currency_id'  => AccountHelper::currency()->id,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $bill = Move::query()
        ->where('partner_id', $partner->id)
        ->where('move_type', MoveType::IN_INVOICE)
        ->first();

    expect($bill)->not->toBeNull()
        ->and($bill->name)->not->toBeNull();
});

it('posts a draft bill through the confirm action', function () {
    FilamentHelper::actingAs(['view_any_account_bill', 'update_account_bill']);

    $bill = AccountHelper::invoice(MoveType::IN_INVOICE, null, null, ['invoice_date' => now()]);
    AccountHelper::productLine($bill, AccountHelper::account('expense'), qty: 2, priceUnit: 100);
    AccountHelper::compute($bill);

    Livewire::test(EditBill::class, ['record' => $bill->id])
        ->assertOk()
        ->assertActionExists(ConfirmAction::class)
        ->callAction(ConfirmAction::class);

    expect($bill->refresh()->state)->toBe(MoveState::POSTED);
});

it('cancels a draft bill through the cancel action', function () {
    FilamentHelper::actingAs(['view_any_account_bill', 'update_account_bill']);

    $bill = AccountHelper::invoice(MoveType::IN_INVOICE, null, null, ['invoice_date' => now()]);

    Livewire::test(EditBill::class, ['record' => $bill->id])
        ->assertOk()
        ->callAction(CancelAction::class);

    expect($bill->refresh()->state)->toBe(MoveState::CANCEL);
});

function postedBillRecord(): Move
{
    $bill = AccountHelper::invoice(MoveType::IN_INVOICE, null, null, ['invoice_date' => now()]);

    AccountHelper::productLine($bill, AccountHelper::account('expense'), qty: 2, priceUnit: 100);

    return AccountHelper::post($bill);
}

/**
 * Plan 10, item 3 (2026-11-25): the bills list was headed "Customer", showed
 * totals as negative (-$2,750.00), had no "Paid" column, and pushed the money
 * off a 1440 px screen. The vendor heading, positive amounts and Paid show by
 * default; Tax excluded, Tax and the bill date column start hidden (the date
 * shows under the number) and stay in the column picker.
 */
it('lists bills with the vendor, positive amounts and what is paid', function () {
    FilamentHelper::actingAs(['view_any_account_bill']);

    $bill = postedBillRecord();

    expect((float) $bill->refresh()->amount_total_in_currency_signed)->toBeLessThan(0);

    Livewire::test(ListBills::class)
        ->assertOk()
        ->assertTableColumnExists('invoice_partner_display_name', fn (TextColumn $column): bool => $column->getLabel() === __('accounts::filament/resources/bill.table.columns.vendor'))
        ->assertTableColumnStateSet('amount_total_in_currency_signed', 200.0, $bill)
        ->assertTableColumnStateSet('amount_residual_signed', 200.0, $bill)
        ->assertTableColumnStateSet('amount_paid', 0.0, $bill)
        ->assertCanRenderTableColumn('amount_total_in_currency_signed')
        ->assertCanRenderTableColumn('amount_paid')
        ->assertCanRenderTableColumn('amount_residual_signed')
        ->assertCanNotRenderTableColumn('amount_untaxed_in_currency_signed')
        ->assertCanNotRenderTableColumn('amount_tax_signed')
        ->assertCanNotRenderTableColumn('invoice_date');
});

it('reverses a posted bill into a refund through the action', function () {
    FilamentHelper::actingAs(['view_any_account_bill', 'update_account_bill']);

    $bill = postedBillRecord();

    Livewire::test(EditBill::class, ['record' => $bill->id])
        ->assertOk()
        ->callAction(ReverseAction::class, data: [
            'reason'     => 'Test reversal',
            'journal_id' => $bill->journal_id,
            'date'       => now(),
        ]);

    expect(
        Move::query()
            ->where('reversed_entry_id', $bill->id)
            ->where('move_type', MoveType::IN_REFUND)
            ->exists()
    )->toBeTrue();
});

it('resets a posted bill to draft through the action', function () {
    FilamentHelper::actingAs(['view_any_account_bill', 'update_account_bill']);

    $bill = postedBillRecord();

    Livewire::test(EditBill::class, ['record' => $bill->id])
        ->assertOk()
        ->callAction(ResetToDraftAction::class, data: ['reason' => 'Customer asked for a lower price']);

    expect($bill->refresh()->state)->toBe(MoveState::DRAFT);
});

it('marks a posted bill as checked through the action', function () {
    FilamentHelper::actingAs(['view_any_account_bill', 'update_account_bill']);

    $bill = postedBillRecord();

    Livewire::test(EditBill::class, ['record' => $bill->id])
        ->assertOk()
        ->callAction(SetAsCheckedAction::class);

    expect($bill->refresh()->checked)->toBeTrue();
});

it('registers a full payment and marks the bill paid through the action', function () {
    FilamentHelper::actingAs(['view_any_account_bill', 'update_account_bill']);

    AccountHelper::bankJournal();

    $bill = postedBillRecord();

    Livewire::test(EditBill::class, ['record' => $bill->id])
        ->assertOk()
        ->callAction(PayAction::class);

    expect($bill->refresh()->payment_state)->toBe(PaymentState::PAID);
});
