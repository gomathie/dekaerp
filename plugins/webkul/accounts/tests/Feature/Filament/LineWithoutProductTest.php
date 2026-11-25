<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Exceptions\PeriodLockedException;
use Webkul\Account\Filament\Resources\BillResource\Pages\CreateBill;
use Webkul\Account\Filament\Resources\InvoiceResource;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\CreateInvoice;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\EditInvoice;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\LockDate;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Support\MoveDocument;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../Helpers/AccountHelper.php';

/**
 * Plan #9 R-5 (user, 2026-11-25, after approving the mockup): invoices and
 * bills take a line without a product - a description, an account, a
 * quantity, a price and taxes that start empty. It is posted as a product
 * line on the account chosen, and its totals, taxes, journal entry and
 * printed document include it.
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
 * The repeater item the "Add a line without a product" button made.
 *
 * @return array{0: string, 1: array<string, mixed>}
 */
function addedLineWithoutProduct($component): array
{
    $lines = collect($component->get('data.products'))->filter(fn ($line): bool => ! empty($line['without_product']));

    expect($lines)->toHaveCount(1);

    return [(string) $lines->keys()->first(), $lines->first()];
}

/** A line without a product, saved the way the form saves it. */
function savedLineWithoutProduct(Move $move, Account $account, float $qty, float $price, array $taxIds = []): MoveLine
{
    $line = $move->invoiceLines()->create(InvoiceResource::prepareLineWithoutProduct([
        'name'        => 'Site survey',
        'account_id'  => $account->id,
        'quantity'    => $qty,
        'price_unit'  => $price,
        'discount'    => 0,
        'currency_id' => $move->currency_id,
    ]));

    $line->taxes()->sync($taxIds);

    return $line;
}

it('adds a line without a product to an invoice, on the account chosen and with no tax until one is chosen', function () {
    FilamentHelper::actingAs(['view_any_account_invoice', 'create_account_invoice']);

    $journal = AccountHelper::saleJournal();
    $partner = AccountHelper::partner();
    $survey = AccountHelper::account('income');

    $component = Livewire::test(CreateInvoice::class)
        ->fillForm(['partner_id' => $partner->id, 'journal_id' => $journal->id])
        ->callFormComponentAction('products', 'addLineWithoutProduct')
        ->assertHasNoFormComponentActionErrors();

    [$key, $line] = addedLineWithoutProduct($component);

    // The journal's income account to start with, and no tax.
    expect((int) $line['account_id'])->toBe((int) $journal->default_account_id)
        ->and($line['taxes'] ?? [])->toBe([]);

    $component
        ->set("data.products.{$key}.name", 'Site survey')
        ->set("data.products.{$key}.account_id", $survey->id)
        ->set("data.products.{$key}.quantity", 2)
        ->set("data.products.{$key}.price_unit", 150)
        ->call('create')
        ->assertHasNoFormErrors();

    $invoice = Move::query()->where('partner_id', $partner->id)->where('move_type', MoveType::OUT_INVOICE)->latest('id')->firstOrFail();
    $saved = $invoice->invoiceLines()->firstOrFail();

    // The account chosen survives MoveLine's saving hook and the recompute.
    expect($saved->product_id)->toBeNull()
        ->and($saved->display_type)->toBe(DisplayType::PRODUCT)
        ->and($saved->account_id)->toBe($survey->id)
        ->and($saved->name)->toBe('Site survey')
        ->and($saved->taxes)->toHaveCount(0)
        ->and((float) $saved->price_subtotal)->toBe(300.0)
        ->and((float) $invoice->amount_untaxed)->toBe(300.0)
        ->and((float) $invoice->amount_total)->toBe(300.0);
});

it('requires a description and an account on a line without a product', function () {
    FilamentHelper::actingAs(['view_any_account_invoice', 'create_account_invoice']);

    $component = Livewire::test(CreateInvoice::class)
        ->fillForm(['partner_id' => AccountHelper::partner()->id, 'journal_id' => AccountHelper::saleJournal()->id])
        ->callFormComponentAction('products', 'addLineWithoutProduct');

    [$key] = addedLineWithoutProduct($component);

    $component
        ->set("data.products.{$key}.account_id", null)
        ->call('create')
        ->assertHasFormErrors(["products.{$key}.name" => 'required', "products.{$key}.account_id" => 'required']);
});

it('refuses another company\'s account, or a receivable one, for a line without a product', function () {
    FilamentHelper::actingAs(['view_any_account_invoice', 'create_account_invoice']);

    $company = AccountHelper::company();
    $ours = AccountHelper::account('income');
    $theirs = AccountHelper::account('income');
    $theirs->companies()->sync([Company::factory()->create()->id]);
    $receivable = AccountHelper::account('receivable');

    $offered = InvoiceResource::lineAccountQuery(Account::query(), $company->id)->pluck('id');

    expect($offered)->toContain($ours->id)
        ->not->toContain($theirs->id)
        ->not->toContain($receivable->id);

    // The server refuses it too, not only the list.
    $component = Livewire::test(CreateInvoice::class)
        ->fillForm(['partner_id' => AccountHelper::partner()->id, 'journal_id' => AccountHelper::saleJournal()->id])
        ->callFormComponentAction('products', 'addLineWithoutProduct');

    [$key] = addedLineWithoutProduct($component);

    $component
        ->set("data.products.{$key}.name", 'Site survey')
        ->set("data.products.{$key}.account_id", $theirs->id)
        ->call('create')
        ->assertHasFormErrors(["products.{$key}.account_id"]);
});

