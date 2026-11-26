<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\ExpenseResource\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;
use Throwable;
use Webkul\Account\Exceptions\PeriodLockedException;
use Webkul\Account\Services\ExpenseRecorder;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ExpenseResource;
use Webkul\Invoice\Models\Expense;

/**
 * Deleting an expense cancels its posted entry, so it asks why (A2b) and
 * goes through ExpenseRecorder, never Filament's plain delete: a row
 * deleted on its own would leave the money out of the bank in the books.
 */
class DeleteExpenseAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'deleteExpense';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__(ExpenseResource::LANG.'.actions.delete.label'))
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->modalDescription(__(ExpenseResource::LANG.'.actions.delete.description'))
            ->schema([
                TextInput::make('reason')
                    ->label(__(ExpenseResource::LANG.'.form.fields.reason'))
                    ->required()
                    ->maxLength(255),
            ])
            ->visible(fn (Expense $record): bool => ExpenseResource::canDelete($record))
            ->action(function (Expense $record, array $data, Action $action): void {
                try {
                    app(ExpenseRecorder::class)->delete($record, $data['reason'] ?? null);
                } catch (ValidationException|PeriodLockedException $e) {
                    Notification::make()->danger()->body($e->getMessage())->send();

                    $action->halt();
                } catch (Throwable $e) {
                    report($e);

                    Notification::make()->danger()->body(__(ExpenseResource::LANG.'.notifications.failed'))->send();

                    $action->halt();
                }

                Notification::make()->success()->title(__(ExpenseResource::LANG.'.actions.delete.done'))->send();

                $action->success();
            })
            ->successRedirectUrl(fn (): string => ExpenseResource::getUrl('index'));
    }
}
