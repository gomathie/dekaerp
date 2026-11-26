<?php

namespace Webkul\Account\Services;

use Carbon\CarbonImmutable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Facades\Tax as TaxFacade;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Expense;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\Partner;
use Webkul\Account\Models\Tax;
use Webkul\Partner\Enums\AccountType as PartnerAccountType;
use Webkul\PluginManager\Support\PluginEntitlement;
use Webkul\Support\Enums\SequenceResetFrequency;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\Scopes\CompanyScope;
use Webkul\Support\Services\SequenceService;

/**
 * The Expenses service (plan #10 Z1/Z2, user 2026-11-26). Every expense is
 * written here, and only here, together with its journal entry:
 *
 *  - Save posts one balanced entry in the chosen bank or cash journal:
 *    expense account debited (net), tax account debited (if any), the
 *    journal's money account credited (paid out). ExpenseEntry builds it.
 *  - Editing rewrites that entry; deleting resets it to draft and cancels
 *    it, DEKA's own way of undoing a posted entry, with a reason. The lock
 *    date is enforced by Move's own hooks (PeriodLock), so a closed month
 *    refuses both, and the whole change rolls back.
 *
 * Every reference is checked against the expense's company here, not only
 * in the form: the super-admin Gate::before skips policies, and a Livewire
 * payload can carry any id. A journal, account, tax, vendor or currency of
 * another company is refused, whoever is acting.
 */
class ExpenseRecorder
{
    public const SEQUENCE = 'accounts.expense';

    /** The Expenses screen lives in the Invoices module (Vendors cluster). */
    public const MODULE = 'invoices';

    /** Folder of receipt files, inside the company's own storage prefix. */
    public const RECEIPT_DIRECTORY = 'accounts/expenses';

    public const MAX_AMOUNT = 999999999;

