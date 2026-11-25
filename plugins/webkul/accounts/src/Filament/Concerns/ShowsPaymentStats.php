<?php

namespace Webkul\Account\Filament\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Support\RecordedPayments;
use Webkul\Support\Filament\Concerns\HasDashboardMetrics;

/**
 * Money received and the number of payments, as cards above a payments
 * received list (plan #9 R-9): Invoices' list and Accounting's. Shared so
 * both read RecordedPayments and show the same figures; each widget keeps
 * its own canView(), because each list has its own module and permission.
 *
 * The widget supplies paymentStatsLabel(): its strings belong to its plugin.
 */
trait ShowsPaymentStats
{
    use HasDashboardMetrics;

    /** One of 'heading', 'received', 'count'. */
    abstract protected function paymentStatsLabel(string $key): string;

    public function getHeading(): ?string
    {
        return $this->periodHeading ?? $this->paymentStatsLabel('heading');
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
        $received = fn (Carbon $start, Carbon $end): Builder => RecordedPayments::between(PaymentType::RECEIVE, $start, $end);

        return [
            $this->buildStat($this->paymentStatsLabel('received'), 'heroicon-o-banknotes', 'success', $received, money: true),
            $this->buildStat($this->paymentStatsLabel('count'), 'heroicon-o-receipt-percent', 'info', $received),
        ];
    }
}
