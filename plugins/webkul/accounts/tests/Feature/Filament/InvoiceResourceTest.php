<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions\CancelAction;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions\ConfirmAction;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions\PayAction;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions\ResetToDraftAction;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions\ReverseAction;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions\SetAsCheckedAction;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\CreateInvoice;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\EditInvoice;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\ListInvoices;
use Webkul\Account\Models\Move;
use Webkul\Account\Services\SaleCurrencyQuestion;
use Webkul\Partner\Models\Partner;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Product\Models\Product;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
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

it('forbids listing invoices without permission', function () {
    FilamentHelper::actingAs([]);

    Livewire::test(ListInvoices::class)->assertForbidden();
});

it('lists invoices with their key columns for authorized users', function () {
    FilamentHelper::actingAs(['view_any_account_invoice']);

    Livewire::test(ListInvoices::class)
        ->assertOk()
        ->assertCanRenderTableColumn('name')
        ->assertCanRenderTableColumn('state');
});

/**
 * Plan #9 R-9 (2026-11-24): a contact's company shows under their name, so
 * "Ama Mensah" reads as Acme's buyer without opening the invoice.
 */
it('shows the company a customer works for under their name', function () {
    FilamentHelper::actingAs(['view_any_account_invoice']);

    $acme = Partner::factory()->create(['company_id' => current_company_id(), 'name' => 'Acme Holdings Ltd']);
    $buyer = AccountHelper::partner();
    $buyer->forceFill(['name' => 'Ama Mensah', 'parent_id' => $acme->getKey()])->save();

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $buyer);

    Livewire::test(ListInvoices::class)
        ->assertCanSeeTableRecords([$invoice])
        ->assertSee('Ama Mensah')
        ->assertSee('Acme Holdings Ltd');
});

/**
 * Upstream #1671: the list ordered by id inside getEloquentQuery(), ahead of
 * any column sort, so clicking a column header changed nothing.
 */
it('lists newest invoices first and still follows a column sort', function () {
    FilamentHelper::actingAs(['view_any_account_invoice']);

    $older = AccountHelper::invoice(overrides: ['invoice_date' => '2026-01-10']);
    $newer = AccountHelper::invoice(overrides: ['invoice_date' => '2026-01-01']);

    Livewire::test(ListInvoices::class)
        ->assertCanSeeTableRecords([$newer, $older], inOrder: true)
        ->sortTable('invoice_date', 'desc')
        ->assertCanSeeTableRecords([$older, $newer], inOrder: true);
});

/**
 * TRACE365, 2026-11-19: a customer can be added from the invoice ("+"),
 * stamped with the current company, and only by someone who may create
 * contacts.
 */
it('adds a new customer from the invoice form', function () {
    FilamentHelper::actingAs(['view_any_account_invoice', 'create_account_invoice', 'create_partner_partner']);

    Livewire::test(CreateInvoice::class)
        ->callFormComponentAction('partner_id', 'createOption', ['name' => 'Walk-in Customer', 'phone' => '0244000000'])
        ->assertHasNoFormComponentActionErrors();

    $customer = Partner::query()->where('name', 'Walk-in Customer')->firstOrFail();

    expect($customer->company_id)->toBe(current_company_id());
});

/**
 * User, 2026-11-20 (after Zoho Books): a product or service can be added
 * from the invoice line ("+"), with the product form's unit default.
 */
it('adds a new service from an invoice line', function () {
    FilamentHelper::actingAs(['view_any_account_invoice', 'create_account_invoice', 'create_product_product']);

    $component = Livewire::test(CreateInvoice::class)
        ->set('data.products', ['line-1' => ['product_id' => null, 'quantity' => 1, 'price_unit' => 0, 'discount' => 0, 'taxes' => []]]);
    $key = 'line-1';

    $component
        ->callFormComponentAction("products.{$key}.product_id", 'createOption', [
            'name'  => 'Installation labour',
            'type'  => 'service',
            'price' => 150,
        ])
        ->assertHasNoFormComponentActionErrors();

    $product = Product::query()->where('name', 'Installation labour')->firstOrFail();

    expect((float) $product->price)->toBe(150.0)
        ->and($product->company_id)->toBe(current_company_id())
        ->and($product->uom_id)->not->toBeNull();
});

