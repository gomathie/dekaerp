<?php

namespace Webkul\Invoice\Filament\Clusters\Customers\Resources\PaymentResource\Pages;

use Illuminate\Database\Eloquent\Builder;
use Webkul\Account\Filament\Resources\PaymentResource\Pages\ListPayments as BaseListPayments;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\PaymentResource;
use Webkul\Invoice\Filament\Widgets\PaymentStatsWidget;
use Webkul\TableViews\Filament\Components\PresetView;

class ListPayments extends BaseListPayments
{
    protected static string $resource = PaymentResource::class;

    /**
     * This month's money received above the payments list (plan #9 R-9),
     * like the invoice list. Read with the Overview chart's rule, so the list
     * and the dashboard agree; the widget's guard decides who sees it.
     */
    public function getHeaderWidgets(): array
    {
        return [
            PaymentStatsWidget::make([
                'pageFilters'   => ['start_date' => now()->startOfMonth()->toDateString(), 'end_date' => now()->toDateString()],
                'periodHeading' => __('invoices::filament/widgets/invoice-dashboard.invoice-stats.this-month'),
            ]),
        ];
    }

    public function getPresetTableViews(): array
    {
        $presets = parent::getPresetTableViews();

        return [
            ...$presets,
            'customer_payments' => PresetView::make(__('Customer Payments'))
                ->favorite()
                ->setAsDefault()
                ->icon('heroicon-s-banknotes')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('partner_type', ['customer', 'company'])),
        ];
    }
}
