<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\DirectSaleResource\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Sale\Filament\Clusters\Orders\Resources\DirectSaleResource;
use Webkul\Sale\Services\DirectSaleManager;
use Webkul\Support\Filament\Concerns\HasRepeaterColumnManager;

class CreateDirectSale extends CreateRecord
{
    use HasRepeaterColumnManager;

    protected static string $resource = DirectSaleResource::class;

    protected ?bool $hasDatabaseTransactions = true;

    /**
     * Payment data extracted from the form before the order is created.
     */
    protected array $paymentData = [];

    /**
     * Whether to open the print dialog immediately after the sale.
     */
    public bool $autoPrint = false;

    protected function getFormActions(): array
    {
        return [
            Action::make('saveAndPrint')
                ->label(__('Save & Print Receipt'))
                ->icon('heroicon-o-printer')
                ->action(function () {
                    $this->autoPrint = true;
                    $this->create();
                }),
            Action::make('save')
                ->label(__('Save Sale'))
                ->color('gray')
                ->action(fn () => $this->create()),
            ...$this->getCancelFormAction() ? [$this->getCancelFormAction()] : [],
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Stash the payment-only fields so they are not persisted on the order.
        $this->paymentData = array_filter([
            'journal_id' => $data['payment_journal_id'] ?? null,
            'amount'     => isset($data['paid_amount']) && $data['paid_amount'] !== '' && $data['paid_amount'] !== null
                ? (float) $data['paid_amount']
                : null,
        ], fn ($value) => ! is_null($value));

        unset($data['payment_journal_id'], $data['paid_amount']);

        $data['sale_source'] = DirectSaleResource::SALE_SOURCE;

        // Walk-in customer fallback.
        if (empty($data['partner_id'])) {
            $data['partner_id'] = DirectSaleManager::resolveWalkInCustomer()->id;
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        try {
            app(DirectSaleManager::class)->process($this->getRecord(), $this->paymentData);
        } catch (\Throwable $e) {
            Notification::make()
                ->danger()
                ->title(__('Direct sale could not be completed'))
                ->body($e->getMessage())
                ->send();

            $this->halt(shouldRollBackDatabaseTransaction: true);
        }
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('Direct sale completed'))
            ->body(__('The invoice was created, stock reduced and payment recorded.'));
    }

    protected function getRedirectUrl(): string
    {
        return DirectSaleResource::getUrl('receipt', [
            'record'   => $this->getRecord(),
            'autoprint' => $this->autoPrint ? 1 : 0,
        ]);
    }
}