/**
 * User, 2026-11-20: the salesperson sits next to the customer, starts as
 * whoever creates the invoice, and lists only this company's people (it
 * used to list every user on the installation).
 */
it('fills the salesperson with the creator and lists only company members', function () {
    $company = CompanyHelper::company(['name' => 'Salesperson Co', 'is_active' => true]);
    $otherCompany = CompanyHelper::company(['name' => 'Salesperson Other Co', 'is_active' => true]);

    $user = FilamentHelper::actingAsCompanyUser($company, ['view_any_account_invoice', 'create_account_invoice']);

    $colleague = CompanyHelper::asNobody(fn () => User::factory()->create(['name' => 'Same Company Colleague']));
    $colleague->allowedCompanies()->attach($company->getKey());

    $stranger = CompanyHelper::asNobody(fn () => User::factory()->create(['name' => 'Other Client Staff']));
    $stranger->allowedCompanies()->attach($otherCompany->getKey());

    $component = Livewire::test(CreateInvoice::class)
        ->assertFormSet(['invoice_user_id' => $user->getKey()]);

    $options = $component->instance()->form->getComponent('invoice_user_id')->getOptions();

    expect($options)->toHaveKey($colleague->getKey())
        ->not->toHaveKey($stranger->getKey());
});

/**
 * Sentry DEKAERP-22, 2026-11-22: "Paid now" on an invoice with no lines
 * reached posting, whose plain Exception became an error page. It is now a
 * warning, and nothing is saved.
 */
it('warns instead of failing when paid now is ticked on an invoice with no lines', function () {
    AccountHelper::actingAsAdmin();
    $bank = AccountHelper::bankJournal();
    $customer = Partner::factory()->create(['company_id' => current_company_id()]);
    $before = Move::query()->withoutGlobalScopes()->count();

    Livewire::test(CreateInvoice::class)
        ->fillForm(['partner_id' => $customer->getKey(), 'paid_now' => true, 'paid_journal_id' => $bank->getKey()])
        ->call('create')
        ->assertNotified(__('accounts::filament/resources/invoice/actions/confirm-action.notification.error.title'));

    expect(Move::query()->withoutGlobalScopes()->count())->toBe($before);
});

/**
 * "New direct sale" and its receipt (user, 2026-11-22): the button sits on
 * the invoice list, and the receipt shows the sale's lines, total and what
 * was paid, with customer text escaped.
 */
it('offers a direct sale on the invoice list and prints a receipt for it', function () {
    AccountHelper::actingAsAdmin();

    Livewire::test(ListInvoices::class)->assertActionVisible('newDirectSale');

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE);
    $invoice->partner->forceFill(['name' => 'Ama <script>x</script>'])->save();
    AccountHelper::productLine($invoice, AccountHelper::account('income'), qty: 2, priceUnit: 75);
    AccountHelper::compute($invoice);

    $html = view('accounts::invoice/actions/receipt.index', ['record' => $invoice->fresh(), 'isRtl' => false])->render();

    expect($html)->toContain(__('accounts::filament/resources/invoice/pages/create-invoice.receipt.title'))
        ->toContain('Ama &lt;script&gt;')
        ->not->toContain('<script>x</script>')
        ->toContain('150');
});

it('hides the add-customer button without the contact create permission', function () {
    FilamentHelper::actingAs(['view_any_account_invoice', 'create_account_invoice']);

    Livewire::test(CreateInvoice::class)
        ->assertFormComponentActionHidden('partner_id', 'createOption');
});

it('hides summary cells together with the columns hidden on narrow screens', function () {
    FilamentHelper::actingAs(['view_any_account_invoice']);

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE);
    AccountHelper::productLine($invoice, AccountHelper::account('income'), qty: 2, priceUnit: 100);
    AccountHelper::compute($invoice);

    $html = Livewire::test(ListInvoices::class)->assertOk()->html();

    expect(preg_match('/<tr[^>]*fi-ta-summary-row[^>]*>(.*?)<\/tr>/s', $html, $summaryRow))->toBe(1);

    // State, created-by and the dates hide on phones, so the "Summary" heading must not span
    // them; and the totals of amount due (md) and untaxed/tax (lg) must hide with their columns.
    expect($summaryRow[1])
        ->not->toContain('colspan')
        ->toContain('md:fi-visible')
        ->toContain('lg:fi-visible');
});

