<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\ExpenseResource\Pages\Concerns;

use Closure;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Webkul\Account\Exceptions\PeriodLockedException;
use Webkul\Account\Models\Expense as AccountExpense;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ExpenseResource;
use Webkul\Invoice\Models\Expense;
use Webkul\PluginManager\Exceptions\PluginNotEntitledException;

/**
 * The create and edit pages hand the form to ExpenseRecorder and show its
 * refusals: a field the service refuses is marked on that field, and a
 * locked period or a missing module becomes a notice. Nothing is saved
 * when either happens: the recorder works in one transaction.
 */
trait RecordsExpenses
{
    protected function recordExpense(Closure $write): Model
    {
        try {
            $expense = $write();
        } catch (ValidationException $e) {
            throw ValidationException::withMessages(
                collect($e->errors())->mapWithKeys(fn (array $messages, string $field): array => ['data.'.$field => $messages])->all(),
            );
        } catch (PeriodLockedException|PluginNotEntitledException $e) {
            Notification::make()->danger()->title(__(ExpenseResource::LANG.'.notifications.refused'))->body($e->getMessage())->send();

            $this->halt();
        }

        return $expense instanceof AccountExpense
            ? Expense::query()->findOrFail($expense->getKey())
            : $expense;
    }
}
