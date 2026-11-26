<?php

namespace Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalEntryResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions as BaseActions;
use Webkul\Account\Models\Expense;
use Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalEntryResource;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\InvoiceResource;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\BillResource;
use Webkul\Chatter\Filament\Actions as ChatterActions;
use Webkul\Support\Filament\Concerns\HasRepeatableEntryColumnManager;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewJournalEntry extends ViewRecord
{
    use HasRecordNavigationTabs, HasRepeatableEntryColumnManager;

    protected static string $resource = JournalEntryResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        if (in_array($this->record->move_type, [MoveType::OUT_INVOICE, MoveType::OUT_REFUND])) {
            $this->redirect(InvoiceResource::getUrl('view', ['record' => $this->record]));

            return;
        }

        if (in_array($this->record->move_type, [MoveType::IN_INVOICE, MoveType::IN_REFUND])) {
            $this->redirect(BillResource::getUrl('view', ['record' => $this->record]));

            return;
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            ChatterActions\ChatterAction::make()
                ->resource($this->getResource())
                ->activityPlans($this->getRecord()->activityPlans()),
            BaseActions\ConfirmAction::make(),
            // An expense's entry is changed from Expenses only (plan #10
            // Z2). Each base action already decides by state, through
            // visible() or hidden(); the other one is used so that rule stays.
            BaseActions\CancelAction::make()->visible(fn ($record): bool => ! Expense::ownsMove($record)),
            BaseActions\ReverseAction::make()->hidden(fn ($record): bool => Expense::ownsMove($record)),
            BaseActions\ResetToDraftAction::make()->hidden(fn ($record): bool => Expense::ownsMove($record)),
            DeleteAction::make()
                ->visible(fn ($record): bool => ! Expense::ownsMove($record))
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('accounts::filament/resources/invoice/pages/view-invoice.header-actions.delete.notification.title'))
                        ->body(__('accounts::filament/resources/invoice/pages/view-invoice.header-actions.delete.notification.body'))
                ),
        ];
    }
}
