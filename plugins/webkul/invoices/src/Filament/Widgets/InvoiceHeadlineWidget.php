<?php

namespace Webkul\Invoice\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\InvoiceResource;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource;
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
                ->url(InvoiceResource::canViewAny() ? InvoiceResource::getUrl('index') : null)
                ->icon('heroicon-o-arrow-down-tray')
                ->description(trans_choice('invoices::filament/pages/overview.headline.overdue', $overdue = $this->overdue($receivable), ['count' => $overdue]))
                ->color($overdue > 0 ? 'warning' : 'success')
                ->extraAttributes(['class' => 'overview-headline-stat']),

            Stat::make(__('invoices::filament/pages/overview.headline.to-pay'), compact_money($this->owed($payable), $currency))
                ->url(BillResource::canViewAny() ? BillResource::getUrl('index') : null)
                ->icon('heroicon-o-arrow-up-tray')
                ->description(trans_choice('invoices::filament/pages/overview.headline.overdue', $overdueBills = $this->overdue($payable), ['count' => $overdueBills]))
                ->color($overdueBills > 0 ? 'danger' : 'success')
                ->extraAttributes(['class' => 'overview-headline-stat']),
        ];
    }

    /**
     * The Invoices panel on the Overview's All tab (mockup approved
     * 2026-11-13): the period's invoices split into paid, still open and
     * overdue (company currency), and the open invoices to act on in three
     * lists. The lists are today's state, not the date filter: an invoice
     * overdue since last quarter still needs chasing.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function panel(array $filters): array
    {
        $this->pageFilters = $filters;

        $lang = 'invoices::filament/pages/overview.board';
        $currency = current_company()?->currency?->name;
        $today = now()->toDateString();

        $period = $this->customerInvoices();

        $paid = abs((float) (clone $period)->where($period->qualifyColumn('payment_state'), 'paid')->sum($period->qualifyColumn('amount_total_signed')));
        $openPeriod = $this->open(clone $period);
        $overdue = abs((float) (clone $openPeriod)->whereDate($period->qualifyColumn('invoice_date_due'), '<', $today)->sum($period->qualifyColumn('amount_residual_signed')));
        $notDue = abs((float) (clone $openPeriod)->where(fn (Builder $q) => $q
            ->whereNull($period->qualifyColumn('invoice_date_due'))
            ->orWhereDate($period->qualifyColumn('invoice_date_due'), '>=', $today))
            ->sum($period->qualifyColumn('amount_residual_signed')));

        $total = $paid + $notDue + $overdue;
        $share = fn (float $amount): int => $total > 0 ? (int) round($amount / $total * 100) : 0;

        $openNow = fn (): Builder => $this->open(
            \Webkul\Invoice\Models\Invoice::query()
                ->where('move_type', \Webkul\Account\Enums\MoveType::OUT_INVOICE)
                ->where('state', \Webkul\Account\Enums\MoveState::POSTED)
        );

        $lists = [
            'overdue' => $openNow()->whereDate('invoice_date_due', '<', $today)->orderBy('invoice_date_due'),
            'soon'    => $openNow()->whereDate('invoice_date_due', '>=', $today)->whereDate('invoice_date_due', '<=', now()->addDays(7)->toDateString())->orderBy('invoice_date_due'),
            'open'    => $openNow()->orderBy('invoice_date_due'),
        ];

        $rows = [];
        $counts = [];

        foreach ($lists as $key => $query) {
            $counts[$key] = (clone $query)->count();
            $rows[$key] = $query->with('partner:id,name')->limit(5)->get()
                ->map(fn ($invoice): array => [
                    'name'   => $invoice->partner?->name ?? $invoice->name,
                    'detail' => $this->dueLabel($invoice->invoice_date_due),
                    'late'   => $invoice->invoice_date_due !== null && $invoice->invoice_date_due->lt(now()->startOfDay()),
                    'amount' => number_format(abs((float) $invoice->amount_residual_signed), 2),
                    'url'    => InvoiceResource::canView($invoice) ? InvoiceResource::getUrl('view', ['record' => $invoice]) : null,
                ])->all();
        }

        return [
            'label' => __('invoices::filament/pages/overview.sections.invoices'),
            'url'   => InvoiceResource::canViewAny() ? InvoiceResource::getUrl('index') : null,
            'split' => [
                ['label' => __($lang.'.paid'), 'value' => compact_money($paid, $currency), 'percent' => $share($paid), 'tone' => 'success'],
                ['label' => __($lang.'.open'), 'value' => compact_money($notDue, $currency), 'percent' => $share($notDue), 'tone' => 'warning'],
                ['label' => __($lang.'.overdue'), 'value' => compact_money($overdue, $currency), 'percent' => $share($overdue), 'tone' => 'danger'],
            ],
            'list' => [
                'currency' => $currency,
                'tabs'     => [
                    'overdue' => ['label' => __($lang.'.tab-overdue'), 'count' => $counts['overdue']],
                    'soon'    => ['label' => __($lang.'.tab-soon'), 'count' => $counts['soon']],
                    'open'    => ['label' => __($lang.'.tab-open'), 'count' => $counts['open']],
                ],
                'rows' => $rows,
            ],
        ];
    }

    protected function dueLabel(mixed $due): string
    {
        $lang = 'invoices::filament/pages/overview.board';

        if ($due === null) {
            return __($lang.'.no-due-date');
        }

        $days = (int) now()->startOfDay()->diffInDays($due->copy()->startOfDay(), false);

        return match (true) {
            $days < 0  => trans_choice($lang.'.days-late', -$days, ['count' => -$days]),
            $days === 0 => __($lang.'.due-today'),
            default    => __($lang.'.due-on', ['date' => $due->translatedFormat('j M')]),
        };
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
