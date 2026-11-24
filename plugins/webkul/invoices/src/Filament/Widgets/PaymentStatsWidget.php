<?php

namespace Webkul\Invoice\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Locked;
use Webkul\Account\Enums\PaymentType;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\PaymentResource;
use Webkul\Invoice\Filament\Widgets\Concerns\HasInvoiceDashboardFilters;
use Webkul\PluginManager\Support\PluginEntitlement;
use Webkul\Support\Filament\DashboardAccess;

/**
 * Money received and the number of payments, above the payments received
 * list (plan #9 R-9). No dashboard had these as cards, so this is the one
 * widget; which payments count is OverviewCashFlowChart's rule, so "Received"
 * equals the dashboard's "Money in" for the same period.
 *
 * Not discovered: it belongs to the list, not to a dashboard, and is still
 * registered with Livewire by the plugin's widget discovery.
 */
class PaymentStatsWidget extends BaseWidget
{
    // No 5-second refresh (Filament default): every open dashboard re-ran all
    // its queries 12 times a minute (Sentry DEKAERP-11). Filters reload it.
    protected ?string $pollingInterval = null;

    use HasInvoiceDashboardFilters;

    protected static bool $isDiscovered = false;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    /**
     * Names the period, as on the invoice list. Locked: only the page that
     * places it sets it.
     */
    #[Locked]
    public ?string $periodHeading = null;

    /**
     * Payment amounts, so the same people as the list itself (the Invoices
     * module and the payments permission, through the list's own resource),
     * and only with the user's money-figures switch on: a widget is reachable
     * through Livewire on its own, so hiding the list is not enough.
     */
    public static function canView(): bool
    {
        return PluginEntitlement::enabledForCurrent('invoices')
            && DashboardAccess::mayViewMoney()
            && PaymentResource::canViewAny();
    }

    public function getHeading(): ?string
    {
        return $this->periodHeading ?? __('invoices::filament/widgets/invoice-dashboard.payment-stats.heading');
    }

    protected function dashboardDateColumn(): string
    {
        return 'date';
    }

    /**
     * Already in company currency. Inbound payments are positive, so this sum
     * is the chart's sum of absolute values.
     */
    protected function dashboardValueColumn(): string
    {
        return 'amount_company_currency_signed';
    }

    protected function getColumns(): int
    {
        return 2;
    }

    protected function getStats(): array
    {
        $received = fn (Carbon $start, Carbon $end): Builder => OverviewCashFlowChart::recordedPayments(PaymentType::RECEIVE, $start, $end);

        return [
            $this->buildStat(
                __('invoices::filament/widgets/invoice-dashboard.payment-stats.received'),
                'heroicon-o-banknotes',
                'success',
                $received,
                money: true,
            ),
            $this->buildStat(
                __('invoices::filament/widgets/invoice-dashboard.payment-stats.count'),
                'heroicon-o-receipt-percent',
                'info',
                $received,
            ),
        ];
    }
}
