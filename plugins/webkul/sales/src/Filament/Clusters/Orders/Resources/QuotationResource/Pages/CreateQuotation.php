<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Webkul\Account\Filament\Concerns\AsksSaleCurrency;
use Webkul\Sale\Facades\SaleOrder;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource;
use Webkul\Support\Filament\Concerns\HandlesCrossCompanyException;
use Webkul\Support\Filament\Concerns\HasRepeaterColumnManager;

class CreateQuotation extends CreateRecord
{
    // "Ask which currency each sale is in" (plan #10 MC-2b): the same dialog
    // as a new invoice, so a quotation (and a sales order, CreateOrder) is
    // priced at the rate agreed with the customer.
    use AsksSaleCurrency;
    use HandlesCrossCompanyException;
    use HasRepeaterColumnManager;

    protected static string $resource = QuotationResource::class;

    protected ?bool $hasDatabaseTransactions = true;

    public function mount(): void
    {
        parent::mount();

        $this->openSaleCurrencyQuestion();
    }

    protected function saleCurrencyRateColumn(): string
    {
        return 'currency_rate';
    }

    protected function getHeaderActions(): array
    {
        return [$this->saleCurrencyHeaderAction()];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('sales::filament/clusters/orders/resources/quotation/pages/create-quotation.notification.title'))
            ->body(__('sales::filament/clusters/orders/resources/quotation/pages/create-quotation.notification.body'));
    }

    protected function handleRecordCreation(array $data): Model
    {
        if ($this->getParentRecord()) {
            return parent::handleRecordCreation($data);
        }

        $record = new ($this->getModel())($data);

        $this->applyConfirmedSaleRate($record);

        $record->save();

        return $record;
    }

    protected function afterCreate(): void
    {
        try {
            SaleOrder::computeSaleOrder($this->getRecord());
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->body($e->getMessage())
                ->send();

            $this->halt(shouldRollbackDatabaseTransaction: true);
        }

        $this->noteConfirmedSaleRate($this->getRecord(), 'accounts::filament/resources/invoice/pages/create-invoice.sale-currency.chatter-order');
    }
}
