<?php

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Journal;
use Webkul\Accounting\Filament\Clusters\Reporting\Pages\CashFlow;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../accounts/tests/Helpers/AccountHelper.php';
require_once __DIR__.'/../../Helpers/ReportHelper.php';

/**
 * Cash flow statement (accounting plan A6). Known figures: opening 100;
 * this month +1,000 from equity (financing), -300 on an expense
 * (operating), -200 on a fixed asset (investing); closing 600, which is
 * the bank account's balance.
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

    FilamentHelper::actingAs(['page_accounting_cash_flow']);

    $this->bank = AccountHelper::bankJournal();
});

function cashEntry(Journal $bank, float $cash, Account $counterpart, ?string $date = null): void
{
    $move = AccountHelper::journalEntry($bank, ['date' => $date ?? now()->toDateString()]);
    $bankAccount = Account::query()->findOrFail($bank->default_account_id);

    AccountHelper::entryLine($move, $bankAccount, max($cash, 0), max(-$cash, 0));
    AccountHelper::entryLine($move, $counterpart, max(-$cash, 0), max($cash, 0));

    AccountHelper::post($move);
}

function typedAccount(AccountType $type): Account
{
    return Account::factory()->create(['account_type' => $type]);
}

it('generates the page permission the cash flow checks', function () {
    $generated = collect(FilamentShield::getPages())
        ->flatMap(fn (array $page): array => array_keys($page['permissions']))
        ->all();

    expect($generated)->toContain('page_accounting_cash_flow');
});

it('renders the cash flow page', function () {
    Livewire::test(CashFlow::class)->assertOk();
});

it('classifies cash by its counterpart and ties opening to closing', function () {
    cashEntry($this->bank, 100, typedAccount(AccountType::INCOME), now()->subMonthNoOverflow()->startOfMonth()->toDateString());
    cashEntry($this->bank, 1000, typedAccount(AccountType::EQUITY));
    cashEntry($this->bank, -300, typedAccount(AccountType::EXPENSE));
    cashEntry($this->bank, -200, typedAccount(AccountType::ASSET_FIXED));

    $data = ReportHelper::data(CashFlow::class, 'cashFlowData');

    expect($data['opening'])->toBe(100.0)
        ->and($data['sections']['financing']['net'])->toBe(1000.0)
        ->and($data['sections']['operating']['out'])->toBe(300.0)
        ->and($data['sections']['investing']['out'])->toBe(200.0)
        ->and($data['net'])->toBe(500.0)
        ->and($data['closing'])->toBe(600.0);
});

it('leaves out a transfer between two cash accounts', function () {
    cashEntry($this->bank, 250, typedAccount(AccountType::ASSET_CASH));

    $data = ReportHelper::data(CashFlow::class, 'cashFlowData');

    expect($data['net'])->toBe(0.0)
        ->and(collect($data['sections'])->sum(fn (array $section): int => count($section['rows'])))->toBe(0);
});

it('puts each counterpart type in its section', function (AccountType $type, string $section) {
    expect(CashFlow::sectionFor($type->value))->toBe($section);
})->with([
    [AccountType::ASSET_RECEIVABLE, 'operating'],
    [AccountType::LIABILITY_PAYABLE, 'operating'],
    [AccountType::ASSET_CURRENT, 'operating'],
    [AccountType::ASSET_FIXED, 'investing'],
    [AccountType::ASSET_NON_CURRENT, 'investing'],
    [AccountType::EQUITY, 'financing'],
    [AccountType::LIABILITY_NON_CURRENT, 'financing'],
]);