it('renders the invoice create page', function () {
    FilamentHelper::actingAs(['view_any_account_invoice', 'create_account_invoice']);

    Livewire::test(CreateInvoice::class)->assertOk();
});

it('posts a draft invoice through the confirm action', function () {
    FilamentHelper::actingAs(['view_any_account_invoice', 'update_account_invoice']);

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE);
    AccountHelper::productLine($invoice, AccountHelper::account('income'), qty: 2, priceUnit: 100);
    AccountHelper::compute($invoice);

    Livewire::test(EditInvoice::class, ['record' => $invoice->id])
        ->assertOk()
        ->assertActionExists(ConfirmAction::class)
        ->callAction(ConfirmAction::class);

    expect($invoice->refresh()->state)->toBe(MoveState::POSTED);
});

/**
 * Plan #10 MC-2 step 5 (TRACE365, 2026-11-24): before a foreign-currency
 * invoice is posted, Confirm says what it books in the company currency, from
 * the invoice's own rate (that of its date, not today's), and names the date
 * the rate belongs to when that is not today. Extended 2026-11-25 ("offers
 * today's rate"): when the latest rate differs, the summary also says what
 * the document would book at it; plain Confirm still posts at its own rate.
 */
it('summarises a foreign-currency invoice in the company currency before posting it', function () {
    FilamentHelper::actingAs(['view_any_account_invoice', 'update_account_invoice']);

    $company = AccountHelper::company()->load('currency');
    $foreign = AccountHelper::otherCurrency();
    $invoiceDate = now()->subDays(3);
    AccountHelper::setCurrencyRate($foreign, 10.0, now()->subDays(5)->toDateString());
    // Today's rate differs: the summary must not use it.
    AccountHelper::setCurrencyRate($foreign, 20.0, now()->toDateString());

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, overrides: [
        'currency_id'  => $foreign->getKey(),
        'invoice_date' => $invoiceDate->toDateString(),
        'date'         => $invoiceDate->toDateString(),
    ]);
    AccountHelper::productLine($invoice, AccountHelper::account('income'), qty: 2, priceUnit: 600);
    AccountHelper::compute($invoice);

    $lang = 'accounts::filament/resources/invoice/actions/confirm-action.currency-summary';
    $question = app(SaleCurrencyQuestion::class);

    // 1,200 in the invoice's currency at 10 per company unit is 120.
    $total = __($lang.'.total', [
        'amount' => money(1200.0, $foreign->name),
        'value'  => money(120.0, $company->currency->name),
        'rate'   => $question->label(10.0, $company->currency, $foreign),
    ]);
    $rateDate = __($lang.'.rate-date', ['date' => $invoiceDate->translatedFormat('j M Y')]);
    // 1,200 at the latest rate of 20 would be 60.
    $latest = __($lang.'.latest', [
        'rate'  => $question->label(20.0, $company->currency, $foreign),
        'date'  => now()->translatedFormat('j M Y'),
        'value' => money(60.0, $company->currency->name),
    ]);

    expect($question->postingSummary($invoice->refresh()))->toBe($total.' '.$rateDate.' '.$latest.' '.__($lang.'.question'));

    $component = Livewire::test(EditInvoice::class, ['record' => $invoice->id])
        ->mountAction(ConfirmAction::class)
        ->assertActionMounted(ConfirmAction::class)
        ->assertMountedActionModalSee([$total, $rateDate]);

    // Nothing is posted until the summary is confirmed.
    expect($invoice->refresh()->state)->toBe(MoveState::DRAFT);

    $component->callMountedAction();

    expect($invoice->refresh()->state)->toBe(MoveState::POSTED)
        ->and(abs((float) $invoice->amount_total_signed))->toBe(120.0)
        ->and($invoice->currency_rate_source)->toBeNull();
});

