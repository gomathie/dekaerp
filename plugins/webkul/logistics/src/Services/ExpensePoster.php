<?php

namespace Webkul\Logistics\Services;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Webkul\Account\Enums as AccountEnums;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Move as AccountMove;
use Webkul\Logistics\Enums\ExpensePaidBy;
use Webkul\Logistics\Enums\ExpenseState;
use Webkul\Logistics\Exceptions\CannotPostBill;
use Webkul\Logistics\Models\CompanySetting;
use Webkul\Logistics\Models\Expense;
use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Support\LogisticsAccess;
use Webkul\Partner\Models\Partner;

/**
 * Turns approved expenses into draft vendor bills (D2, D3).
 *
 * Like ShipmentInvoicer, this builds nothing of its own: it creates the same
 * Account\Models\Move a purchase bill is, in the same shape, and hands it to
 * AccountFacade::computeAccountMove() for totals and taxes. Accounting owns
 * bills, their numbering, their journals and their payment state.
 *
 * One draft bill per partner, never posted. Posting is a finance decision, and
 * a service that posted automatically would put entries in the ledger that
 * nobody chose to make. Follows plugins/webkul/purchases/src/Services/Biller.php.
 */
class ExpensePoster
{
    /**
     * Bill every approved, unbilled expense on a shipment.
     *
     * @return Collection<int, AccountMove> one draft bill per partner
     */
    public function postForShipment(Shipment $shipment): Collection
    {
        LogisticsAccess::ensureEnabled((int) $shipment->company_id);

        // Re-read under the company scope before anything else, for the same
        // reason as ShipmentInvoicer: the policy grants on permission plus the
        // switch, so a shipment obtained another way would otherwise pass.
        $shipment = Shipment::query()->whereKey($shipment->getKey())->firstOrFail();

        $expenses = $shipment->expenses()
            ->where('state', ExpenseState::APPROVED)
            ->whereNull('bill_move_id')
            ->get();

        if ($expenses->isEmpty()) {
            throw CannotPostBill::noExpenses((string) $shipment->name);
        }

        return $this->postExpenses($expenses);
    }

    /**
     * Bill a set of approved expenses, grouped into one draft bill per partner.
     *
     * @param  Collection<int, Expense>|EloquentCollection<int, Expense>  $expenses
     * @return Collection<int, AccountMove>
     */
    public function postExpenses(Collection|EloquentCollection $expenses): Collection
    {
        $expenses = $this->readable($expenses);

        if ($expenses->isEmpty()) {
            throw CannotPostBill::noExpenses('');
        }

        // Resolved before the transaction opens: a missing contact or journal
        // is a setup problem, and finding it out here means nothing has been
        // created and half-written bills cannot be left behind.
        $byPartner = $expenses->groupBy(fn (Expense $expense): int => $this->billPartnerId($expense));

        return DB::transaction(function () use ($byPartner): Collection {
            return $byPartner
                ->map(fn (Collection|EloquentCollection $group, int $partnerId): AccountMove => $this->createBill($partnerId, $group))
                ->values();
        });
    }

    /**
     * Re-read the expenses under the company scope, and authorise each one.
     *
     * @param  Collection<int, Expense>|EloquentCollection<int, Expense>  $expenses
     * @return EloquentCollection<int, Expense>
     */
    protected function readable(Collection|EloquentCollection $expenses): EloquentCollection
    {
        $readable = Expense::query()
            ->whereKey($expenses->map->getKey()->all())
            ->where('state', ExpenseState::APPROVED)
            ->whereNull('bill_move_id')
            ->with(['category', 'employee', 'payee'])
            ->get();

        foreach ($readable as $expense) {
            LogisticsAccess::ensureEnabled((int) $expense->company_id);

            Gate::authorize('postBill', $expense);
        }

        return $readable;
    }

