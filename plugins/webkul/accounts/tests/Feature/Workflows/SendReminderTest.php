<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions\SendReminderAction;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\EditInvoice;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\ViewInvoice;
use Webkul\Account\Mail\Invoice\Actions\InvoiceEmail;
use Webkul\Account\Models\Move;
use Webkul\Account\Services\MoveWorkflow;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Support\Models\Company;
use Webkul\Support\Services\CompanyContext;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../Helpers/AccountHelper.php';

/**
 * Plan 10 D6 (user, 2026-11-27): a manual reminder on an overdue customer
 * invoice. The mail uses the invoice email path, after commit, and only
 * this invoice's own customer.
 */
beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');

    DB::table('plugins')->updateOrInsert(
        ['name' => 'accounts'],
        ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
    );

    Package::$plugins = Plugin::all()->keyBy('name');

    AccountHelper::actingAsAdmin();

    Mail::fake();
});

function reminderInvoice(bool $overdue = true): Move
{
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE);

    AccountHelper::productLine($invoice, AccountHelper::account('income'), qty: 1, priceUnit: 80);

    $posted = AccountHelper::post($invoice);

    $posted->partner->forceFill(['email' => 'customer@example.com'])->save();

    if ($overdue) {
        $posted->forceFill(['invoice_date_due' => now()->subDays(2)->toDateString()])->save();
    }

    return $posted->refresh();
}

function reminderRefusal(Move $invoice): ValidationException
{
    try {
        app(MoveWorkflow::class)->deliverReminder($invoice);
    } catch (ValidationException $exception) {
        return $exception;
    }

    throw new RuntimeException('The reminder was sent.');
}

it('emails the invoice customer after the reminder is delivered', function () {
    $invoice = reminderInvoice();

    app(MoveWorkflow::class)->deliverReminder($invoice);

    Mail::assertSent(InvoiceEmail::class, function (InvoiceEmail $mail) use ($invoice): bool {
        return $mail->payload['to']['address'] === 'customer@example.com'
            && $mail->payload['from']['address'] === config('mail.from.address')
            && str_contains($mail->payload['subject'], (string) $invoice->name)
            && str_contains($mail->payload['description'], e((string) $invoice->name));
    });

    Mail::assertSentCount(1);
});

it('does not send the reminder until the transaction commits', function () {
    $invoice = reminderInvoice();

    DB::beginTransaction();

    try {
        app(MoveWorkflow::class)->sendReminder($invoice);

        Mail::assertNothingSent();
    } finally {
        DB::rollBack();
    }

    Mail::assertNothingSent();
});

it('emails the commercial customer and not another contact', function () {
    $invoice = reminderInvoice();
    $buyer = AccountHelper::partner();
    $buyer->forceFill(['email' => 'buyer@example.com'])->save();

    Move::withoutGlobalScopes()->whereKey($invoice->id)->update([
        'commercial_partner_id' => $buyer->id,
    ]);

    app(MoveWorkflow::class)->deliverReminder($invoice->refresh());

    Mail::assertSent(InvoiceEmail::class, fn (InvoiceEmail $mail): bool => $mail->payload['to']['address'] === 'buyer@example.com');
    Mail::assertNotSent(InvoiceEmail::class, fn (InvoiceEmail $mail): bool => $mail->payload['to']['address'] === 'customer@example.com');
});

it('refuses a customer with no email address', function () {
    $invoice = reminderInvoice();
    $invoice->partner->forceFill(['email' => null])->save();

    $exception = reminderRefusal($invoice->refresh());

    expect($exception->errors()['partners'][0])->toBe(__('accounts::filament/resources/invoice/actions/send-reminder.no-email'));

    Mail::assertNothingSent();
});

it('refuses an invoice that is not overdue', function () {
    $dueToday = reminderInvoice(overdue: false);

    $exception = reminderRefusal($dueToday);

    expect($exception->errors()['invoice'][0])->toBe(__('accounts::filament/resources/invoice/actions/send-reminder.not-overdue'));

    Mail::assertNothingSent();
});

it('refuses a paid invoice', function () {
    AccountHelper::bankJournal();

    $invoice = reminderInvoice();

    AccountHelper::pay($invoice);

    $exception = reminderRefusal($invoice->refresh());

    expect($invoice->payment_state)->toBe(PaymentState::PAID)
        ->and($exception->errors()['invoice'][0])->toBe(__('accounts::filament/resources/invoice/actions/send-reminder.not-overdue'));

    Mail::assertNothingSent();
});

it('refuses an invoice in a company the user cannot write to', function () {
    $invoice = reminderInvoice();

    FilamentHelper::actingAsCompanyUser(Company::factory()->create(), ['update_account_invoice']);
    app()->forgetInstance(CompanyContext::class);

    $exception = reminderRefusal($invoice);

    expect($exception->errors()['company'][0])->toBe(__('accounts::filament/resources/invoice/actions/send-reminder.company'));

    Mail::assertNothingSent();
});

it('shows Send reminder on an overdue invoice and hides it once paid', function () {
    $invoice = reminderInvoice();
    $paid = reminderInvoice();

    AccountHelper::bankJournal();
    AccountHelper::pay($paid);

    FilamentHelper::actingAs(['view_any_account_invoice', 'view_account_invoice', 'update_account_invoice']);

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    Livewire::test(ViewInvoice::class, ['record' => $invoice->id])
        ->assertOk()
        ->assertActionVisible(SendReminderAction::class)
        ->callAction(SendReminderAction::class);

    Livewire::test(EditInvoice::class, ['record' => $invoice->id])
        ->assertOk()
        ->assertActionVisible(SendReminderAction::class);

    Livewire::test(ViewInvoice::class, ['record' => $paid->id])
        ->assertOk()
        ->assertActionHidden(SendReminderAction::class);
});