/**
 * Plan #10 MC-2 step 5, "offers today's rate" (2026-11-25): a back-dated
 * foreign document can be posted at the latest rate instead, by choice. It is
 * kept as a confirmed rate under the person's name (a feed rate would be
 * re-read for the invoice date on the next save), the lines are re-costed at
 * it, and the history says so. With nothing to offer there is no button.
 */
it('posts a back-dated foreign invoice at the latest rate when that is chosen', function () {
    $user = FilamentHelper::actingAs(['view_any_account_invoice', 'update_account_invoice']);

    $company = AccountHelper::company()->load('currency');
    $foreign = AccountHelper::otherCurrency();
    $invoiceDate = now()->subDays(3);
    AccountHelper::setCurrencyRate($foreign, 10.0, now()->subDays(5)->toDateString());
    AccountHelper::setCurrencyRate($foreign, 20.0, now()->toDateString());

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, overrides: [
        'currency_id'  => $foreign->getKey(),
        'invoice_date' => $invoiceDate->toDateString(),
        'date'         => $invoiceDate->toDateString(),
    ]);
    AccountHelper::productLine($invoice, AccountHelper::account('income'), qty: 2, priceUnit: 600);
    AccountHelper::compute($invoice);

    $question = app(SaleCurrencyQuestion::class);

    expect($question->latestRateOffer($invoice->refresh())['rate'])->toBe(20.0);

    Livewire::test(EditInvoice::class, ['record' => $invoice->id])
        ->mountAction(ConfirmAction::class)
        ->assertMountedActionModalSee(__('accounts::filament/resources/invoice/actions/confirm-action.currency-summary.use-latest'))
        ->callAction('postAtLatestRate');

    $invoice->refresh();

    expect($invoice->state)->toBe(MoveState::POSTED)
        ->and((float) $invoice->invoice_currency_rate)->toBe(20.0)
        ->and($invoice->currency_rate_source)->toBe(Move::RATE_MANUAL)
        ->and((int) $invoice->currency_rate_confirmed_by)->toBe($user->id)
        // 1,200 at 20 per company unit is 60, on the lines as well as the total.
        ->and(abs((float) $invoice->amount_total_signed))->toBe(60.0)
        ->and(abs((float) $invoice->lines()->where('display_type', 'product')->sum('balance')))->toBe(60.0)
        ->and($invoice->messages()->where('body', 'like', '%'.$question->label(20.0, $company->currency, $foreign).'%')->exists())->toBeTrue();

    // Now at the latest rate, so nothing is offered; and a posted document's
    // rate is never changed, whatever is asked.
    expect($question->latestRateOffer($invoice))->toBeNull()
        ->and(fn () => $question->applyLatestRate($invoice, $user->id))->toThrow(InvalidArgumentException::class);

    // Dated today, or already at the latest rate: no offer, no second button.
    $today = AccountHelper::invoice(MoveType::OUT_INVOICE, overrides: ['currency_id' => $foreign->getKey()]);
    AccountHelper::productLine($today, AccountHelper::account('income'), qty: 1, priceUnit: 100);
    AccountHelper::compute($today);

    expect($question->latestRateOffer($today->refresh()))->toBeNull();
});

it('confirms a company-currency invoice with one click and summarises a foreign bill', function () {
    FilamentHelper::actingAs(['view_any_account_invoice', 'update_account_invoice']);

    $question = app(SaleCurrencyQuestion::class);

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE);
    AccountHelper::productLine($invoice, AccountHelper::account('income'), qty: 2, priceUnit: 100);
    AccountHelper::compute($invoice);

    expect($question->postingSummary($invoice->refresh()))->toBeNull()
        ->and($question->postingSummary(AccountHelper::journalEntry()))->toBeNull();

    // No dialog: mounting the action posts straight away, as before.
    Livewire::test(EditInvoice::class, ['record' => $invoice->id])
        ->mountAction(ConfirmAction::class)
        ->assertActionNotMounted(ConfirmAction::class);

    expect($invoice->refresh()->state)->toBe(MoveState::POSTED);

    $company = AccountHelper::company()->load('currency');
    $foreign = AccountHelper::otherCurrency();
    AccountHelper::setCurrencyRate($foreign, 4.0, now()->subDay()->toDateString());

    $bill = AccountHelper::invoice(MoveType::IN_INVOICE, overrides: ['currency_id' => $foreign->getKey()]);
    AccountHelper::productLine($bill, AccountHelper::account('expense'), qty: 1, priceUnit: 400);
    AccountHelper::compute($bill);

    // Dated today: no date sentence.
    expect($question->postingSummary($bill->refresh()))->toBe(
        __('accounts::filament/resources/invoice/actions/confirm-action.currency-summary.total', [
            'amount' => money(400.0, $foreign->name),
            'value'  => money(100.0, $company->currency->name),
            'rate'   => $question->label(4.0, $company->currency, $foreign),
        ]).' '.__('accounts::filament/resources/invoice/actions/confirm-action.currency-summary.question'),
    );
});

