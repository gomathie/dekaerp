<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Webkul\Account\Enums\AmountType;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\TaxIncludeOverride;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Exceptions\PeriodLockedException;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Expense;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\LockDate;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\Partner;
use Webkul\Account\Models\Tax;
use Webkul\Account\Services\ExpenseEntry;
use Webkul\Account\Services\ExpenseRecorder;
use Webkul\PluginManager\Exceptions\PluginNotEntitledException;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../accounts/tests/Helpers/AccountHelper.php';

/**
 * Plan #10 Z1/Z2 (user, 2026-11-26): an expense posts one balanced entry in
 * the bank or cash journal it was paid from, at once. Editing rewrites it,
 * deleting cancels it, a locked period refuses both, and nothing of another
 * company can be named. Only Expenses may put an entry in a bank journal.
 */
beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('invoices');
    PluginAccess::flush();

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    AccountHelper::actingAsAdmin();

    $this->company = AccountHelper::company();
    $this->bank = AccountHelper::bankJournal();
    $this->expenseAccount = AccountHelper::account('expense');
    $this->recorder = app(ExpenseRecorder::class);
});

/**
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function expenseData(array $overrides = []): array
{
    return array_merge([
        'date'               => now()->toDateString(),
        'amount'             => 350,
        'currency_id'        => test()->company->currency_id,
        'expense_account_id' => test()->expenseAccount->id,
        'journal_id'         => test()->bank->id,
        'reference'          => 'Receipt 4471',
        'note'               => 'Trip to a client site',
    ], $overrides);
}

function expenseMove(Expense $expense): Move
{
    return Move::withoutGlobalScopes()->with('lines')->findOrFail($expense->move_id);
}

function purchaseTax(float $rate, TaxIncludeOverride $include): Tax
{
    return AccountHelper::taxWithAccounts($rate, AmountType::PERCENT, TypeTaxUse::PURCHASE, $include);
}

function otherExpenseCompany(): Company
{
    return CompanyHelper::asNobody(fn () => Company::factory()->create());
}

it('posts one balanced entry in the bank journal it was paid from', function () {
    $expense = $this->recorder->create(expenseData(), $this->company->id);

    $move = expenseMove($expense);
    $debit = $move->lines->firstWhere('account_id', $this->expenseAccount->id);
    $credit = $move->lines->firstWhere('account_id', $this->bank->default_account_id);

    expect($move->state)->toBe(MoveState::POSTED)
        ->and($move->journal_id)->toBe($this->bank->id)
        ->and($move->lines)->toHaveCount(2)
        ->and((float) $debit->debit)->toBe(350.0)
        ->and((float) $credit->credit)->toBe(350.0)
        ->and($debit->display_type)->toBe(DisplayType::PRODUCT)
        ->and((float) $move->lines->sum('debit'))->toBe((float) $move->lines->sum('credit'))
        ->and((float) $expense->amount_total_company)->toBe(350.0);
});

it('takes an included tax out of the amount paid', function () {
    $tax = purchaseTax(15, TaxIncludeOverride::TAX_INCLUDED);

    $expense = $this->recorder->create(expenseData(['amount' => 402.50, 'tax_id' => $tax->id]), $this->company->id);

    $move = expenseMove($expense);
    $taxLine = $move->lines->firstWhere('display_type', DisplayType::TAX);

    expect((float) $move->lines->firstWhere('account_id', $this->expenseAccount->id)->debit)->toBe(350.0)
        ->and((float) $taxLine->debit)->toBe(52.5)
        ->and($taxLine->tax_line_id)->toBe($tax->id)
        ->and((float) $taxLine->tax_base_amount)->toBe(350.0)
        ->and((float) $move->lines->firstWhere('account_id', $this->bank->default_account_id)->credit)->toBe(402.5)
        ->and((float) $expense->amount_tax)->toBe(52.5)
        ->and((float) $expense->amount_total)->toBe(402.5);
});

it('adds an excluded tax on top of the amount', function () {
    $tax = purchaseTax(10, TaxIncludeOverride::TAX_EXCLUDED);

    $expense = $this->recorder->create(expenseData(['amount' => 100, 'tax_id' => $tax->id]), $this->company->id);

    $move = expenseMove($expense);

    expect((float) $move->lines->firstWhere('account_id', $this->expenseAccount->id)->debit)->toBe(100.0)
        ->and((float) $move->lines->firstWhere('display_type', DisplayType::TAX)->debit)->toBe(10.0)
        ->and((float) $move->lines->firstWhere('account_id', $this->bank->default_account_id)->credit)->toBe(110.0);
});

it('books another currency at the day\'s rate, like an invoice', function () {
    $foreign = Currency::query()->where('id', '!=', $this->company->currency_id)->firstOrFail();
    $foreign->forceFill(['active' => true])->save();
    $foreign->rates()->delete();
    AccountHelper::setCurrencyRate($foreign, 2.0);

    $expense = $this->recorder->create(expenseData(['amount' => 100, 'currency_id' => $foreign->id]), $this->company->id);

    $move = expenseMove($expense);
    $debit = $move->lines->firstWhere('account_id', $this->expenseAccount->id);
    $credit = $move->lines->firstWhere('account_id', $this->bank->default_account_id);

    expect($move->currency_id)->toBe($foreign->id)
        ->and((float) $debit->debit)->toBe(50.0)
        ->and((float) $debit->amount_currency)->toBe(100.0)
        ->and((float) $credit->credit)->toBe(50.0)
        ->and((float) $credit->amount_currency)->toBe(-100.0)
        ->and((float) $expense->currency_rate)->toBe(2.0)
        ->and((float) $expense->amount_total_company)->toBe(50.0);
});

it('refuses a currency with no rate rather than booking it one to one', function () {
    $foreign = Currency::query()->where('id', '!=', $this->company->currency_id)->firstOrFail();
    $foreign->forceFill(['active' => true])->save();
    $foreign->rates()->delete();

    expect(fn () => $this->recorder->create(expenseData(['currency_id' => $foreign->id]), $this->company->id))
        ->toThrow(ValidationException::class, "There is no exchange rate for {$foreign->name} on this date.");

    expect(Expense::query()->count())->toBe(0);
});

it('rewrites the same entry when the expense is edited', function () {
    $expense = $this->recorder->create(expenseData(), $this->company->id);
    $moveId = $expense->move_id;
    $number = $expense->number;

    $expense = $this->recorder->update($expense, expenseData(['amount' => 200]), 'Typed the wrong amount');

    $move = expenseMove($expense);

    expect($expense->move_id)->toBe($moveId)
        ->and($expense->number)->toBe($number)
        ->and($move->state)->toBe(MoveState::POSTED)
        ->and($move->journal_id)->toBe($this->bank->id)
        ->and($move->lines)->toHaveCount(2)
        ->and((float) $move->lines->firstWhere('account_id', $this->expenseAccount->id)->debit)->toBe(200.0)
        ->and((float) $move->lines->firstWhere('account_id', $this->bank->default_account_id)->credit)->toBe(200.0);
});

it('moves the entry to the other journal when the expense is paid from another account', function () {
    $expense = $this->recorder->create(expenseData(), $this->company->id);
    $cash = AccountHelper::bankJournal();
    $cash->forceFill(['type' => JournalType::CASH])->save();

    $expense = $this->recorder->update($expense, expenseData(['journal_id' => $cash->id]), 'Paid from petty cash');

    $move = expenseMove($expense);

    expect($move->journal_id)->toBe($cash->id)
        ->and($move->name)->toStartWith($cash->code)
        ->and($move->lines->firstWhere('account_id', $cash->default_account_id))->not->toBeNull();
});

it('refuses to change an expense without a reason', function () {
    $expense = $this->recorder->create(expenseData(), $this->company->id);

    expect(fn () => $this->recorder->update($expense, expenseData(['amount' => 1]), '  '))
        ->toThrow(ValidationException::class, 'Give a reason: this expense is already in the books.');

    expect((float) $expense->refresh()->amount)->toBe(350.0);
});

it('cancels the entry when the expense is deleted', function () {
    $expense = $this->recorder->create(expenseData(), $this->company->id);

    $this->recorder->delete($expense, 'Recorded twice');

    expect(expenseMove($expense)->state)->toBe(MoveState::CANCEL)
        ->and(Expense::query()->find($expense->id))->toBeNull()
        ->and(Expense::withTrashed()->find($expense->id))->not->toBeNull();
});

it('refuses an expense dated in a locked period and leaves nothing behind', function () {
    LockDate::query()->create(['company_id' => $this->company->id, 'lock_date' => now()->subDay()->toDateString()]);

    expect(fn () => $this->recorder->create(expenseData(['date' => now()->subDays(3)->toDateString()]), $this->company->id))
        ->toThrow(PeriodLockedException::class);

    expect(Expense::query()->count())->toBe(0);
});

it('refuses to edit or delete an expense once its period is locked', function () {
    $expense = $this->recorder->create(expenseData(['date' => now()->subDays(3)->toDateString()]), $this->company->id);

    LockDate::query()->create(['company_id' => $this->company->id, 'lock_date' => now()->subDay()->toDateString()]);

    expect(fn () => $this->recorder->update($expense, expenseData(['amount' => 10]), 'Correction'))
        ->toThrow(PeriodLockedException::class)
        ->and(fn () => $this->recorder->delete($expense, 'Correction'))
        ->toThrow(PeriodLockedException::class);

    expect((float) $expense->refresh()->amount)->toBe(350.0)
        ->and(expenseMove($expense)->state)->toBe(MoveState::POSTED);
});

it('refuses a journal, account, tax or vendor of another company', function (string $field, Closure $foreign, string $message) {
    $other = otherExpenseCompany();

    $data = expenseData([$field => $foreign($other)->id]);

    expect(fn () => $this->recorder->create($data, $this->company->id))
        ->toThrow(ValidationException::class, $message);

    expect(Expense::query()->count())->toBe(0);
})->with([
    'journal' => ['journal_id', fn (Company $other) => CompanyHelper::asNobody(fn () => Journal::factory()->bank()->create([
        'company_id'         => $other->id,
        'default_account_id' => Account::factory()->create()->id,
    ])), 'Choose a bank or cash account of this company.'],
    'account' => ['expense_account_id', function (Company $other) {
        $account = AccountHelper::account('expense');
        $account->companies()->sync([$other->id]);

        return $account;
    }, 'Choose an expense account of this company.'],
    'tax' => ['tax_id', fn (Company $other) => CompanyHelper::asNobody(fn () => Tax::factory()->create([
        'company_id'   => $other->id,
        'type_tax_use' => TypeTaxUse::PURCHASE,
    ])), 'Choose a purchase tax of this company.'],
    'vendor' => ['partner_id', fn (Company $other) => CompanyHelper::asNobody(fn () => Partner::factory()->create([
        'company_id' => $other->id,
    ])), 'Choose a vendor of this company.'],
]);

it('refuses an income account or a general journal', function () {
    expect(fn () => $this->recorder->create(expenseData(['expense_account_id' => AccountHelper::account('income')->id]), $this->company->id))
        ->toThrow(ValidationException::class, 'Choose an expense account of this company.')
        ->and(fn () => $this->recorder->create(expenseData(['journal_id' => AccountHelper::generalJournal()->id]), $this->company->id))
        ->toThrow(ValidationException::class, 'Choose a bank or cash account of this company.');
});

it('does not find another company\'s expense for a user of this company', function () {
    $expense = $this->recorder->create(expenseData(), $this->company->id);

    $own = CompanyHelper::asNobody(fn () => Company::factory()->create());
    CompanyHelper::actingAsCompanyUser($own, ['update_invoice_expense', 'delete_invoice_expense']);

    expect(fn () => $this->recorder->update($expense, expenseData(['amount' => 1]), 'Not mine'))
        ->toThrow(ModelNotFoundException::class)
        ->and(fn () => $this->recorder->delete($expense, 'Not mine'))
        ->toThrow(ModelNotFoundException::class);

    expect((float) $expense->refresh()->amount)->toBe(350.0);
});

it('refuses an expense for a company without the invoices module', function () {
    DB::table('company_plugin')
        ->where('company_id', $this->company->id)
        ->where('plugin_id', Plugin::query()->where('name', 'invoices')->value('id'))
        ->delete();

    PluginAccess::flush();

    expect(fn () => $this->recorder->create(expenseData(), $this->company->id))
        ->toThrow(PluginNotEntitledException::class);

    expect(Expense::query()->count())->toBe(0);
});

it('numbers expenses per company and per year', function () {
    $first = $this->recorder->create(expenseData(), $this->company->id);
    $second = $this->recorder->create(expenseData(), $this->company->id);

    $year = now()->format('Y');

    expect($first->number)->toBe("EXP/{$year}/00001")
        ->and($second->number)->toBe("EXP/{$year}/00002");

    // The other company's expense is written as a job would, with nobody
    // signed in: the admin here works in the first company only.
    $theirs = CompanyHelper::asNobody(function () {
        $other = Company::factory()->create();
        $otherBank = Journal::factory()->bank()->create([
            'company_id'         => $other->id,
            'default_account_id' => Account::factory()->create(['account_type' => 'asset_cash'])->id,
        ]);
        $vendor = Partner::factory()->create(['company_id' => $other->id]);

        return $this->recorder->create(expenseData([
            'journal_id'  => $otherBank->id,
            'currency_id' => $other->currency_id,
            'partner_id'  => $vendor->id,
        ]), $other->id);
    });

    expect($theirs->number)->toBe("EXP/{$year}/00001");
});

it('refuses a receipt path outside the company receipt folder', function () {
    expect(fn () => $this->recorder->create(expenseData(['receipt_path' => 'accounts/expenses/../../secret.pdf']), $this->company->id))
        ->toThrow(ValidationException::class, 'The receipt file could not be accepted. Upload it again.')
        ->and(fn () => $this->recorder->create(expenseData(['receipt_path' => 'logistics/expenses/other.pdf']), $this->company->id))
        ->toThrow(ValidationException::class, 'The receipt file could not be accepted. Upload it again.');

    expect(ExpenseRecorder::isReceiptPath('accounts/expenses/'.str_repeat('a', 40).'.pdf'))->toBeTrue()
        ->and(ExpenseRecorder::isReceiptPath('accounts/expenses/'.str_repeat('a', 40).'.php'))->toBeFalse()
        ->and(ExpenseRecorder::isReceiptPath('accounts/expenses/sub/'.str_repeat('a', 40).'.pdf'))->toBeFalse();
});

it('stores a receipt by its content, never by the name it was sent with', function () {
    Storage::fake('public');

    $script = UploadedFile::fake()->createWithContent('receipt.jpg', '<?php echo "x";');

    expect(fn () => $this->recorder->storeReceipt($script, $this->company->id))
        ->toThrow(ValidationException::class, 'The receipt file could not be accepted. Upload it again.');

    $path = $this->recorder->storeReceipt(UploadedFile::fake()->createWithContent('scan.php', "%PDF-1.4\n%%EOF"), $this->company->id);

    expect($path)->toEndWith('.pdf')
        ->and(ExpenseRecorder::isReceiptPath($path))->toBeTrue();

    Storage::disk('public')->assertExists($path);
});

it('keeps a hand-made entry out of a bank journal', function () {
    AccountHelper::generalJournal();

    $manual = AccountHelper::journalEntry($this->bank);

    expect($manual->refresh()->journal->type)->toBe(JournalType::GENERAL);
});

it('keeps an entry built without an expense out of a bank journal', function () {
    AccountHelper::generalJournal();

    $move = app(ExpenseEntry::class)->post(
        companyId: $this->company->id,
        currencyId: $this->company->currency_id,
        journalId: $this->bank->id,
        date: now()->toDateString(),
        lines: [['account_id' => $this->expenseAccount->id, 'amount' => 25, 'name' => 'Import']],
        paidFromAccountId: (int) $this->bank->default_account_id,
        paidFromLabel: 'Bank',
    );

    expect($move->journal->type)->toBe(JournalType::GENERAL)
        ->and($move->journal_id)->not->toBe($this->bank->id)
        ->and($move->state)->toBe(MoveState::POSTED);
});

it('keeps an expense entry in its bank journal through reset to draft and cancel', function () {
    $expense = $this->recorder->create(expenseData(), $this->company->id);

    $move = AccountHelper::resetToDraft(Move::withoutGlobalScopes()->findOrFail($expense->move_id));

    expect($move->refresh()->journal_id)->toBe($this->bank->id);

    $move = AccountHelper::cancel($move);

    expect($move->refresh()->journal_id)->toBe($this->bank->id);
});
