<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\BankStatement;
use Webkul\Account\Models\BankStatementLine;
use Webkul\Accounting\Filament\OverviewCard;
use Webkul\Accounting\Filament\Widgets\AccountingHeadlineWidget;
use Webkul\Invoice\Filament\Overview\OverviewRegistry;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../accounts/tests/Helpers/AccountHelper.php';

/**
 * Accounting on the combined Overview (accounting plan A5): cash at bank,
 * bank lines to reconcile and draft entries, shown only to users who may
 * open the Accounting overview.
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
});

it('computes cash at bank, lines to reconcile and draft entries', function () {
    FilamentHelper::actingAs(['page_accounting_overview']);

    $bank = AccountHelper::bankJournal();
    $bankAccount = Account::query()->findOrFail($bank->default_account_id);

    $posted = AccountHelper::journalEntry($bank);
    AccountHelper::entryLine($posted, $bankAccount, 750, 0);
    AccountHelper::entryLine($posted, Account::factory()->create(['account_type' => AccountType::EQUITY]), 0, 750);
    AccountHelper::post($posted);

    $before = OverviewCard::figures()['drafts'];
    AccountHelper::journalEntry($bank);

    $statement = BankStatement::create(['company_id' => $bank->company_id, 'journal_id' => $bank->id, 'name' => 'S', 'date' => now()->toDateString()]);
    BankStatementLine::create(['statement_id' => $statement->id, 'journal_id' => $bank->id, 'date' => now()->toDateString(), 'amount' => 20]);

    $figures = OverviewCard::figures();

    expect($figures['cash'])->toBeGreaterThanOrEqual(750.0)
        ->and($figures['to_reconcile'])->toBeGreaterThanOrEqual(1)
        ->and($figures['drafts'])->toBe($before + 1)
        ->and(OverviewCard::attention()[0]['count'])->toBe($figures['to_reconcile']);
});

it('shows the card and the section only to users who may open the accounting overview', function () {
    FilamentHelper::actingAs([]);

    // OverviewCard::visible() is exactly the card's registered visibility.
    // Not DashboardCardRegistry::visible(): that runs every module's card,
    // and Inventory's queries its tables, absent from this suite.
    expect(OverviewCard::visible())->toBeFalse();

    FilamentHelper::actingAs(['page_accounting_overview']);

    expect(OverviewCard::visible())->toBeTrue()
        ->and(OverviewCard::data()['items'])->toHaveCount(3);
});

it('contributes an Accounting section with its headline figures', function () {
    FilamentHelper::actingAs(['page_accounting_overview']);

    expect(OverviewRegistry::visible())->toHaveKey('accounting')
        ->and(OverviewRegistry::visible()['accounting']['headline'])->toBe(AccountingHeadlineWidget::class)
        ->and(app(AccountingHeadlineWidget::class)->headlineStats([]))->toHaveCount(3);
});