it('cancels a draft invoice through the cancel action', function () {
    FilamentHelper::actingAs(['view_any_account_invoice', 'update_account_invoice']);

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE);

    Livewire::test(EditInvoice::class, ['record' => $invoice->id])
        ->assertOk()
        ->callAction(CancelAction::class);

    expect($invoice->refresh()->state)->toBe(MoveState::CANCEL);
});

function postedInvoiceRecord(): Move
{
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE);

    AccountHelper::productLine($invoice, AccountHelper::account('income'), qty: 2, priceUnit: 100);

    return AccountHelper::post($invoice);
}

it('reverses a posted invoice into a credit note through the action', function () {
    FilamentHelper::actingAs(['view_any_account_invoice', 'update_account_invoice']);

    $invoice = postedInvoiceRecord();

    Livewire::test(EditInvoice::class, ['record' => $invoice->id])
        ->assertOk()
        ->callAction(ReverseAction::class, data: [
            'reason'     => 'Test reversal',
            'journal_id' => $invoice->journal_id,
            'date'       => now(),
        ]);

    expect(
        Move::query()
            ->where('reversed_entry_id', $invoice->id)
            ->where('move_type', MoveType::OUT_REFUND)
            ->exists()
    )->toBeTrue();
});

it('resets a posted invoice to draft through the action', function () {
    FilamentHelper::actingAs(['view_any_account_invoice', 'update_account_invoice']);

    $invoice = postedInvoiceRecord();

    Livewire::test(EditInvoice::class, ['record' => $invoice->id])
        ->assertOk()
        ->callAction(ResetToDraftAction::class, data: ['reason' => 'Customer asked for a lower price']);

    expect($invoice->refresh()->state)->toBe(MoveState::DRAFT);
});

/**
 * Accounting A2b (user, 2026-11-13): reopening a confirmed invoice asks why,
 * like Zoho Books; the screen requires the field.
 */
it('requires a reason before resetting a posted invoice from the screen', function () {
    FilamentHelper::actingAs(['view_any_account_invoice', 'update_account_invoice']);

    $invoice = postedInvoiceRecord();

    Livewire::test(EditInvoice::class, ['record' => $invoice->id])
        ->callAction(ResetToDraftAction::class, data: ['reason' => ''])
        ->assertHasActionErrors(['reason' => 'required']);

    expect($invoice->refresh()->state)->toBe(MoveState::POSTED);
});

it('marks a posted invoice as checked through the action', function () {
    FilamentHelper::actingAs(['view_any_account_invoice', 'update_account_invoice']);

    $invoice = postedInvoiceRecord();

    Livewire::test(EditInvoice::class, ['record' => $invoice->id])
        ->assertOk()
        ->callAction(SetAsCheckedAction::class);

    expect($invoice->refresh()->checked)->toBeTrue();
});

it('registers a full payment and marks the invoice paid through the action', function () {
    FilamentHelper::actingAs(['view_any_account_invoice', 'update_account_invoice']);

    AccountHelper::bankJournal();

    $invoice = postedInvoiceRecord();

    Livewire::test(EditInvoice::class, ['record' => $invoice->id])
        ->assertOk()
        ->assertActionVisible(PayAction::class)
        ->callAction(PayAction::class)
        ->assertActionHidden(PayAction::class);

    expect($invoice->refresh()->payment_state)->toBe(PaymentState::PAID);
});
