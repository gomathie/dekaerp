<?php

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentStatus;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\BankStatement;
use Webkul\Account\Models\BankStatementLine;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\LockDate;
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
require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
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

    // The default set-up parks payments in "outstanding receipts", so the
    // match posts the bank side.
    $move = Move::query()->findOrFail($line->move_id);
    $bankSide = $move->lines()->where('account_id', $journal->default_account_id)->firstOrFail();

    expect($move->state)->toBe(MoveState::POSTED)
        ->and((float) $bankSide->debit)->toBe(500.0);
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

/*
 * Plan #9 R-4 (user, 2026-11-25): clean reconciliation for cash, bank and
 * Mobile Money - a balance check per account, "Match all suggested", and the
 * column mapping remembered per journal.
 */

function journalCard(BankReconciliation $service, Journal $journal): ?array
{
    return collect($service->balances())->first(fn (array $card): bool => $card['journal']->id === $journal->id);
}

function otherCompanyBankJournal(): Journal
{
    $other = Company::factory()->create();

    return Journal::factory()->create([
        'type'               => JournalType::BANK,
        'company_id'         => $other->id,
        'currency_id'        => $other->currency_id,
        'default_account_id' => Account::factory()->create(['account_type' => AccountType::ASSET_CASH])->id,
    ]);
}

it('shows an account as clean when the statement and the books agree and nothing is open', function () {
    $journal = AccountHelper::bankJournal();

    $statement = $this->service->import($journal, bankCsv("Date,Description,Amount\n2026-11-02,Interest,200\n"),
        ['date' => 0, 'label' => 1, 'amount' => 2, 'date_format' => 'Y-m-d']);

    $line = BankStatementLine::query()->where('statement_id', $statement->id)->firstOrFail();
    $this->service->bookToAccount($line, AccountHelper::account('income'));

    $card = journalCard($this->service, $journal);

    expect($card['statement'])->toBe(200.0)
        ->and($card['book'])->toBe(200.0)
        ->and($card['difference'])->toBe(0.0)
        ->and($card['to_match'])->toBe(0)
        ->and($card['date'])->toBe('2026-11-02')
        ->and($card['state'])->toBe('clean');
});

it('counts the lines still to match and shows the difference they leave', function () {
    $journal = AccountHelper::bankJournal();

    $statement = $this->service->import($journal, bankCsv("Date,Description,Amount\n2026-11-02,Interest,200\n2026-11-03,Customer,50\n"),
        ['date' => 0, 'label' => 1, 'amount' => 2, 'date_format' => 'Y-m-d']);

    $first = BankStatementLine::query()->where('statement_id', $statement->id)->orderBy('sort')->firstOrFail();
    $this->service->bookToAccount($first, AccountHelper::account('income'));

    $card = journalCard($this->service, $journal);

    expect($card['statement'])->toBe(250.0)
        ->and($card['book'])->toBe(200.0)
        ->and($card['difference'])->toBe(50.0)
        ->and($card['to_match'])->toBe(1)
        ->and($card['state'])->toBe('to-match');
});

it('shows a cash journal without a statement as having none', function () {
    $cash = Journal::factory()->create([
        'type'               => JournalType::CASH,
        'company_id'         => AccountHelper::company()->id,
        'currency_id'        => AccountHelper::currency()->id,
        'default_account_id' => Account::factory()->create(['account_type' => AccountType::ASSET_CASH])->id,
    ]);

    $card = journalCard($this->service, $cash);

    expect($card['state'])->toBe('none')
        ->and($card['statement'])->toBeNull()
        ->and($card['difference'])->toBeNull()
        ->and($card['date'])->toBeNull();
});

it('never shows another company\'s bank or cash journals in the balance check', function () {
    $own = AccountHelper::bankJournal();
    $foreign = otherCompanyBankJournal();

    $companies = fn (): array => collect($this->service->balances())->map(fn (array $card): int => $card['journal']->company_id)->unique()->values()->all();

    expect($companies())->toBe([$own->company_id])
        ->and(journalCard($this->service, $foreign))->toBeNull();

    CompanyHelper::actingAsCompanyUser($own->company_id, [BankReconciliation::PERMISSION]);

    expect($companies())->toBe([$own->company_id])
        ->and(journalCard($this->service, $foreign))->toBeNull();
});

