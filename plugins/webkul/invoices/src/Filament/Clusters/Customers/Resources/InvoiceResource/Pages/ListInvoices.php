<?php

namespace Webkul\Invoice\Filament\Clusters\Customers\Resources\InvoiceResource\Pages;

use Webkul\Account\Filament\Resources\InvoiceResource\Pages\ListInvoices as BaseListInvoices;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\InvoiceResource;
use Webkul\Invoice\Filament\Widgets\InvoiceStatsWidget;

class ListInvoices extends BaseListInvoices
{
    protected static string $resource = InvoiceResource::class;

    /**
     * This month's invoice figures above the list (plan #10 R-9, TRACE365's
     * reference screens). It is the Invoices dashboard's own widget and
     * queries, so the list and the dashboard cannot disagree, and its guard
     * (widget permission, module, money-figures switch) decides who sees it.
     */
    public function getHeaderWidgets(): array
    {
        return [
            InvoiceStatsWidget::make([
                'pageFilters'   => ['start_date' => now()->startOfMonth()->toDateString(), 'end_date' => now()->toDateString()],
                'periodHeading' => __('invoices::filament/widgets/invoice-dashboard.invoice-stats.this-month'),
            ]),
        ];
    }
}