    /**
     * Who the bill is made out to (D3).
     *
     * Paid by the employee: their own contact, so it sits in payables until
     * they are reimbursed. Paid by the company: the payee, and finance
     * registers payment from the account the money actually left.
     */
    protected function billPartnerId(Expense $expense): int
    {
        if ($expense->paid_by === ExpensePaidBy::EMPLOYEE) {
            $employee = $expense->employee;

            // The contact must still exist, not merely be referenced. Employee
            // creates one automatically, so a missing id is rare - a dangling
            // one is not, and putting it on a bill would fail on the foreign
            // key half way through posting. Read scope-free by id: the contact
            // belongs to the expense's company, which is not necessarily the
            // company active in this session.
            $partnerId = $employee?->partner_id;

            if (! $partnerId || ! Partner::withoutGlobalScopes()->whereKey($partnerId)->exists()) {
                throw CannotPostBill::missingEmployeeContact(
                    (string) ($employee?->name ?? $expense->getKey()),
                );
            }

            return (int) $partnerId;
        }

        if (! $expense->payee_id) {
            throw CannotPostBill::missingPartner((string) ($expense->description ?? $expense->getKey()));
        }

        return (int) $expense->payee_id;
    }

    /**
     * @param  Collection<int, Expense>|EloquentCollection<int, Expense>  $expenses
     */
    protected function createBill(int $partnerId, Collection|EloquentCollection $expenses): AccountMove
    {
        $first = $expenses->first();
        $companyId = (int) $first->company_id;
        $settings = CompanySetting::forCompany($companyId);

        $journalId = $settings->bill_journal_id;
        $accountId = $settings->default_expense_account_id;

        if (! $journalId) {
            throw CannotPostBill::missingJournal();
        }

        // Expenses carry no product, and a move line with no account is the
        // silent failure this fork has been bitten by before: accounting
        // computes it untaxed and the bill looks right while being wrong.
        if (! $accountId) {
            throw CannotPostBill::missingExpenseAccount();
        }

        $bill = AccountMove::create([
            'move_type'      => AccountEnums\MoveType::IN_INVOICE,
            'invoice_origin' => $this->origin($expenses),
            'date'           => now(),
            'company_id'     => $companyId,
            'journal_id'     => $journalId,
            // As in ShipmentInvoicer: the currency must be supplied because
            // Move::computeCurrencyId() runs before computeJournalId().
            'currency_id'    => $first->currency_id ?? $first->company?->currency_id,
            'partner_id'     => $partnerId,
            'creator_id'     => Auth::id(),
        ]);

        foreach ($expenses as $expense) {
            $this->createBillLine($bill, $expense, $accountId);
        }

        // Totals and taxes are accounting's job. The bill stays draft: D3 says
        // nothing posts automatically.
        return AccountFacade::computeAccountMove($bill);
    }

    protected function createBillLine(AccountMove $bill, Expense $expense, int $accountId): void
    {
        $bill->lines()->create([
            'name'         => $this->lineName($expense),
            'date'         => $bill->date,
            'creator_id'   => $bill->creator_id,
            'parent_state' => $bill->state,
            // Set explicitly, and it matters: MoveLine's saving hook calls
            // computeAccountId() before computeDisplayType(), so a line created
            // without a display type is costed through the "default" branch,
            // which replaces account_id with the journal's default account.
            // MoveCalculator::recomputeLine() also zeroes the totals of any line
            // that is not PRODUCT or COGS. Both failures are silent: the bill
            // looks finished, on the wrong account, for nothing.
            'display_type' => DisplayType::PRODUCT,
            'quantity'     => 1,
            'price_unit'   => $expense->amount,
            'company_id'   => $bill->company_id,
            'currency_id'  => $bill->currency_id,
            'partner_id'   => $bill->partner_id,
            'account_id'   => $accountId,
        ]);

        // The expense keeps its bill, which is what stops it being billed
        // twice - postForShipment() and readable() both skip anything that
        // already has one.
        $expense->forceFill([
            'bill_move_id' => $bill->getKey(),
            'state'        => ExpenseState::BILLED,
        ])->save();

    }

    protected function lineName(Expense $expense): string
    {
        return collect([
            $expense->category?->name,
            $expense->description,
            $expense->vendor_reference,
        ])->filter()->implode(' - ') ?: (string) $expense->getKey();
    }

    /**
     * @param  Collection<int, Expense>|EloquentCollection<int, Expense>  $expenses
     */
    protected function origin(Collection|EloquentCollection $expenses): ?string
    {
        return $expenses
            ->map(fn (Expense $expense): ?string => $expense->shipment?->name)
            ->filter()
            ->unique()
            ->implode(', ') ?: null;
    }
}