it('takes the closing balance from the balance column on the statement\'s last day', function () {
    // Newest first, as many banks export: the closing balance is the first row's.
    $statement = $this->service->import(AccountHelper::bankJournal(), bankCsv(
        "Date,Description,Amount,Balance\n2026-11-03,Fee,-20,180\n2026-11-02,Deposit,50,200\n2026-11-01,Deposit,100,150\n"
    ), ['date' => 0, 'label' => 1, 'amount' => 2, 'balance' => 3, 'date_format' => 'Y-m-d']);

    expect((float) $statement->balance_end_real)->toBe(180.0)
        ->and((float) $statement->balance_start)->toBe(50.0)
        ->and(BankStatementLine::query()->where('statement_id', $statement->id)->count())->toBe(3);
});

it('prefers the closing balance typed from the statement', function () {
    $statement = $this->service->import(AccountHelper::bankJournal(), bankCsv(
        "Date,Description,Amount,Balance\n2026-11-01,Deposit,100,150\n"
    ), ['date' => 0, 'label' => 1, 'amount' => 2, 'balance' => 3, 'date_format' => 'Y-m-d'], null, 999.0);

    expect((float) $statement->balance_end_real)->toBe(999.0);
});

it('refuses a balance that cannot be read', function () {
    $this->service->import(AccountHelper::bankJournal(), bankCsv(
        "Date,Description,Amount,Balance\n2026-11-01,Deposit,100,abc\n"
    ), ['date' => 0, 'label' => 1, 'amount' => 2, 'balance' => 3, 'date_format' => 'Y-m-d']);
})->throws(CannotReconcile::class, 'Line 2: the balance cannot be read.');

it('continues a statement without a closing balance from the previous one', function () {
    $journal = AccountHelper::bankJournal();
    $map = ['date' => 0, 'label' => 1, 'amount' => 2, 'date_format' => 'Y-m-d'];

    $this->service->import($journal, bankCsv("Date,Description,Amount\n2026-11-01,Deposit,100\n"), $map);
    $second = $this->service->import($journal, bankCsv("Date,Description,Amount\n2026-11-05,Fee,-30\n"), $map);

    expect((float) $second->balance_start)->toBe(100.0)
        ->and((float) $second->balance_end_real)->toBe(70.0)
        ->and(journalCard($this->service, $journal)['statement'])->toBe(70.0)
        ->and(journalCard($this->service, $journal)['date'])->toBe('2026-11-05');
});

it('chains the balances of statements imported before the balance check', function () {
    $journal = AccountHelper::bankJournal();

    $older = bankLine($journal, 100, ['date' => '2026-10-01']);
    $newer = bankLine($journal, -40, ['date' => '2026-10-15']);
    BankStatement::query()->whereKey($older->statement_id)->update(['date' => '2026-10-01', 'balance_start' => 0, 'balance_end' => 100, 'balance_end_real' => 0]);
    BankStatement::query()->whereKey($newer->statement_id)->update(['date' => '2026-10-15', 'balance_start' => 0, 'balance_end' => -40, 'balance_end_real' => 0]);

    $migration = require __DIR__.'/../../../../accounts/database/migrations/2026_11_25_100001_add_statement_import_columns_to_accounts_journals.php';
    $migration->up();

    $statement = BankStatement::query()->findOrFail($newer->statement_id);

    expect((float) $statement->balance_start)->toBe(100.0)
        ->and((float) $statement->balance_end_real)->toBe(60.0);

    // A second run changes nothing.
    $migration->up();

    expect((float) BankStatement::query()->findOrFail($newer->statement_id)->balance_end_real)->toBe(60.0);
});

it('matches all and only the lines with exactly one suggested payment of the same amount', function () {
    $single = paidInvoicePayment(500);
    $journal = $single->journal;

    // Two payments of 300 in the same bank account: which one is a
    // person's choice, so the 300 line stays.
    $first = paidInvoicePayment(300);
    $second = paidInvoicePayment(300);
    Payment::query()->whereKey([$first->id, $second->id])->update(['journal_id' => $journal->id]);

    $matchable = bankLine($journal, 500);
    $ambiguous = bankLine($journal, 300);
    $unmatched = bankLine($journal, 777);

    expect($this->service->singleSuggestions()->map(fn (array $pair): array => [$pair['line']->id, $pair['payment']->id])->all())
        ->toBe([[$matchable->id, $single->id]]);

    expect($this->service->matchAllSuggested())->toBe(['matched' => 1, 'left' => 0])
        ->and($matchable->refresh()->is_reconciled)->toBeTrue()
        ->and($matchable->payment_id)->toBe($single->id)
        ->and($single->refresh()->state)->toBe(PaymentStatus::PAID)
        ->and($ambiguous->refresh()->is_reconciled)->toBeFalse()
        ->and($unmatched->refresh()->is_reconciled)->toBeFalse();
});

