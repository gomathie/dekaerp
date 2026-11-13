<?php

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentStatus;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\BankStatement;
use Webkul\Account\Models\BankStatementLine;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\Payment;
use Webkul\Accounting\Exceptions\CannotReconcile;
use Webkul\Accounting\Filament\Clusters\Accounting\Pages\BankReconciliation as BankReconciliationPage;
use Webkul\Accounting\Services\BankReconciliation;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../accounts/tests/Helpers/AccountHelper.php';

/**
 * Bank reconciliation (accounting plan A3): import a CSV statement, match a
 * line to the payment it confirms (which turns the payment Paid through the
 * existing Reconciler), or book it to an account. Every refusal pins its
 * message.
 */
beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounting');

    DB::table('plugins')->whereIn('name', ['accounts', 'accounting'])->update([
        'is_installed' => true,
        'is_active'    => true,
        'updated_at'   => now(),
    ]);

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    FilamentHelper::actingAs([BankReconciliation::PERMISSION]);

    $this->service = app(BankReconciliation::class);
});

function bankCsv(string $contents): string
{
    $path = tempnam(sys_get_temp_dir(), 'bank-test-');

    file_put_contents($path, $contents);

    return $path;
}

function bankLine(Journal $journal, float $amount, array $overrides = []): BankStatementLine
{
    $statement = BankStatement::create([
        'company_id' => $journal->company_id,
        'journal_id' => $journal->id,
        'name'       => 'Test statement',
        'date'       => now()->toDateString(),
    ]);

    return BankStatementLine::create(array_merge([
        'statement_id'      => $statement->id,
        'journal_id'        => $journal->id,
        'currency_id'       => $journal->currency_id ?: $journal->company->currency_id,
        'date'              => now()->toDateString(),
        'amount'            => $amount,
        'payment_reference' => 'Line',
    ], $overrides));
}

function paidInvoicePayment(float $price = 500): Payment
{
    $move = AccountHelper::invoice(MoveType::OUT_INVOICE, AccountHelper::partner());
    AccountHelper::productLine($move, AccountHelper::account('income'), qty: 1, priceUnit: $price);
    AccountHelper::post($move);
    AccountHelper::pay($move);

    return Payment::query()->latest('id')->firstOrFail();
}

it('generates the page permission the bank reconciliation checks', function () {
    $generated = collect(FilamentShield::getPages())
        ->flatMap(fn (array $page): array => array_keys($page['permissions']))
        ->all();

    expect($generated)->toContain(BankReconciliation::PERMISSION);
});

it('renders the bank reconciliation page', function () {
    Livewire::test(BankReconciliationPage::class)->assertOk();
});

it('reads US thousands separators', function () {
    $statement = $this->service->import(AccountHelper::bankJournal(), bankCsv(
        "Date,Description,Amount\n30/11/2026,Customer A,\"1,234.50\"\n"
    ), ['date' => 0, 'label' => 1, 'amount' => 2, 'date_format' => 'd/m/Y']);

    expect((float) BankStatementLine::query()->where('statement_id', $statement->id)->value('amount'))->toBe(1234.5);
});

it('refuses a zero amount instead of importing nothing', function () {
    $this->service->import(AccountHelper::bankJournal(), bankCsv(
        "Date,Description,Amount\n30/11/2026,Customer A,10\n29/11/2026,Nothing,0\n"
    ), ['date' => 0, 'label' => 1, 'amount' => 2, 'date_format' => 'd/m/Y']);
})->throws(CannotReconcile::class, 'Line 3: the amount cannot be read.');

it('imports every line under the journal\'s company', function () {
    $journal = AccountHelper::bankJournal();

    $statement = $this->service->import($journal, bankCsv(
        "Date;Description;Amount\n30.11.2026;Customer A;1.234,50\n29.11.2026;Bank fee;-15,00\n"
    ), ['date' => 0, 'label' => 1, 'amount' => 2, 'date_format' => 'd.m.Y']);

    $lines = BankStatementLine::query()->where('statement_id', $statement->id)->orderBy('sort')->get();

    expect($lines)->toHaveCount(2)
        ->and((float) $lines[0]->amount)->toBe(1234.5)
        ->and((float) $lines[1]->amount)->toBe(-15.0)
        ->and($lines[0]->date->toDateString())->toBe('2026-11-30')
        ->and($lines->pluck('company_id')->unique()->all())->toBe([$journal->company_id])
        ->and($statement->company_id)->toBe($journal->company_id);
});

