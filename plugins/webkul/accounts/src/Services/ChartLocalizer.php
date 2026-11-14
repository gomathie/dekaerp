<?php

namespace Webkul\Account\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Webkul\Account\Enums\AmountType;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Support\Models\Company;

/**
 * Country chart templates (accounting plan A4, 2026-11-14; user: no local
 * accountant yet, so follow the published standards an accountant already
 * knows: IFRS-style for Ghana and Zambia, SYSCOHADA for the OHADA states).
 *
 * Runs once, inside AccountingSetupService::setUp(), after the standard chart
 * has been copied. It renames and recodes the copied accounts **in place**,
 * so every journal, setting and tax that points at one keeps pointing at it,
 * adds the country's extra accounts, and sets the standard VAT rate.
 *
 * A company whose country has no template, or no country at all, is left
 * exactly as the copy made it: every country can use the system (user,
 * 2026-11-14). Companies set up before A4 are never touched.
 *
 * Templates live in resources/data/charts/*.php. Their tax rates are
 * starting values; each company's users maintain their own (user,
 * 2026-11-14).
 */
class ChartLocalizer
{
    /**
     * @return array{file: string, name: string, countries: list<string>, accounts: array<string, array{0: string, 1: string}>, extra_accounts: list<array{0: string, 1: string, 2: string, 3: bool}>, taxes: array<string, array{0: string, 1: int|float}>, exempt_tax: string}|null
     */
    public function templateFor(?string $countryCode): ?array
    {
        if ($countryCode === null || $countryCode === '') {
            return null;
        }

        foreach (glob(dirname(__DIR__, 2).'/resources/data/charts/*.php') ?: [] as $file) {
            $template = require $file;

            if (in_array(strtoupper($countryCode), $template['countries'], true)) {
                return $template + ['file' => basename($file, '.php')];
            }
        }

        return null;
    }

    /**
     * @param  array<int, int>  $taxMap  template tax id => this company's copy
     * @return string|null the template applied, or null when none fits
     */
    public function apply(Company $company, array $taxMap): ?string
    {
        // Country is not company-scoped, but read it scope-free anyway: a
        // missed relation here would silently skip the template.
        $countryCode = $company->country_id
            ? DB::table('countries')->where('id', $company->country_id)->value('code')
            : null;

        $template = $this->templateFor($countryCode);

        if ($template === null) {
            return null;
        }

        $this->recodeAccounts($company, $template['accounts']);
        $this->addAccounts($company, $template['extra_accounts']);
        $this->setTaxes($company, $taxMap, $template['taxes'][strtoupper($countryCode)] ?? null, $template['exempt_tax']);

        return $template['file'];
    }

    /**
     * Look every id up first and update by id, so a new code that equals
     * another account's old code (SYSCOHADA 101000 is "Capital", the
     * standard 101000 is "Current Assets") cannot redirect a later rename.
     *
     * @param  array<string, array{0: string, 1: string}>  $accounts
     */
    protected function recodeAccounts(Company $company, array $accounts): void
    {
        $ids = $this->accountIdsByCode($company);

        foreach ($accounts as $sourceCode => [$code, $name]) {
            if (! isset($ids[$sourceCode])) {
                continue;
            }

            DB::table('accounts_accounts')->where('id', $ids[$sourceCode])->update([
                'code'       => $code,
                'name'       => $name,
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * @param  list<array{0: string, 1: string, 2: string, 3: bool}>  $accounts
     */
    protected function addAccounts(Company $company, array $accounts): void
    {
        $existing = $this->accountIdsByCode($company);

        foreach ($accounts as [$code, $name, $type, $reconcile]) {
            if (isset($existing[$code])) {
                continue;
            }

            $id = DB::table('accounts_accounts')->insertGetId([
                'code'         => $code,
                'name'         => $name,
                'account_type' => $type,
                'reconcile'    => $reconcile,
                'deprecated'   => false,
                'non_trade'    => false,
                'currency_id'  => $company->currency_id,
                'creator_id'   => Auth::id() ?? $company->creator_id,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            DB::table('accounts_account_companies')->insert([
                'account_id' => $id,
                'company_id' => $company->id,
            ]);
        }
    }

    /**
     * The copied percent sale and purchase taxes take the country's standard
     * rate, and an exempt 0 % pair is added from each one. A country with no
     * rate in its template keeps the copied rate: better a rate the company
     * can see and correct than a guessed one.
     *
     * @param  array<int, int>  $taxMap
     * @param  array{0: string, 1: int|float}|null  $rate
     */
    protected function setTaxes(Company $company, array $taxMap, ?array $rate, string $exemptName): void
    {
        $taxes = DB::table('accounts_taxes')
            ->whereIn('id', array_values($taxMap))
            ->where('amount_type', AmountType::PERCENT->value)
            ->whereIn('type_tax_use', [TypeTaxUse::SALE->value, TypeTaxUse::PURCHASE->value])
            ->get();

        foreach ($taxes as $tax) {
            if ($rate !== null) {
                DB::table('accounts_taxes')->where('id', $tax->id)->update([
                    'name'          => $rate[0],
                    'invoice_label' => $rate[0],
                    'amount'        => $rate[1],
                    'updated_at'    => now(),
                ]);
            }

            $this->addExemptCopy($company, $tax, $exemptName);
        }
    }

    protected function addExemptCopy(Company $company, object $tax, string $name): void
    {
        $alreadyThere = DB::table('accounts_taxes')
            ->where('company_id', $company->id)
            ->where('type_tax_use', $tax->type_tax_use)
            ->where('name', $name)
            ->exists();

        if ($alreadyThere) {
            return;
        }

        $row = (array) $tax;
        unset($row['id']);

        $exemptId = DB::table('accounts_taxes')->insertGetId(array_merge($row, [
            'name'          => $name,
            'invoice_label' => $name,
            'amount'        => 0,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]));

        // The same distribution lines as the rated tax, so a 0 % line still
        // posts through the same accounts and reports with it.
        foreach (DB::table('accounts_tax_partition_lines')->where('tax_id', $tax->id)->get() as $line) {
            $copy = (array) $line;
            unset($copy['id']);

            DB::table('accounts_tax_partition_lines')->insert(array_merge($copy, [
                'tax_id'     => $exemptId,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * @return array<string, int>
     */
    protected function accountIdsByCode(Company $company): array
    {
        return DB::table('accounts_accounts')
            ->whereIn('id', DB::table('accounts_account_companies')->where('company_id', $company->id)->select('account_id'))
            ->pluck('id', 'code')
            ->map(fn ($id): int => (int) $id)
            ->all();
    }
}