it('adds a line without a product to a bill, on the purchase journal\'s expense account', function () {
    FilamentHelper::actingAs(['view_any_account_bill', 'create_account_bill']);

    $journal = AccountHelper::purchaseJournal();
    $partner = AccountHelper::partner();

    $component = Livewire::test(CreateBill::class)
        ->fillForm([
            'partner_id'   => $partner->id,
            'invoice_date' => now(),
            'journal_id'   => $journal->id,
            'currency_id'  => AccountHelper::currency()->id,
        ])
        ->callFormComponentAction('products', 'addLineWithoutProduct')
        ->assertHasNoFormComponentActionErrors();

    [$key, $line] = addedLineWithoutProduct($component);

    expect((int) $line['account_id'])->toBe((int) $journal->default_account_id);

    $component
        ->set("data.products.{$key}.name", 'Office cleaning')
        ->set("data.products.{$key}.quantity", 1)
        ->set("data.products.{$key}.price_unit", 80)
        ->call('create')
        ->assertHasNoFormErrors();

    $bill = Move::query()->where('partner_id', $partner->id)->where('move_type', MoveType::IN_INVOICE)->latest('id')->firstOrFail();
    $saved = $bill->invoiceLines()->firstOrFail();

    expect($saved->product_id)->toBeNull()
        ->and($saved->account_id)->toBe($journal->default_account_id)
        ->and((float) $saved->price_subtotal)->toBe(80.0)
        ->and((float) $bill->amount_total)->toBe(80.0);
});

it('posts a line without a product with the taxes chosen into a balanced journal entry and the printed invoice', function () {
    AccountHelper::actingAsAdmin();

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE);
    $survey = AccountHelper::account('income');
    $vat = AccountHelper::taxWithAccounts(15);

    savedLineWithoutProduct($invoice, $survey, qty: 2, price: 150, taxIds: [$vat->id]);

    AccountHelper::compute($invoice);
    AccountHelper::post($invoice);

    $invoice->refresh();
    $line = $invoice->invoiceLines()->firstOrFail();

    expect($line->account_id)->toBe($survey->id)
        ->and((float) $line->price_subtotal)->toBe(300.0)
        ->and((float) $line->price_total)->toBe(345.0)
        ->and((float) $line->credit)->toBe(300.0)
        ->and((float) $invoice->amount_untaxed)->toBe(300.0)
        ->and((float) $invoice->amount_tax)->toBe(45.0)
        ->and((float) $invoice->amount_total)->toBe(345.0)
        ->and($invoice->taxLines)->toHaveCount(1);

    $lines = $invoice->lines;

    expect(round((float) $lines->sum('debit'), 2))->toBe(round((float) $lines->sum('credit'), 2))
        ->and(round((float) $lines->sum('debit'), 2))->toBe(345.0);

    $printed = MoveDocument::data($invoice)['lines'];

    expect($printed)->toHaveCount(1)
        ->and($printed[0]['name'])->toBe('Site survey');
});

it('keeps a saved line without a product, and its price, when the invoice is opened again', function () {
    FilamentHelper::actingAs(['view_any_account_invoice', 'update_account_invoice']);

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE);
    $survey = AccountHelper::account('income');
    savedLineWithoutProduct($invoice, $survey, qty: 2, price: 150);
    AccountHelper::compute($invoice);

    $component = Livewire::test(EditInvoice::class, ['record' => $invoice->id]);

    [, $line] = addedLineWithoutProduct($component);

    expect((float) $line['price_unit'])->toBe(150.0)
        ->and((float) $line['price_subtotal'])->toBe(300.0)
        ->and((int) $line['account_id'])->toBe($survey->id);

    $component->call('save')->assertHasNoFormErrors();

    $saved = $invoice->refresh()->invoiceLines()->firstOrFail();

    expect($saved->product_id)->toBeNull()
        ->and($saved->account_id)->toBe($survey->id)
        ->and((float) $saved->price_subtotal)->toBe(300.0);
});

it('keeps the lock date on a line without a product of a posted entry', function () {
    AccountHelper::actingAsAdmin();

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, null, null, ['invoice_date' => '2026-01-15', 'date' => '2026-01-15']);
    savedLineWithoutProduct($invoice, AccountHelper::account('income'), qty: 1, price: 100);
    AccountHelper::compute($invoice);
    AccountHelper::post($invoice);

    LockDate::query()->create(['company_id' => AccountHelper::company()->id, 'lock_date' => '2026-01-31']);

    $line = $invoice->refresh()->invoiceLines()->firstOrFail();

    expect(fn () => $line->update(['account_id' => AccountHelper::account('income')->id]))
        ->toThrow(PeriodLockedException::class);
});
