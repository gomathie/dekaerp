<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Webkul\Account\Services\AccountingSetupService;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/AccountHelper.php';

/**
 * TRACE365, 2026-11-19: no invoice could be marked paid. Accounting setup
 * copied the bank and cash journals without their payment method lines, and
 * a journal with none cannot register a payment.
 */
beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');

    DB::table('plugins')->updateOrInsert(
        ['name' => 'accounts'],
        ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
    );

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    AccountHelper::actingAsAdmin();
    AccountHelper::company();
});

/** @return array<string, int> journal type => method line count */
function paymentMethodLinesByJournalType(int $companyId): array
{
    return DB::table('accounts_journals')
        ->leftJoin('accounts_payment_method_lines', 'accounts_payment_method_lines.journal_id', '=', 'accounts_journals.id')
        ->where('accounts_journals.company_id', $companyId)
        ->whereIn('accounts_journals.type', ['bank', 'cash'])
        ->groupBy('accounts_journals.type')
        ->selectRaw('accounts_journals.type, count(accounts_payment_method_lines.id) as lines')
        ->pluck('lines', 'type')
        ->map(fn ($count) => (int) $count)
        ->all();
}

it('gives a newly set-up company payment methods on its bank and cash journals', function () {
    $company = Company::factory()->create();

    expect(app(AccountingSetupService::class)->setUp($company))->toBeTrue();

    $lines = paymentMethodLinesByJournalType($company->id);

    expect($lines['bank'] ?? 0)->toBeGreaterThan(0)
        ->and($lines['cash'] ?? 0)->toBeGreaterThan(0);
});

it('backfills manual payment in and out on bank and cash journals that have none, once', function () {
    $company = Company::factory()->create();
    app(AccountingSetupService::class)->setUp($company);

    $journalIds = DB::table('accounts_journals')->where('company_id', $company->id)->whereIn('type', ['bank', 'cash'])->pluck('id');
    DB::table('accounts_payment_method_lines')->whereIn('journal_id', $journalIds)->delete();

    $migration = require __DIR__.'/../../../database/migrations/2026_11_19_000001_backfill_payment_method_lines_for_bank_and_cash_journals.php';
    $migration->up();
    $migration->up();

    foreach ($journalIds as $journalId) {
        $types = DB::table('accounts_payment_method_lines')
            ->join('accounts_payment_methods', 'accounts_payment_methods.id', '=', 'accounts_payment_method_lines.payment_method_id')
            ->where('accounts_payment_method_lines.journal_id', $journalId)
            ->pluck('accounts_payment_methods.payment_type')
            ->sort()
            ->values()
            ->all();

        expect($types)->toBe(['inbound', 'outbound']);
    }
});
