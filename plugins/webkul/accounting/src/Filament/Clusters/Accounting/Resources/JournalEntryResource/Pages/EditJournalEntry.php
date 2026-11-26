<?php

namespace Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalEntryResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions as BaseActions;
use Webkul\Account\Models\Expense;
use Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalEntryResource;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\InvoiceResource;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\BillResource;
use Webkul\Chatter\Filament\Actions as ChatterActions;
use Webkul\Support\Filament\Concerns\HandlesCrossCompanyException;
use Webkul\Support\Filament\Concerns\HasRepeaterColumnManager;
use Webkul\Support\Traits\HasRecordNavigationTabs;
use Webkul\Support\Traits\RefreshesRecordState;

class EditJournalEntry extends EditRecord
{
    use HandlesCrossCompanyException;

    protected ?bool $hasDatabaseTransactions = true;

    use HasRecordNavigationTabs, HasRepeaterColumnManager;
    use RefreshesRecordState;

    protected static string $resource = JournalEntryResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        if (in_array($this->record->move_type, [MoveType::OUT_INVOICE, MoveType::OUT_REFUND])) {
            $this->redirect(InvoiceResource::getUrl('edit', ['record' => $this->record]));

            return;
        }

        if (in_array($this->record->move_type, [MoveType::IN_INVOICE, MoveType::IN_REFUND])) {
            $this->redirect(BillResource::getUrl('edit', ['record' => $this->record]));

            return;
        }

        // An expense's entry is changed from Expenses only (plan #10 Z2):
        // edited by hand here it would stay in the bank journal as a
        // free-form entry, which is what Z2 keeps out.
        if (Expense::ownsMove($this->record)) {
            $this->redirect(JournalEntryResource::getUrl('view', ['record' => $this->record]));

            return;
        }
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('accounts::filament/resources/invoice/pages/edit-invoice.notification.title'))
            ->body(__('accounts::filament/resources/invoice/pages/edit-invoice.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            ChatterActions\ChatterAction::make()
                ->resource($this->getResource())
                ->activityPlans($this->getRecord()->activityPlans()),
            BaseActions\ConfirmAction::make(),
            // Each base action already decides by state, through visible()
            // or hidden(); the other one is used here so that rule stays.
            BaseActions\CancelAction::make()->visible(fn ($record): bool => ! Expense::ownsMove($record)),
            BaseActions\ReverseAction::make()->hidden(fn ($record): bool => Expense::ownsMove($record)),
            BaseActions\ResetToDraftAction::make()->hidden(fn ($record): bool => Expense::ownsMove($record)),
            DeleteAction::make()->visible(fn ($record): bool => ! Expense::ownsMove($record)),
        ];
    }

    protected function afterSave(): void
    {
        AccountFacade::computeAccountMove($this->getRecord());

        $this->refreshRecordState();
    }

    public function refreshFormData(array $statePaths): void
    {
        parent::refreshFormData($statePaths);

        $this->rememberData();
    }
}
