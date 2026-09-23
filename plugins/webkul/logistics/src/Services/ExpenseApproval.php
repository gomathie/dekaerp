<?php

namespace Webkul\Logistics\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use LogicException;
use Webkul\Logistics\Enums\ExpenseState;
use Webkul\Logistics\Models\Expense;
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
}
