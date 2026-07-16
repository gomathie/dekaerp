<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\DirectSaleResource\Pages;

use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Webkul\Sale\Filament\Clusters\Orders\Resources\DirectSaleResource;

class PrintReceipt extends Page
{
    use InteractsWithRecord;

    protected static string $resource = DirectSaleResource::class;

    protected string $view = 'sales::filament.clusters.orders.resources.direct-sale.print-receipt';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->getRecord()->load([
            'partner',
            'currency',
            'company',
            'user',
            'lines.product',
            'lines.taxes',
            'invoices',
        ]);
    }

    public function getTitle(): string
    {
        return __('Receipt :name', ['name' => $this->getRecord()->name]);
    }

    public function getHeading(): string
    {
        return __('Receipt');
    }

    /**
     * The customer invoice that backs this direct sale.
     */
    public function getInvoice()
    {
        return $this->getRecord()->invoices->sortByDesc('id')->first();
    }

    /**
     * Amount already paid against the invoice.
     */
    public function getPaidAmount(): float
    {
        $invoice = $this->getInvoice();

        if (! $invoice) {
            return 0.0;
        }

        return (float) $invoice->amount_total - (float) ($invoice->amount_residual ?? 0);
    }

    public function shouldAutoPrint(): bool
    {
        return (bool) request()->query('autoprint', false);
    }
}
