<?php

namespace Webkul\Logistics\Filament\Clusters\Finance\Resources\ExpenseResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;
use Webkul\Logistics\Enums\ExpenseState;
use Webkul\Logistics\Filament\Clusters\Finance\Resources\ExpenseResource;
use Webkul\Logistics\Models\Expense;
use Webkul\Logistics\Services\ExpenseApproval;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewExpense extends ViewRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = ExpenseResource::class;

    protected static string $lang = 'logistics::filament/clusters/finance/resources/expense';

    protected function getHeaderActions(): array
    {
        return [
            static::stateAction('submitExpense', ExpenseState::DRAFT, 'update', 'heroicon-o-paper-airplane'),
            static::stateAction('approveExpense', ExpenseState::SUBMITTED, 'approve', 'heroicon-o-check-circle'),
            static::stateAction('rejectExpense', ExpenseState::SUBMITTED, 'approve', 'heroicon-o-x-circle'),
            EditAction::make(),
            DeleteAction::make(),
        ];
    }

    protected static function stateAction(string $name, ExpenseState $from, string $ability, string $icon): Action
    {
        return Action::make($name)
            ->label(__(static::$lang.'.actions.'.$name.'.label'))
            ->icon($icon)
            ->color($name === 'rejectExpense' ? 'danger' : 'primary')
            ->requiresConfirmation()
            ->modalHeading(__(static::$lang.'.actions.'.$name.'.heading'))
            ->visible(fn (Expense $record): bool => $record->state === $from
                && (Auth::user()?->can($ability, $record) ?? false))
            ->action(function (Expense $record) use ($name): void {
                $service = app(ExpenseApproval::class);

                match ($name) {
                    'submitExpense'  => $service->submit($record),
                    'approveExpense' => $service->approve($record),
                    'rejectExpense'  => $service->reject($record),
                };

                Notification::make()
                    ->success()
                    ->title(__(static::$lang.'.actions.'.$name.'.notification'))
                    ->send();
            });
    }
}
