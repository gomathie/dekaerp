<?php

namespace Webkul\Account\Filament\Resources\InvoiceResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Events\InvoicesDeletedFromScreen;
use Webkul\Account\Filament\Resources\CreditNoteResource;
use Webkul\Account\Filament\Resources\InvoiceResource;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions as BaseActions;
use Webkul\Account\Filament\Resources\InvoiceResource\Support\InvoiceSchemaRegistry;
use Webkul\Account\Models\Move;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Support\Filament\Concerns\HasRepeatableEntryColumnManager;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewInvoice extends ViewRecord
{
    use HasRecordNavigationTabs, HasRepeatableEntryColumnManager;

    protected static string $resource = InvoiceResource::class;

    protected static string $reverseResource = CreditNoteResource::class;

    /**
     * @return class-string
     */
    public static function getReverseResource(): string
    {
        return static::$reverseResource;
    }

    protected function getHeaderActions(): array
    {
        return [
            ChatterAction::make()
                ->resource($this->getResource())
                ->activityPlans($this->getRecord()->activityPlans()),
            BaseActions\PrintAndSendAction::make(),
            BaseActions\SendReminderAction::make(),
            BaseActions\PreviewAction::make()
                ->setTemplate('accounts::invoice/actions/preview.index'),
            // Till receipt for a sale paid at once (user, 2026-11-22, "New
            // direct sale"). The Preview action's own visibility (posted only)
            // and print path, with a narrow template.
            BaseActions\PreviewAction::make('printReceipt')
                ->setTemplate('accounts::invoice/actions/receipt.index')
                ->label(__('accounts::filament/resources/invoice/pages/create-invoice.receipt.label'))
                ->modalHeading(__('accounts::filament/resources/invoice/pages/create-invoice.receipt.heading'))
                ->icon('heroicon-o-receipt-percent'),
            BaseActions\PayAction::make(),
            BaseActions\ConfirmAction::make(),
            BaseActions\CancelAction::make(),
            BaseActions\SetAsCheckedAction::make(),
            BaseActions\ReverseAction::make()
                ->setResource(static::getReverseResource())
                ->label(__('accounts::filament/resources/invoice/pages/view-invoice.header-actions.reverse.label'))
                ->modalHeading(__('accounts::filament/resources/invoice/pages/view-invoice.header-actions.reverse.modal-heading')),
            BaseActions\ResetToDraftAction::make(),
            DeleteAction::make()
                ->hidden(fn (Move $record): bool => $record->state == MoveState::POSTED)
                // As on EditInvoice: a plugin's questions before the delete
                // (Sales: also cancel the order?), answers handed back after.
                ->schema(fn (Move $record): array => InvoiceSchemaRegistry::renderForm('delete', [$record]))
                ->after(fn (Move $record, array $data) => InvoicesDeletedFromScreen::dispatch([$record->getKey()], $data))
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('accounts::filament/resources/invoice/pages/view-invoice.header-actions.delete.notification.title'))
                        ->body(__('accounts::filament/resources/invoice/pages/view-invoice.header-actions.delete.notification.body'))
                ),
        ];
    }
}
