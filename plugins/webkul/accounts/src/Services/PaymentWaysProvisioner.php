<?php

namespace Webkul\Account\Services;

use Illuminate\Support\Facades\DB;
use Webkul\Support\Models\Company;

/**
 * The ready-made ways to pay (user, 2026-11-19/20): Cash, Bank transfer and
 * Mobile Money, each a journal the Pay window lists by name.
 *
 * - Plain names: the default "Cash Transactions" / "Bank Transactions" are
 *   renamed "Cash" / "Bank transfer". A journal someone renamed is left
 *   alone.
 * - Mobile Money is a bank-type journal copied from the company's BANK
 *   journal, with its **own** liquidity account (user: own account), so its
 *   balance is never mixed with the bank's. Nothing existing is changed.
 *
 * Idempotent: run on setup and by migration for companies already set up;
 * a company without a BANK journal is skipped.
 */
class PaymentWaysProvisioner
{
    public const MOBILE_MONEY_CODE = 'MOMO';

    protected const RENAMES = [
        'CASH' => ['from' => 'Cash Transactions', 'to' => 'Cash'],
        'BANK' => ['from' => 'Bank Transactions', 'to' => 'Bank transfer'],
    ];

    public function provision(Company $company): void
    {
        DB::transaction(function () use ($company) {
            $this->renameDefaults($company);
            $this->ensureMobileMoney($company);
        });
    }

    protected function renameDefaults(Company $company): void
    {
        foreach (self::RENAMES as $code => $names) {
            DB::table('accounts_journals')
                ->where('company_id', $company->id)
                ->where('code', $code)
                ->where('name', $names['from'])
                ->update(['name' => $names['to'], 'updated_at' => now()]);
        }
    }

    protected function ensureMobileMoney(Company $company): void
    {
        $exists = DB::table('accounts_journals')
            ->where('company_id', $company->id)
            ->where('code', self::MOBILE_MONEY_CODE)
            ->exists();

        if ($exists) {
            return;
        }

        $this->addWay($company, 'Mobile Money', self::MOBILE_MONEY_CODE);
    }

    /**
     * A company's own way to pay, added from the Pay window ("add your own",
     * user, 2026-11-19): a bank-type journal with its own account, copied
     * from the BANK journal. Returns the new journal id, or null when the
     * company has no BANK journal to copy.
     */
    public function addWay(Company $company, string $name, ?string $code = null): ?int
    {
        $name = trim($name);

        if ($name === '') {
            return null;
        }

        $code ??= $this->freeJournalCode($company, $name);

        $bank = DB::table('accounts_journals')
            ->where('company_id', $company->id)
            ->where('code', 'BANK')
            ->first();

        if (! $bank || ! $bank->default_account_id) {
            return null;
        }

        $bankAccount = DB::table('accounts_accounts')->where('id', $bank->default_account_id)->first();

        if (! $bankAccount) {
            return null;
        }

        $now = now();

        $accountRow = (array) $bankAccount;
        unset($accountRow['id']);
        $accountRow['name'] = $name;
        $accountRow['code'] = $this->freeAccountCode($company, (string) $bankAccount->code);
        $accountRow['created_at'] = $now;
        $accountRow['updated_at'] = $now;

        $accountId = DB::table('accounts_accounts')->insertGetId($accountRow);

        DB::table('accounts_account_companies')->insert([
            'account_id' => $accountId,
            'company_id' => $company->id,
        ]);

        $journalRow = (array) $bank;
        unset($journalRow['id']);
        $journalRow['name'] = $name;
        $journalRow['code'] = $code;
        $journalRow['default_account_id'] = $accountId;
        $journalRow['bank_account_id'] = null;
        if (array_key_exists('sort', $journalRow)) {
            $journalRow['sort'] = ((int) $bank->sort) + 1;
        }
        $journalRow['created_at'] = $now;
        $journalRow['updated_at'] = $now;

        $journalId = DB::table('accounts_journals')->insertGetId($journalRow);

        // Same payment methods as the bank journal, so the Pay window can use
        // it at once (a journal without lines cannot register a payment).
        foreach (DB::table('accounts_payment_method_lines')->where('journal_id', $bank->id)->get() as $line) {
            $lineRow = (array) $line;
            unset($lineRow['id']);
            $lineRow['journal_id'] = $journalId;
            $lineRow['created_at'] = $now;
            $lineRow['updated_at'] = $now;

            DB::table('accounts_payment_method_lines')->insert($lineRow);
        }

        return $journalId;
    }

    /**
     * Up to five letters of the name, numbered when taken in this company.
     */
    protected function freeJournalCode(Company $company, string $name): string
    {
        $base = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name) ?: 'PAY', 0, 5));
        $used = DB::table('accounts_journals')->where('company_id', $company->id)->pluck('code')->all();
        $code = $base;

        for ($i = 2; in_array($code, $used, true); $i++) {
            $code = substr($base, 0, 4).$i;
        }

        return $code;
    }

    /**
     * The bank account's code plus one, or the next free one after it, among
     * this company's accounts.
     */
    protected function freeAccountCode(Company $company, string $bankCode): string
    {
        $used = DB::table('accounts_accounts')
            ->join('accounts_account_companies', 'accounts_account_companies.account_id', '=', 'accounts_accounts.id')
            ->where('accounts_account_companies.company_id', $company->id)
            ->pluck('accounts_accounts.code')
            ->map(fn ($code) => (string) $code)
            ->all();

        if (! ctype_digit($bankCode)) {
            $candidate = $bankCode.'-MM';

            return in_array($candidate, $used, true) ? $candidate.'-'.$company->id : $candidate;
        }

        $next = (int) $bankCode + 1;

        while (in_array((string) $next, $used, true)) {
            $next++;
        }

        return str_pad((string) $next, strlen($bankCode), '0', STR_PAD_LEFT);
    }
}
