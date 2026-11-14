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
 * Country chart templates (accounting plan A4, 2026-11-14): a country with a
 * template gets its chart and VAT on top of the standard copy, every other
 * country keeps the standard chart unchanged, and copied taxes keep their
 * distribution lines.
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

function countryChartCompany(?string $countryCode): Company
{
    $countryId = null;

    if ($countryCode !== null) {
        $countryId = DB::table('countries')->where('code', $countryCode)->value('id')
            ?? DB::table('countries')->insertGetId(['code' => $countryCode, 'name' => 'Test '.$countryCode, 'created_at' => now(), 'updated_at' => now()]);
    }

    $company = Company::factory()->create(['country_id' => $countryId]);

    expect(app(AccountingSetupService::class)->setUp($company))->toBeTrue();

    return $company;
}

/** @return array<string, string> code => name */
function countryChartAccounts(Company $company): array
{
    return DB::table('accounts_accounts')
        ->whereIn('id', DB::table('accounts_account_companies')->where('company_id', $company->id)->select('account_id'))
        ->pluck('name', 'code')
        ->all();
}

it('gives a Congo company the SYSCOHADA chart and 16 % VAT', function () {
    $company = countryChartCompany('CD');
    $accounts = countryChartAccounts($company);

    expect($accounts)->toHaveKey('411000', 'Clients')
        ->toHaveKey('401000', 'Fournisseurs')
        ->toHaveKey('521000', 'Banques locales')
        ->toHaveKey('101000', 'Capital social')
        ->toHaveKey('701000', 'Ventes de marchandises')
        ->not->toHaveKey('121000', 'Account Receivable');

    $rates = DB::table('accounts_taxes')->where('company_id', $company->id)->pluck('amount', 'name')->map(fn ($a) => (float) $a)->all();

    expect($rates)->toHaveKey('TVA 16 %', 16.0)
        ->toHaveKey('Exonéré 0 %', 0.0);
});

it('keeps journals pointing at the recoded accounts', function () {
    $company = countryChartCompany('CD');

    $codes = DB::table('accounts_journals')
        ->join('accounts_accounts', 'accounts_accounts.id', '=', 'accounts_journals.default_account_id')
        ->where('accounts_journals.company_id', $company->id)
        ->pluck('accounts_accounts.code')
        ->all();

    // Every journal account is this company's own, now under a SYSCOHADA code.
    expect($codes)->not->toBeEmpty()
        ->each->toMatch('/^[1-9]\d{5}$/');
    expect(array_intersect($codes, ['101401', '101501']))->toBeEmpty();
});

it('gives a Ghana company the statutory payables and 20 % VAT', function () {
    $company = countryChartCompany('GH');
    $accounts = countryChartAccounts($company);

    expect($accounts)->toHaveKey('230200', 'SSNIT Payable (Tier 1)')
        ->toHaveKey('230300', 'Tier 2 Pension Payable')
        ->toHaveKey('121000', 'Account Receivable');

    expect(DB::table('accounts_taxes')->where('company_id', $company->id)->where('amount', 20)->count())->toBeGreaterThan(0);
});

it('leaves a company from a country without a template on the standard chart', function () {
    $standard = countryChartAccounts(countryChartCompany(null));
    $kenya = countryChartAccounts(countryChartCompany('KE'));

    // Same accounts and names; row order from the database is not part of it.
    ksort($standard);
    ksort($kenya);

    expect($kenya)->toBe($standard)
        ->toHaveKey('121000', 'Account Receivable');
});

it('copies the distribution lines of every tax it copies', function () {
    $company = countryChartCompany('ZM');

    $taxIds = DB::table('accounts_taxes')->where('company_id', $company->id)->pluck('id');
    $withLines = DB::table('accounts_tax_partition_lines')->whereIn('tax_id', $taxIds)->distinct()->count('tax_id');

    expect($taxIds)->not->toBeEmpty()
        ->and($withLines)->toBe($taxIds->count());

    // No line may post to another company's account.
    $foreign = DB::table('accounts_tax_partition_lines')
        ->whereIn('tax_id', $taxIds)
        ->whereNotNull('account_id')
        ->whereNotIn('account_id', DB::table('accounts_account_companies')->where('company_id', $company->id)->select('account_id'))
        ->count();

    expect($foreign)->toBe(0);
});
