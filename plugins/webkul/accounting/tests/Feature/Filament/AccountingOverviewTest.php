<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\BankStatement;
use Webkul\Account\Models\BankStatementLine;
use Webkul\Accounting\Filament\Clusters\Accounting\Pages\BankReconciliation as BankReconciliationPage;
use Webkul\Accounting\Filament\Clusters\Reporting\Pages\CashFlow;
use Webkul\Accounting\Filament\OverviewCard;
use Webkul\Accounting\Filament\Widgets\AccountingHeadlineWidget;
use Webkul\Accounting\Filament\Widgets\JournalChartsWidget;
use Webkul\Accounting\Services\BankReconciliation;
use Webkul\Invoice\Filament\Overview\OverviewRegistry;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/DashboardWidgetGuardHelper.php';
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

/**
 * Plan #9 R-4 and dashboard plan DB-8 (2026-11-25): "Cash at bank" counts
 * reconciled money only, says so, and opens the reconciliation page - but
 * only for someone who may open it.
 */
it('says cash at bank is reconciled money and links it to reconciliation only for someone who may reconcile', function () {
    FilamentHelper::actingAs(['page_accounting_overview', BankReconciliation::PERMISSION]);

    $cash = app(AccountingHeadlineWidget::class)->headlineStats([])[0];

    expect($cash->getLabel())->toBe(__('accounting::filament/overview-card.cash'))
        ->and((string) $cash->getDescription())->toBe(__('accounting::filament/overview-card.cash-hint'))
        ->and(OverviewCard::cashUrl())->toBe(BankReconciliationPage::getUrl());

    FilamentHelper::actingAs(['page_accounting_overview', 'page_accounting_cash_flow']);

    expect(BankReconciliationPage::canAccess())->toBeFalse()
        ->and(OverviewCard::cashUrl())->toBe(CashFlow::getUrl());

    FilamentHelper::actingAs(['page_accounting_overview']);

    expect(OverviewCard::cashUrl())->toBeNull();
});

/**
 * Dashboard plan DB-3 (2026-11-25, the module the 2026-11-23 sweep left
 * out): the Accounting overview's journal cards are absent without the
 * page permission or once Accounting is off for the company.
 */
it('shows the journal cards only with the overview permission and the module', function () {
    FilamentHelper::bootAdminPanel();

    $company = Company::factory()->create();

    expect(DashboardWidgetGuardHelper::visibility([JournalChartsWidget::class], 'accounting', $company, ['page_accounting_overview']))
        ->toBe(DashboardWidgetGuardHelper::guarded([JournalChartsWidget::class]));
});

it('contributes an Accounting section with its headline figures', function () {
    FilamentHelper::actingAs(['page_accounting_overview']);

    expect(OverviewRegistry::visible())->toHaveKey('accounting')
        ->and(OverviewRegistry::visible()['accounting']['headline'])->toBe(AccountingHeadlineWidget::class)
        ->and(app(AccountingHeadlineWidget::class)->headlineStats([]))->toHaveCount(3);
});