    public function __construct(protected ExpenseEntry $entry) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data, ?int $companyId): Expense
    {
        $company = $this->company($companyId);

        return DB::transaction(function () use ($data, $company): Expense {
            $resolved = $this->resolve($data, $company);

            $expense = new Expense;
            $expense->forceFill([
                'company_id' => $company->id,
                'creator_id' => Auth::id(),
                'number'     => $this->nextNumber($company->id, $resolved['date']),
            ]);
            $this->apply($expense, $resolved, $data);
            $expense->save();

            $move = $this->entry->post(
                companyId: $company->id,
                currencyId: $resolved['currency']->id,
                journalId: $resolved['journal']->id,
                date: $resolved['date'],
                lines: $this->lines($expense, $resolved),
                paidFromAccountId: (int) $resolved['journal']->default_account_id,
                paidFromLabel: $this->paidFromLabel($expense, $resolved),
                partnerId: $resolved['partner']?->id,
                reference: $expense->number,
                narration: $this->narration($expense),
                creatorId: Auth::id(),
                expense: $expense,
            );

            $expense->forceFill(['move_id' => $move->id])->save();

            return $expense->refresh();
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Expense $expense, array $data, ?string $reason): Expense
    {
        $this->assertReason($reason);

        return DB::transaction(function () use ($expense, $data, $reason): Expense {
            $expense = $this->lockedExpense($expense);
            $company = $this->company((int) $expense->company_id);
            $resolved = $this->resolve($data, $company);

            // The row first, so the entry it owns sees the new journal when
            // it is saved (Move::isExpenseEntryInItsJournal()).
            $this->apply($expense, $resolved, $data);
            $expense->save();

            $move = $this->entry->rewrite(
                move: $this->ownMove($expense),
                expense: $expense,
                reason: (string) $reason,
                currencyId: $resolved['currency']->id,
                journalId: $resolved['journal']->id,
                date: $resolved['date'],
                lines: $this->lines($expense, $resolved),
                paidFromAccountId: (int) $resolved['journal']->default_account_id,
                paidFromLabel: $this->paidFromLabel($expense, $resolved),
                partnerId: $resolved['partner']?->id,
                reference: $expense->number,
                narration: $this->narration($expense),
                creatorId: Auth::id(),
            );

            $expense->forceFill(['move_id' => $move->id])->save();

            return $expense->refresh();
        });
    }

    /**
     * Deleting reverses the books by DEKA's own undo: the posted entry is
     * reset to draft with the reason (A2b), then cancelled, so it leaves the
     * ledger and the bank balance, and the history says why. The expense row
     * is soft-deleted and keeps its link to the cancelled entry.
     */
    public function delete(Expense $expense, ?string $reason): void
    {
        $this->assertReason($reason);

        DB::transaction(function () use ($expense, $reason): void {
            $expense = $this->lockedExpense($expense);
            $this->company((int) $expense->company_id);

            if ($expense->move_id) {
                $move = $this->ownMove($expense);

                if ($move->state === MoveState::POSTED) {
                    $move = AccountFacade::resetToDraftMove($move, (string) $reason);
                }

                if ($move->state !== MoveState::CANCEL) {
                    AccountFacade::cancelMove($move);
                }
            }

            $expense->delete();
        });
    }

    /**
     * Net, tax and paid-out amounts for an amount and an optional tax, in
     * the expense currency. The tax's own setting decides: a tax included
     * in the price is taken out of the amount typed (the amount is what was
     * paid); a tax excluded from it is added on top. Amounts come from
     * DEKA's tax engine (TaxFacade::computeAll), never a second formula.
     *
     * @return array{net: float, tax: float, total: float, taxes: list<array{tax_id: int, name: string, amount: float, account_id: ?int, tax_repartition_line_id: ?int}>}
     */
    public function split(float $amount, ?Tax $tax, Currency $currency): array
    {
        $amount = $currency->round($amount);

        if (! $tax) {
            return ['net' => $amount, 'tax' => 0.0, 'total' => $amount, 'taxes' => []];
        }

        $computed = TaxFacade::computeAll(collect([$tax]), $amount, $currency);

        $taxes = array_map(fn (array $row): array => [
            'tax_id'                  => (int) $row['id'],
            'name'                    => (string) $row['name'],
            'amount'                  => $currency->round((float) $row['amount']),
            'account_id'              => $row['account_id'] ? (int) $row['account_id'] : null,
            'tax_repartition_line_id' => $row['tax_repartition_line_id'] ? (int) $row['tax_repartition_line_id'] : null,
        ], $computed['taxes']);

        $taxTotal = $currency->round(array_sum(array_column($taxes, 'amount')));

        // Rounded from the parts, so net + tax is exactly what left the
        // account and the entry balances to the cent.
        return $tax->price_include
            ? ['net' => $currency->round($amount - $taxTotal), 'tax' => $taxTotal, 'total' => $amount, 'taxes' => $taxes]
            : ['net' => $amount, 'tax' => $taxTotal, 'total' => $currency->round($amount + $taxTotal), 'taxes' => $taxes];
    }

    /**
     * The bank and cash journals an expense may be paid from: the company's
     * own, with a money account to credit. The form lists these and the
     * service accepts nothing else (one rule for both).
     */
    public function journalsQuery(int $companyId): Builder
    {
        return Journal::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->whereIn('type', [JournalType::BANK, JournalType::CASH])
            ->whereNotNull('default_account_id')
            ->orderBy('sort')
            ->orderBy('name');
    }

    /** Expense-type accounts of the company's chart, not deprecated. */
    public function accountsQuery(int $companyId): Builder
    {
        return Account::withoutGlobalScopes()
            ->whereIn('account_type', Account::expenseTypes())
            ->where('deprecated', false)
            ->where(owned_by_company($companyId));
    }

    /** The company's own active purchase taxes. */
    public function taxesQuery(int $companyId): Builder
    {
        return Tax::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('type_tax_use', TypeTaxUse::PURCHASE)
            ->where('is_active', true);
    }

    /**
     * Vendors the expense may name: the company's own contacts and shared
     * ones (company_id null), as CompanyScope shows them. Soft deletes stay.
     */
    public function partnersQuery(int $companyId): Builder
    {
        return Partner::withoutGlobalScope(CompanyScope::class)
            ->where(fn (Builder $query) => $query->where('company_id', $companyId)->orWhereNull('company_id'));
    }

    /** Currencies the company may use on a document (CR-6), active only. */
    public function currenciesQuery(int $companyId): Builder
    {
        return Currency::query()->active()->permittedFor($companyId);
    }

    /** The account of the company's latest expense, suggested first. */
    public function lastUsedAccountId(int $companyId): ?int
    {
        $accountId = Expense::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->latest('id')
            ->value('expense_account_id');

        return $accountId && $this->accountsQuery($companyId)->whereKey($accountId)->exists() ? (int) $accountId : null;
    }

    /**
     * A vendor typed in the form that does not exist yet, stamped with the
     * expense's company (per-company contacts, 2026-10-29).
     */
    public function createVendor(string $name, ?int $companyId): Partner
    {
        $company = $this->company($companyId);

        $name = trim($name);

        if ($name === '' || mb_strlen($name) > 255) {
            throw ValidationException::withMessages(['partner_id' => __('accounts::expenses.errors.vendor-name')]);
        }

        $partner = new Partner;
        $partner->forceFill([
            'name'          => $name,
            'account_type'  => PartnerAccountType::COMPANY,
            'company_id'    => $company->id,
            'creator_id'    => Auth::id(),
            'supplier_rank' => 1,
        ])->save();

        return $partner;
    }

    /**
     * The receipt must be a file stored by the form, in the company's own
     * receipt folder (the tenant disk adds the company prefix). A path typed
     * into a tampered payload, pointing anywhere else, is refused.
     */
    public static function isReceiptPath(?string $path): bool
    {
        if (blank($path)) {
            return true;
        }

        return str_starts_with($path, self::RECEIPT_DIRECTORY.'/')
            && ! str_contains($path, '..')
            && ! str_contains($path, '\\')
            && preg_match('#^[A-Za-z0-9/_.\-]+$#', $path) === 1;
    }

    protected function company(?int $companyId): Company
    {
        // The Invoices module switch, checked for the expense's company on
        // every write: hiding the menu alone would leave the service open.
        PluginEntitlement::assertForCompany(self::MODULE, $companyId);

        return Company::query()->whereKey($companyId)->firstOrFail();
    }

    /**
     * Re-read under the company scope and lock it: an id from a payload that
     * belongs to another company is simply not found.
     */
    protected function lockedExpense(Expense $expense): Expense
    {
        return Expense::query()->whereKey($expense->getKey())->lockForUpdate()->firstOrFail();
    }

    /**
     * The expense's own entry, read past the ownership scope: the expense
     * row names it, and whoever may change the expense may change its entry.
     */
    protected function ownMove(Expense $expense): Move
    {
        $move = Move::withoutGlobalScopes()->whereKey($expense->move_id)->firstOrFail();

        return $move->forExpense($expense);
    }

    protected function assertReason(?string $reason): void
    {
        // A2b: undoing a confirmed entry needs a reason. Checked before any
        // write, so a blank reason changes nothing.
        if (trim((string) $reason) === '') {
            throw ValidationException::withMessages(['reason' => __('accounts::expenses.errors.reason-required')]);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{date: string, amount: float, currency: Currency, rate: float, journal: Journal, account: Account, tax: ?Tax, partner: ?Partner, split: array}
     */
    protected function resolve(array $data, Company $company): array
    {
        $errors = [];

        $date = $data['date'] ?? null;
        $date = filled($date) ? substr((string) ($date instanceof DateTimeInterface ? $date->format('Y-m-d') : $date), 0, 10) : null;

        if (! $date || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || ! strtotime($date)) {
            $errors['date'] = __('accounts::expenses.errors.date');
        }

        $amount = is_numeric($data['amount'] ?? null) ? (float) $data['amount'] : null;

        if ($amount === null || $amount <= 0 || $amount > self::MAX_AMOUNT) {
            $errors['amount'] = __('accounts::expenses.errors.amount');
        }

        $journal = $this->journalsQuery($company->id)->whereKey($data['journal_id'] ?? 0)->first();

        if (! $journal) {
            $errors['journal_id'] = __('accounts::expenses.errors.journal');
        }

        $account = $this->accountsQuery($company->id)->whereKey($data['expense_account_id'] ?? 0)->first();

        if (! $account) {
            $errors['expense_account_id'] = __('accounts::expenses.errors.account');
        }

        $tax = null;

        if (filled($data['tax_id'] ?? null)) {
            $tax = $this->taxesQuery($company->id)->whereKey($data['tax_id'])->first();

            if (! $tax) {
                $errors['tax_id'] = __('accounts::expenses.errors.tax');
            }
        }

        $partner = null;

        if (filled($data['partner_id'] ?? null)) {
            $partner = $this->partnersQuery($company->id)->whereKey($data['partner_id'])->first();

            if (! $partner) {
                $errors['partner_id'] = __('accounts::expenses.errors.vendor');
            }
        }

        $currency = $this->currenciesQuery($company->id)->whereKey($data['currency_id'] ?? $company->currency_id)->first();

        if (! $currency) {
            $errors['currency_id'] = __('accounts::expenses.errors.currency');
        }

        if (! static::isReceiptPath($data['receipt_path'] ?? null)) {
            $errors['receipt_path'] = __('accounts::expenses.errors.receipt');
        }

        if (mb_strlen((string) ($data['reference'] ?? '')) > 255) {
            $errors['reference'] = __('accounts::expenses.errors.reference');
        }

        if ($errors) {
            throw ValidationException::withMessages($errors);
        }

        $companyCurrency = $company->currency;

        // The day's rate, by the rule invoices use (Move::computeInvoiceCurrencyRate():
        // company currency to document currency on the document date). A
        // currency with no rate is refused: posting it at 1:1 would book a
        // foreign amount as if it were the company's own.
        $rate = 1.0;

        if ($currency->id !== $companyCurrency->id) {
            if (! $currency->canConvertTo($companyCurrency, $company, $date)) {
                throw ValidationException::withMessages(['currency_id' => __('accounts::expenses.errors.no-rate', ['currency' => $currency->name])]);
            }

            $rate = (float) $currency->getConversionRate($companyCurrency, $currency, $company, $date);
        }

        return [
            'date'             => $date,
            'amount'           => $amount,
            'currency'         => $currency,
            'company_currency' => $companyCurrency,
            'rate'             => $rate,
            'journal'          => $journal,
            'account'          => $account,
            'tax'              => $tax,
            'partner'          => $partner,
            'split'            => $this->split($amount, $tax, $currency),
        ];
    }

    /**
     * @param  array<string, mixed>  $resolved
     * @param  array<string, mixed>  $data
     */
    protected function apply(Expense $expense, array $resolved, array $data): void
    {
        $split = $resolved['split'];

        $expense->forceFill([
            'date'                 => $resolved['date'],
            'currency_id'          => $resolved['currency']->id,
            'amount'               => $resolved['amount'],
            'currency_rate'        => $resolved['rate'],
            'amount_total'         => $split['total'],
            'amount_tax'           => $split['tax'],
            'amount_total_company' => $this->lineAmounts($resolved)['total'],
            'expense_account_id'   => $resolved['account']->id,
            'journal_id'           => $resolved['journal']->id,
            'partner_id'           => $resolved['partner']?->id,
            'tax_id'               => $resolved['tax']?->id,
            'reference'            => filled($data['reference'] ?? null) ? trim((string) $data['reference']) : null,
            'note'                 => filled($data['note'] ?? null) ? trim((string) $data['note']) : null,
            'receipt_path'         => filled($data['receipt_path'] ?? null) ? (string) $data['receipt_path'] : null,
            'receipt_name'         => filled($data['receipt_path'] ?? null) && filled($data['receipt_name'] ?? null)
                ? mb_substr(basename((string) $data['receipt_name']), 0, 255)
                : null,
        ]);
    }

    /**
     * Company-currency amounts of each line, rounded per line; the paid-out
     * credit is their sum, so the entry balances in the company currency.
     *
     * @param  array<string, mixed>  $resolved
     * @return array{net: float, taxes: list<float>, total: float}
     */
    protected function lineAmounts(array $resolved): array
    {
        $companyCurrency = $resolved['company_currency'];
        $round = fn (float $value): float => $companyCurrency->round($resolved['rate'] > 0 ? $value / $resolved['rate'] : $value);

        $net = $round($resolved['split']['net']);
        $taxes = array_map(fn (array $tax): float => $round($tax['amount']), $resolved['split']['taxes']);

        return ['net' => $net, 'taxes' => $taxes, 'total' => $companyCurrency->round($net + array_sum($taxes))];
    }

    /**
     * @param  array<string, mixed>  $resolved
     * @return list<array<string, mixed>>
     */
    protected function lines(Expense $expense, array $resolved): array
    {
        $amounts = $this->lineAmounts($resolved);
        $label = $this->lineLabel($expense, $resolved['account']);

        $lines = [[
            'account_id'      => $resolved['account']->id,
            'amount'          => $amounts['net'],
            'amount_currency' => $resolved['split']['net'],
            'name'            => $label,
        ]];

        foreach ($resolved['split']['taxes'] as $index => $tax) {
            if ($tax['amount'] == 0.0) {
                continue;
            }

            $lines[] = [
                // A repartition line with no account means the tax is not
                // recoverable: it is part of the cost, as on a bill.
                'account_id'              => $tax['account_id'] ?? $resolved['account']->id,
                'amount'                  => $amounts['taxes'][$index],
                'amount_currency'         => $tax['amount'],
                'name'                    => $tax['name'],
                'tax_line_id'             => $tax['tax_id'],
                'tax_repartition_line_id' => $tax['tax_repartition_line_id'],
                'tax_base_amount'         => $amounts['net'],
            ];
        }

        return $lines;
    }

    protected function lineLabel(Expense $expense, Account $account): string
    {
        return mb_substr(implode(' - ', array_filter([$expense->number, $expense->reference ?: $account->name])), 0, 255);
    }

    /**
     * @param  array<string, mixed>  $resolved
     */
    protected function paidFromLabel(Expense $expense, array $resolved): string
    {
        return mb_substr(implode(' - ', array_filter([$expense->number, $resolved['journal']->name])), 0, 255);
    }

    /**
     * Escaped, as the import does: the entry's narration is shown on
     * accounting screens that print it as HTML.
     */
    protected function narration(Expense $expense): ?string
    {
        return filled($expense->note) ? e($expense->note) : null;
    }

    protected function nextNumber(int $companyId, string $date): string
    {
        $defaults = [
            'name'            => 'Expense',
            'prefix'          => 'EXP/%(year)/',
            'padding'         => 5,
            'reset_frequency' => SequenceResetFrequency::YEARLY,
        ];

        // Ensure first: next() alone falls back to, and creates, a counter
        // shared by every company (AGENTS.md, SequenceService).
        SequenceService::ensure(self::SEQUENCE, $companyId, $defaults);

        return SequenceService::next(self::SEQUENCE, $companyId, $defaults, CarbonImmutable::parse($date));
    }
}
