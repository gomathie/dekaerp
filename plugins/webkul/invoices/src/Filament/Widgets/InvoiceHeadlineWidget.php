<?php

namespace Webkul\Invoice\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Invoice\Filament\Pages\Invoices;
use Webkul\Invoice\Filament\Widgets\Concerns\HasInvoiceDashboardFilters;

/**
 * The Invoices card in the Overview's "At a glance" row (user, 2026-11-11:
 * relevant figures from every part first, without scrolling). What is owed
 * to the company and what it owes, in company currency, with overdue counts.
 *
 * Visible exactly when the Invoices section is (its dashboard's canAccess()).
 * Not a Shield widget: a new widget permission would reach only the Admin
 * role through erp:sync-permissions, so tenant roles would never see it.
 */
class InvoiceHeadlineWidget extends StatsOverviewWidget
{
    // No 5-second refresh (Filament default): every open dashboard re-ran all
    // its queries 12 times a minute (Sentry DEKAERP-11). Filters reload it.
    protected ?string $pollingInterval = null;

    use HasInvoiceDashboardFilters;

    protected static bool $isLazy = false;

    protected static bool $isDiscovered = false;

    protected int|string|array $columnSpan = ['default' => 'full', 'lg' => 1];

    public static function canView(): bool
    {
        return Invoices::canAccess();
    }

    public function getHeading(): ?string
    {
        return __('invoices::filament/pages/overview.headline.invoices');
    }

    protected function getColumns(): int
    {
        return 2;
    }

    /**
     * This section's figures for the Overview's combined "At a glance"
     * row, computed with the page filters handed in.
     *
     * @param  array<string, mixed>  $filters
     * @return array<int, Stat>
     */
    public function headlineStats(array $filters): array
    {
        $this->pageFilters = $filters;

        return $this->getStats();
    }

    protected function getStats(): array
    {
        $currency = current_company()?->currency?->name;

        $receivable = $this->open($this->customerInvoices());
        $payable = $this->open($this->vendorBills());

        return [
            Stat::make(__('invoices::filament/pages/overview.headline.to-collect'), compact_money($this->owed($receivable), $currency))
                ->icon('heroicon-o-arrow-down-tray')
                ->description(trans_choice('invoices::filament/pages/overview.headline.overdue', $overdue = $this->overdue($receivable), ['count' => $overdue]))
                ->color($overdue > 0 ? 'warning' : 'success')
                ->extraAttributes(['class' => 'overview-headline-stat']),

            Stat::make(__('invoices::filament/pages/overview.headline.to-pay'), compact_money($this->owed($payable), $currency))
                ->icon('heroicon-o-arrow-up-tray')
                ->description(trans_choice('invoices::filament/pages/overview.headline.overdue', $overdueBills = $this->overdue($payable), ['count' => $overdueBills]))
                ->color($overdueBills > 0 ? 'danger' : 'success')
                ->extraAttributes(['class' => 'overview-headline-stat']),
        ];
    }

    /** Posted documents not yet fully paid. */
    protected function open(Builder $query): Builder
    {
        return $query->whereIn($query->qualifyColumn('payment_state'), ['not_paid', 'partial']);
    }

    /** Remaining amount in company currency (bills are signed negative). */
    protected function owed(Builder $query): float
    {
        return abs((float) (clone $query)->sum($query->qualifyColumn('amount_residual_signed')));
    }

    protected function overdue(Builder $query): int
    {
        return (clone $query)->whereDate($query->qualifyColumn('invoice_date_due'), '<', now()->toDateString())->count();
    }
}