it('imports money in and money out columns', function () {
    $journal = AccountHelper::bankJournal();

    $statement = $this->service->import($journal, bankCsv(
        "Date,Label,In,Out\n2026-11-01,Deposit,200,\n2026-11-02,Rent,,75.25\n"
    ), ['date' => 0, 'label' => 1, 'money_in' => 2, 'money_out' => 3, 'date_format' => 'Y-m-d']);

    expect(BankStatementLine::query()->where('statement_id', $statement->id)->orderBy('sort')->pluck('amount')->map(fn ($a) => (float) $a)->all())
        ->toBe([200.0, -75.25]);
});

it('refuses a row whose date does not match the format', function () {
    $this->service->import(AccountHelper::bankJournal(), bankCsv("Date,Label,Amount\n2026-13-45,X,10\n"),
        ['date' => 0, 'label' => 1, 'amount' => 2, 'date_format' => 'd/m/Y']);
})->throws(CannotReconcile::class, 'Line 2: the date cannot be read.');

it('refuses a journal that is not a bank or cash journal', function () {
    $this->service->import(AccountHelper::saleJournal(), bankCsv("Date,Label,Amount\n2026-11-01,X,10\n"),
        ['date' => 0, 'label' => 1, 'amount' => 2, 'date_format' => 'Y-m-d']);
})->throws(CannotReconcile::class, 'Choose a bank or cash journal.');

it('matches a line to its payment and the payment becomes paid', function () {
    $payment = paidInvoicePayment(500);
    $journal = $payment->journal;

    $line = bankLine($journal, 500);

    expect($this->service->suggestions($line)->pluck('id')->all())->toContain($payment->id);

    $this->service->matchPayment($line, $payment);

    $line->refresh();
    $payment->refresh();

    expect($line->is_reconciled)->toBeTrue()
        ->and($line->payment_id)->toBe($payment->id)
        ->and($payment->state)->toBe(PaymentStatus::PAID);

    if ($line->move_id) {
        $move = Move::query()->findOrFail($line->move_id);
        $bankSide = $move->lines()->where('account_id', $journal->default_account_id)->firstOrFail();

        expect($move->state)->toBe(MoveState::POSTED)
            ->and((float) $bankSide->debit)->toBe(500.0);
    }
});

it('refuses a payment for a different amount', function () {
    $payment = paidInvoicePayment(500);

    $this->service->matchPayment(bankLine($payment->journal, 499), $payment);
})->throws(CannotReconcile::class, 'That payment does not match this line');

it('refuses to settle a line twice', function () {
    $payment = paidInvoicePayment(300);
    $line = bankLine($payment->journal, 300);

    $this->service->matchPayment($line, $payment);
    $this->service->matchPayment($line, $payment);
})->throws(CannotReconcile::class, 'This line is already reconciled.');

it('books a bank charge to an expense account', function () {
    $journal = AccountHelper::bankJournal();
    $expense = AccountHelper::account('expense');

    $line = $this->service->bookToAccount(bankLine($journal, -15), $expense, 'Monthly fee');

    $move = Move::query()->findOrFail($line->move_id);

    expect($line->is_reconciled)->toBeTrue()
        ->and($move->state)->toBe(MoveState::POSTED)
        ->and((float) $move->lines()->where('account_id', $expense->id)->value('debit'))->toBe(15.0)
        ->and((float) $move->lines()->where('account_id', $journal->default_account_id)->value('credit'))->toBe(15.0);
});

it('refuses to book a line to a receivable account', function () {
    $receivable = Account::factory()->create(['account_type' => AccountType::ASSET_RECEIVABLE, 'reconcile' => true]);

    $this->service->bookToAccount(bankLine(AccountHelper::bankJournal(), 40), $receivable);
})->throws(CannotReconcile::class, 'That account cannot be used here.');

it('refuses a user without the page permission', function () {
    $line = bankLine(AccountHelper::bankJournal(), 40);

    FilamentHelper::actingAs([]);

    $this->service->bookToAccount($line, AccountHelper::account('expense'));
})->throws(CannotReconcile::class, 'You cannot reconcile this bank account.');

it('does not reach another company\'s statement line', function () {
    $journal = AccountHelper::bankJournal();
    $other = Company::factory()->create();

    $line = bankLine($journal, 40);
    BankStatementLine::withoutGlobalScopes()->whereKey($line->id)->update(['company_id' => $other->id]);

    $this->service->bookToAccount($line, AccountHelper::account('expense'));
})->throws(CannotReconcile::class, 'You cannot reconcile this bank account.');
