<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\ListInvoices;
use Webkul\Account\Filament\Resources\PaymentResource\Pages\CreatePayment;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\Partner;
use Webkul\Account\Models\Payment;
use Webkul\Account\Services\QuickPayment;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\PaymentResource\Pages\CreatePayment as AccountingCustomerCreatePayment;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\PaymentResource\Pages\CreatePayment as CustomerCreatePayment;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\PaymentResource\Pages\CreatePayment as VendorCreatePayment;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../Helpers/AccountHelper.php';

/**
 * U95 and U96: one received payment for the amounts named on each open
 * invoice. The older invoice is not taken in full just because it is older.
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

function postedCustomerInvoice(Partner $partner, float $amount, string $due): Move
{
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $partner, null, [
        'invoice_date_due' => $due,
    ]);
    AccountHelper::productLine($invoice, AccountHelper::account('income'), 1, $amount);
    AccountHelper::compute($invoice);
    AccountHelper::post($invoice);

    return $invoice->refresh();
}

/**
 * @return array<string, array<int, string>>
 */
function severalPaymentErrors(callable $pay): array
{
    try {
        $pay();
    } catch (ValidationException $exception) {
        return $exception->errors();
    }

    throw new RuntimeException('The payment was not refused.');
}

it('opens record payment on the invoice list and fills the customer\'s open invoices', function () {
    // Opening this dialog in production exhausted 256 MB (Sentry
    // DEKAERP-2C, v1.18.0): the earlier tests only called the service and
    // checked the button, so nothing ever mounted the dialog.
    FilamentHelper::actingAs(['view_any_account_invoice', QuickPayment::PERMISSION]);

    $partner = AccountHelper::partner();
    AccountHelper::bankJournal();
    postedCustomerInvoice($partner, 100, now()->toDateString());

    Livewire::test(ListInvoices::class)
        ->mountAction('recordSeveral')
        ->assertHasNoErrors()
        ->setActionData(['partner_id' => $partner->id])
        ->assertActionDataSet(fn (array $data): bool => count($data['allocations'] ?? []) === 1);
});

it('records one payment for the amounts chosen, and leaves the rest on the older invoice', function () {
    FilamentHelper::actingAs(['view_any_account_invoice', QuickPayment::PERMISSION]);

    $partner = AccountHelper::partner();
    $journal = AccountHelper::bankJournal();
    $older = postedCustomerInvoice($partner, 100, now()->subDays(10)->toDateString());
    $newer = postedCustomerInvoice($partner, 80, now()->toDateString());

    $before = Payment::withoutGlobalScopes()->where('company_id', $older->company_id)->count();

    $payment = QuickPayment::payCustomerInvoices($partner->id, [
        $older->id => 40,
        $newer->id => 80,
    ], $journal->id, now()->toDateString());

    $after = Payment::withoutGlobalScopes()->where('company_id', $older->company_id)->count();

    expect($after)->toBe($before + 1)
        ->and(round((float) $payment->amount, 2))->toBe(120.0)
        ->and(round(abs((float) $older->refresh()->amount_residual), 2))->toBe(60.0)
        ->and(round(abs((float) $newer->refresh()->amount_residual), 2))->toBe(0.0)
        ->and($older->matchedPayments()->pluck('id')->all())->toContain($payment->id)
        ->and($newer->matchedPayments()->pluck('id')->all())->toContain($payment->id);
});

it('refuses invoices that are not one customer, an amount above what is open, and an empty payment', function () {
    FilamentHelper::actingAs([QuickPayment::PERMISSION]);

    $partner = AccountHelper::partner();
    $other = AccountHelper::partner();
    $journal = AccountHelper::bankJournal();
    $invoice = postedCustomerInvoice($partner, 100, now()->subDay()->toDateString());
    $theirs = postedCustomerInvoice($other, 80, now()->toDateString());

    $mixed = severalPaymentErrors(fn () => QuickPayment::payCustomerInvoices($partner->id, [
        $invoice->id => 40,
        $theirs->id  => 80,
    ], $journal->id, now()->toDateString()));

    $tooMuch = severalPaymentErrors(fn () => QuickPayment::payCustomerInvoices($partner->id, [
        $invoice->id => 150,
    ], $journal->id, now()->toDateString()));

    $nothing = severalPaymentErrors(fn () => QuickPayment::payCustomerInvoices($partner->id, [
        $invoice->id => 0,
    ], $journal->id, now()->toDateString()));

    expect($mixed['allocations'][0])->toBe(__('accounts::filament/resources/invoice/actions/record-several.not-together'))
        ->and($tooMuch['allocations'][0])->toBe(__('accounts::filament/resources/invoice/actions/record-several.amount', [
            'invoice' => $invoice->name,
        ]))
        ->and($nothing['allocations'][0])->toBe(__('accounts::filament/resources/invoice/actions/record-several.nothing'));
});

it('refuses a person who cannot record payments, another way to pay, and another company', function () {
    FilamentHelper::actingAs([QuickPayment::PERMISSION]);

    $partner = AccountHelper::partner();
    $journal = AccountHelper::bankJournal();
    $invoice = postedCustomerInvoice($partner, 100, now()->toDateString());
    $general = AccountHelper::generalJournal();

    $journalErrors = severalPaymentErrors(fn () => QuickPayment::payCustomerInvoices(
        $partner->id,
        [$invoice->id => 40],
        $general->id,
        now()->toDateString(),
    ));

    FilamentHelper::actingAs([]);

    $permissionErrors = severalPaymentErrors(fn () => QuickPayment::payCustomerInvoices(
        $partner->id,
        [$invoice->id => 40],
        $journal->id,
        now()->toDateString(),
    ));

    FilamentHelper::actingAsCompanyUser(Company::factory()->create(), [QuickPayment::PERMISSION]);

    $companyErrors = severalPaymentErrors(fn () => QuickPayment::payCustomerInvoices(
        $partner->id,
        [$invoice->id => 40],
        $journal->id,
        now()->toDateString(),
    ));

    expect($journalErrors['journal_id'][0])->toBe(__('accounts::filament/resources/invoice/actions/record-several.journal'))
        ->and($permissionErrors['partner_id'][0])->toBe(__('accounts::filament/resources/invoice/actions/record-several.not-allowed'))
        ->and($companyErrors['partner_id'][0])->toBe(__('accounts::filament/resources/invoice/actions/record-several.company'));
});

it('shows record payment on the invoice list only to someone who may record payments', function () {
    FilamentHelper::actingAs(['view_any_account_invoice']);

    Livewire::test(ListInvoices::class)->assertActionHidden('recordSeveral');

    FilamentHelper::actingAs(['view_any_account_invoice', QuickPayment::PERMISSION]);

    Livewire::test(ListInvoices::class)->assertActionVisible('recordSeveral');
});

it('offers the open invoices on a customer payment and leaves a vendor payment as a draft', function () {
    $customer = (new ReflectionClass(CustomerCreatePayment::class))->getDefaultProperties();
    $accounting = (new ReflectionClass(AccountingCustomerCreatePayment::class))->getDefaultProperties();
    $vendor = (new ReflectionClass(VendorCreatePayment::class))->getDefaultProperties();
    $accounts = (new ReflectionClass(CreatePayment::class))->getDefaultProperties();

    expect($customer['allocatesOpenInvoices'])->toBeTrue()
        ->and($accounting['allocatesOpenInvoices'])->toBeTrue()
        ->and($vendor['allocatesOpenInvoices'])->toBeFalse()
        ->and($accounts['allocatesOpenInvoices'])->toBeFalse();
});
