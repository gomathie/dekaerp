<?php

namespace Webkul\Invoice\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Webkul\Invoice\Filament\Widgets\Concerns\GuardsInvoiceDashboardWidget;
use Webkul\Invoice\Filament\Widgets\Concerns\HasInvoiceDashboardFilters;

class BillStatsWidget extends BaseWidget
{
    // No 5-second refresh (Filament default): every open dashboard re-ran all
    // its queries 12 times a minute (Sentry DEKAERP-11). Filters reload it.
    protected ?string $pollingInterval = null;

    use GuardsInvoiceDashboardWidget, HasInvoiceDashboardFilters;

    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    public function getHeading(): ?string
    {
        return __('invoices::filament/widgets/invoice-dashboard.bill-stats.heading');
    }

    protected function getStats(): array
    {
        return [
            $this->buildStat(
                __('invoices::filament/widgets/invoice-dashboard.bill-stats.total'),
                'heroicon-o-currency-dollar',
                'info',
                fn (?Carbon $start, ?Carbon $end): Builder => $this->scope($start, $end),
                money: true,
                abs: true,
            ),
            $this->buildStat(
                __('invoices::filament/widgets/invoice-dashboard.bill-stats.count'),
                'heroicon-o-document-text',
                'primary',
                fn (?Carbon $start, ?Carbon $end): Builder => $this->scope($start, $end),
            ),
            $this->buildStat(
                __('invoices::filament/widgets/invoice-dashboard.bill-stats.paid'),
                'heroicon-o-check-circle',
                'success',
                fn (?Carbon $start, ?Carbon $end): Builder => $this->stateScope('paid', $start, $end),
            ),
            $this->buildStat(
                __('invoices::filament/widgets/invoice-dashboard.bill-stats.unpaid'),
                'heroicon-o-exclamation-circle',
                'danger',
                fn (?Carbon $start, ?Carbon $end): Builder => $this->stateScope('not_paid', $start, $end),
            ),
        ];
    }

    protected function scope(?Carbon $start = null, ?Carbon $end = null): Builder
    {
        return $this->vendorBills($start, $end);
    }

    protected function stateScope(string $state, ?Carbon $start = null, ?Carbon $end = null): Builder
    {
        $query = $this->scope($start, $end);

        return $query->where($query->qualifyColumn('payment_state'), $state);
    }
}
