<?php

namespace Webkul\Invoice\Notifications;

use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\InvoiceResource;
use Webkul\Support\Services\CompanyContext;

class RecurringInvoiceCreated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $invoiceId, public int $companyId, public string $invoiceNumber) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $invoiceUrl = app(CompanyContext::class)->runFor($this->companyId, fn (): string => InvoiceResource::getUrl('edit', ['record' => $this->invoiceId]));

        return FilamentNotification::make()
            ->title(__('invoices::recurring.notifications.created.title'))
            ->body(__('invoices::recurring.notifications.created.body', ['number' => $this->invoiceNumber]))
            ->actions([
                Action::make('view')
                    ->label(__('invoices::recurring.notifications.created.view'))
                    ->url($invoiceUrl)
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
