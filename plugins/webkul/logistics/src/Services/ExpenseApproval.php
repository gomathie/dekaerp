<?php

namespace Webkul\Logistics\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use LogicException;
use Webkul\Logistics\Enums\ExpenseState;
use Webkul\Logistics\Exceptions\ReceiptRequired;
use Webkul\Logistics\Models\Expense;
use Webkul\Logistics\Models\ExpenseCategory;
use Webkul\Logistics\Support\LogisticsAccess;

class ExpenseApproval
{
    public function submit(Expense $expense): Expense
    {
        return $this->transition($expense, ExpenseState::SUBMITTED, 'update');
    }

    public function approve(Expense $expense): Expense
    {
        return $this->transition($expense, ExpenseState::APPROVED, 'approve');
    }

    public function reject(Expense $expense): Expense
    {
        return $this->transition($expense, ExpenseState::REJECTED, 'approve');
    }

    protected function transition(Expense $expense, ExpenseState $to, string $ability): Expense
    {
        return DB::transaction(function () use ($expense, $to, $ability): Expense {
            $expense = Expense::query()
                ->whereKey($expense->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            LogisticsAccess::ensureEnabled((int) $expense->company_id);
            Gate::authorize($ability, $expense);

            $from = $expense->state;

            if (! $from->canTransitionTo($to)) {
                throw new LogicException("Expense cannot transition from {$from->value} to {$to->value}.");
            }

            $this->ensureReceiptAttached($expense, $to);

            $expense->state = $to;

            if ($to === ExpenseState::APPROVED) {
                $expense->approved_by_id = Auth::id();
                $expense->approved_at = now();
            }

            if ($to === ExpenseState::REJECTED) {
                $expense->approved_by_id = null;
                $expense->approved_at = null;
            }

            $expense->save();

            return $expense->refresh();
        });
    }

    /**
     * Enforce the category's `requires_receipt` rule where money is at stake.
     *
     * The expense form already marks the upload required, but that only binds
     * someone using the form. The API, a console command, an import or a direct
     * call to this service would otherwise walk an unevidenced expense straight
     * through to approved, which is the point at which the company agrees to
     * pay it. Checked on submission (the claimant asserting the claim is
     * complete) and again on approval, because an expense can reach `submitted`
     * without passing through submit() - a seeded record, an import, an API
     * write.
     */
    protected function ensureReceiptAttached(Expense $expense, ExpenseState $to): void
    {
        if (! in_array($to, [ExpenseState::SUBMITTED, ExpenseState::APPROVED], true)) {
            return;
        }

        if (filled($expense->receipt_path)) {
            return;
        }

        // Read scope-free by key: this can run from a queued job or a console
        // command, where the active company is not the expense's, and a
        // category hidden by the company scope would read as "no receipt
        // needed" - the rule failing open, which is the wrong way round.
        $category = ExpenseCategory::withoutGlobalScopes()
            ->whereKey($expense->category_id)
            ->first();

        if (! $category?->requires_receipt) {
            return;
        }

        throw ReceiptRequired::forCategory((string) $category->name);
    }
}
