<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource\Pages;

use Webkul\Account\Filament\Resources\BillResource\Pages\ListBills as BaseListBills;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource;
use Webkul\Invoice\Filament\Widgets\BillStatsWidget;
use Webkul\Support\Filament\DashboardAccess;

class ListBills extends BaseListBills
{
    protected static string $resource = BillResource::class;

    /**
     * This month's bill figures above the list (plan #9 R-9, TRACE365's
     * reference screens). It is the Invoices dashboard's own widget and
     * queries, so the list and the dashboard cannot disagree, and its guard
     * (widget permission, module, "See dashboard") decides who sees it.
     *
     * The money-figures switch is checked here because the widget's guard
     * does not: only the dashboard pages read it, so without this line a
     * user with money figures off saw the month's billed total here.
     */
    public function getHeaderWidgets(): array
    {
        if (! DashboardAccess::mayViewMoney()) {
            return [];
        }

        return [
            BillStatsWidget::make([
                'pageFilters'   => ['start_date' => now()->startOfMonth()->toDateString(), 'end_date' => now()->toDateString()],
                'periodHeading' => __('invoices::filament/widgets/invoice-dashboard.bill-stats.this-month'),
            ]),
        ];
    }
}