it('leaves out a payment that is the only suggestion of two lines', function () {
    $payment = paidInvoicePayment(250);

    bankLine($payment->journal, 250);
    bankLine($payment->journal, 250);

    expect($this->service->singleSuggestions())->toHaveCount(0)
        ->and($this->service->matchAllSuggested())->toBe(['matched' => 0, 'left' => 0]);
});

it('leaves a line dated inside a locked period open when matching all', function () {
    $payment = paidInvoicePayment(500);
    $line = bankLine($payment->journal, 500, ['date' => now()->subDays(10)->toDateString()]);

    LockDate::query()->create(['company_id' => $line->company_id, 'lock_date' => now()->subDays(5)->toDateString()]);

    expect($this->service->matchAllSuggested())->toBe(['matched' => 0, 'left' => 1])
        ->and($line->refresh()->is_reconciled)->toBeFalse()
        ->and($line->payment_id)->toBeNull()
        // Nothing was posted into the locked period: the refused match rolled back.
        ->and(Move::query()->where('statement_line_id', $line->id)->exists())->toBeFalse();
});

it('matches all suggested lines from the page after confirmation', function () {
    $payment = paidInvoicePayment(420);
    $line = bankLine($payment->journal, 420);

    Livewire::test(BankReconciliationPage::class)
        ->assertSet('suggestedCount', 1)
        ->assertActionVisible('matchAll')
        ->callAction('matchAll')
        ->assertSet('suggestedCount', 0);

    expect($line->refresh()->is_reconciled)->toBeTrue();
});

it('shows the balance check above the statement lines', function () {
    $journal = AccountHelper::bankJournal();

    Livewire::test(BankReconciliationPage::class)
        ->assertOk()
        ->assertSee($journal->name)
        ->assertSee(__('accounting::filament/clusters/accounting/pages/bank-reconciliation.balances.state.none'));
});

it('remembers each journal\'s columns and finds a moved column by its heading', function () {
    $journal = AccountHelper::bankJournal();
    $map = ['date' => 0, 'label' => 1, 'amount' => 2, 'date_format' => 'Y-m-d'];

    $this->service->import($journal, bankCsv("Date,Description,Amount\n2026-11-01,First,10\n"), $map);

    expect($this->service->rememberedColumns($journal))->toBe([
        'columns'     => ['date' => 0, 'label' => 1, 'amount' => 2],
        'headers'     => ['date' => 'Date', 'label' => 'Description', 'amount' => 'Amount'],
        'date_format' => 'Y-m-d',
        'has_header'  => true,
    ])->and($this->service->rememberedColumns(AccountHelper::bankJournal()))->toBeNull();

    // The bank moved its columns; the remembered numbers still find them.
    $statement = $this->service->import($journal, bankCsv("Amount,Date,Description\n15,2026-11-02,Second\n"), $map);

    $line = BankStatementLine::query()->where('statement_id', $statement->id)->firstOrFail();

    expect((float) $line->amount)->toBe(15.0)
        ->and($line->date->toDateString())->toBe('2026-11-02')
        ->and($line->payment_reference)->toBe('Second')
        ->and($this->service->rememberedColumns($journal)['columns'])->toBe(['date' => 1, 'label' => 2, 'amount' => 0]);
});

it('neither reads nor writes another company\'s remembered columns', function () {
    $foreign = otherCompanyBankJournal();
    $stored = ['columns' => ['date' => 4, 'label' => 5, 'amount' => 6], 'headers' => [], 'date_format' => 'd/m/Y', 'has_header' => true];
    $foreign->forceFill(['statement_import_columns' => $stored])->saveQuietly();

    CompanyHelper::actingAsCompanyUser(AccountHelper::company(), [BankReconciliation::PERMISSION]);

    expect(fn () => $this->service->rememberedColumns($foreign->id))
        ->toThrow(CannotReconcile::class, 'You cannot reconcile this bank account.')
        ->and(fn () => $this->service->import($foreign, bankCsv("Date,Description,Amount\n2026-11-01,X,10\n"), ['date' => 0, 'label' => 1, 'amount' => 2, 'date_format' => 'Y-m-d']))
        ->toThrow(CannotReconcile::class, 'You cannot reconcile this bank account.');

    expect(Journal::withoutGlobalScopes()->findOrFail($foreign->id)->statement_import_columns)->toBe($stored);
});
