<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\ExpenseResource\Pages;

use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Webkul\Account\Services\ExpenseRecorder;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ExpenseResource;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ExpenseResource\Actions\DeleteExpenseAction;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ExpenseResource\Pages\Concerns\RecordsExpenses;

class EditExpense extends EditRecord
{
    use RecordsExpenses;

    protected static string $resource = ExpenseResource::class;

    /**
     * Rewrites the posted entry (Z1), so a reason is asked, as for any
     * change to a confirmed document (A2b).
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return $this->recordExpense(fn () => app(ExpenseRecorder::class)->update($record, $data, $data['reason'] ?? null));
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteExpenseAction::make(),
        ];
    }

    protected function getRedirectUrl(): ?string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__(ExpenseResource::LANG.'.notifications.saved'));
    }
}
