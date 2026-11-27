<?php

namespace Webkul\Account\Filament\Resources\PaymentResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Webkul\Account\Filament\Resources\PaymentResource;
use Webkul\Account\Services\QuickPayment;

class CreatePayment extends CreateRecord
{
    /**
     * Customer payment pages set this. Save then posts the payment, and
     * applies the open invoices when amounts were entered (U96). Vendor
     * pages leave it false, so they stay a draft until Confirm.
     */
    public bool $allocatesOpenInvoices = false;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected static string $resource = PaymentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['allocations']);

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        // The invoice amounts are not columns. They are read from the
        // live form, which still has them after mutateFormDataBeforeCreate
        // removed them from the data about to be stored.
        if ($this->allocatesOpenInvoices && QuickPayment::isReceive($data['payment_type'] ?? null)) {
            return QuickPayment::createReceivedPayment([
                ...$data,
                'allocations' => $this->data['allocations'] ?? [],
            ]);
        }

        return parent::handleRecordCreation($data);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('accounts::filament/resources/payment/pages/create-payment.notification.title'))
            ->body(__('accounts::filament/resources/payment/pages/create-payment.notification.body'));
    }
}
