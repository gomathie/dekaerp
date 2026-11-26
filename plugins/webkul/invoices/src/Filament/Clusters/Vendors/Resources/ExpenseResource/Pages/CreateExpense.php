<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\ExpenseResource\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Webkul\Account\Services\ExpenseRecorder;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ExpenseResource;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ExpenseResource\Pages\Concerns\RecordsExpenses;

class CreateExpense extends CreateRecord
{
    use RecordsExpenses;

    protected static string $resource = ExpenseResource::class;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    /**
     * Saved and posted at once (Z1). Filed under the company being worked
     * in; the form has no company field.
     */
    protected function handleRecordCreation(array $data): Model
    {
        return $this->recordExpense(fn () => app(ExpenseRecorder::class)->create($data, current_company_id()));
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()->label(__(ExpenseResource::LANG.'.actions.save'));
    }

    protected function getCreateAnotherFormAction(): Action
    {
        return parent::getCreateAnotherFormAction()->label(__(ExpenseResource::LANG.'.actions.save-and-new'));
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__(ExpenseResource::LANG.'.notifications.created'));
    }
}
