<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Webkul\Accounting\Filament\Clusters\Reporting\Pages\AgedPayable;
use Webkul\Accounting\Filament\Clusters\Reporting\Pages\AgedReceivable;
use Webkul\Accounting\Filament\Clusters\Reporting\Pages\BalanceSheet;
use Webkul\Accounting\Filament\Clusters\Reporting\Pages\GeneralLedger;
use Webkul\Accounting\Filament\Clusters\Reporting\Pages\PartnerLedger;
use Webkul\Accounting\Filament\Clusters\Reporting\Pages\ProfitLoss;
use Webkul\Accounting\Filament\Clusters\Reporting\Pages\TrialBalance;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../accounts/tests/Helpers/AccountHelper.php';
require_once __DIR__.'/../../Helpers/ReportHelper.php';

/**
 * Accounting plan, A1 (2026-11-12): the report pages build raw joins over
 * the ledger rather than reading scoped models, so CompanyScope does not
 * protect them. Each one filters on the active companies itself. These tests
 * pin that a company's reports never contain another company's postings,
 * because clients are unrelated businesses sharing one installation.
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

dataset('report pages', [
    'trial balance'   => [TrialBalance::class, 'trialBalanceData', 'page_accounting_trial_balance'],
    'general ledger'  => [GeneralLedger::class, 'generalLedgerData', 'page_accounting_general_ledger'],
    'partner ledger'  => [PartnerLedger::class, 'partnerLedgerData', 'page_accounting_partner_ledger'],
    'profit and loss' => [ProfitLoss::class, 'profitLossData', 'page_accounting_profit_loss'],
    'balance sheet'   => [BalanceSheet::class, 'balanceSheetData', 'page_accounting_balance_sheet'],
    'aged receivable' => [AgedReceivable::class, 'agedReceivableData', 'page_accounting_aged_receivable'],
    'aged payable'    => [AgedPayable::class, 'agedPayableData', 'page_accounting_aged_payable'],
]);

it('never shows another company\'s postings', function (string $page, string $method, string $permission) {
    // AccountHelper files its journals under the first company.
    $companyA = AccountHelper::company();
    $companyB = CompanyHelper::company();

    // A figure no default fixture produces, so finding it anywhere in the
    // report's data can only mean company A's documents leaked in.
    FilamentHelper::actingAsCompanyUser($companyA, [$permission]);

    $today = now()->toDateString();

    ReportHelper::postedSale($today, 1, 4321.87, AccountHelper::account('income'), AccountHelper::partner());
    ReportHelper::postedBill($today, 1, 4321.87, AccountHelper::account('expense'), AccountHelper::partner());

    $ownReport = json_encode(ReportHelper::data($page, $method));

    FilamentHelper::actingAsCompanyUser($companyB, [$permission]);

    $otherReport = json_encode(ReportHelper::data($page, $method));

    expect($ownReport)->toContain('4321.87')
        ->and($otherReport)->not->toContain('4321.87');
})->with('report pages');
