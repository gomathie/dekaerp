<?php

namespace Webkul\Sale\Filament\Widgets;

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Filament\Widgets\Concerns\GuardsSaleDashboardWidget;
use Webkul\Sale\Filament\Widgets\Concerns\HasSaleDashboardFilters;
use Webkul\Sale\Models\Order;
use Webkul\Support\Filament\Widgets\DashboardChartWidget;

/**
 * What became of the quotations made in the period (DB-4 mockup, approved
 * 2026-11-23): became orders, still open, cancelled. The third breakdown on
 * the Sales tab, beside revenue by category and salespeople.
 */
class QuotationOutcomeChart extends DashboardChartWidget
{
    use GuardsSaleDashboardWidget, HasSaleDashboardFilters;

    protected static bool $isDiscovered = false;

    protected static bool $isLazy = true;

    protected int|string|array $columnSpan = ['default' => 'full', 'md' => 1, 'xl' => 2];

    protected ?string $maxHeight = '240px';

    /**
     * Seen by whoever may see "Top quotations": the same module switch and
     * that widget's own Shield permission. A permission of its own would
     * reach only the Admin role through erp:sync-permissions, and
     * GuardsDashboardWidget refuses a widget Shield cannot name.
     */
    protected static function getWidgetPermission(): ?string
    {
        $widget = FilamentShield::getWidgets()[TopQuotationsTable::class] ?? null;

        return $widget ? array_key_first($widget['permissions']) : null;
    }

    public function getHeading(): string|Htmlable|null
    {
        return __('sales::filament/widgets/sales-dashboard.quotation-outcome.heading');
    }

    public function getDescription(): string|Htmlable|null
    {
        return __('sales::filament/widgets/sales-dashboard.quotation-outcome.description');
    }

    /**
     * @return array{won: int, open: int, cancelled: int}
     */
    public function outcomes(): array
    {
        $query = Order::query();
        $state = $query->qualifyColumn('state');

        $byState = $this->applyFilters($query)
            ->toBase()
            ->selectRaw("{$state} as bucket_state, COUNT(*) as bucket_count")
            ->groupBy($state)
            ->pluck('bucket_count', 'bucket_state');

        $count = fn (OrderState ...$states): int => (int) collect($states)->sum(fn (OrderState $state): int => (int) ($byState[$state->value] ?? 0));

        return [
            'won'       => $count(OrderState::SALE),
            'open'      => $count(OrderState::DRAFT, OrderState::SENT),
            'cancelled' => $count(OrderState::CANCEL),
        ];
    }

    protected function getData(): array
    {
        $outcomes = $this->outcomes();

        return [
            'datasets' => [
                [
                    'label'           => __('sales::filament/widgets/sales-dashboard.quotation-outcome.count'),
                    'data'            => array_values($outcomes),
                    'backgroundColor' => [$this->brandColor('success'), $this->brandColor('warning'), $this->brandColor('gray', 400)],
                    'borderRadius'    => 8,
                    'borderSkipped'   => 'bottom',
                    'maxBarThickness' => 40,
                ],
            ],
            'labels' => [
                __('sales::filament/widgets/sales-dashboard.quotation-outcome.won'),
                __('sales::filament/widgets/sales-dashboard.quotation-outcome.open'),
                __('sales::filament/widgets/sales-dashboard.quotation-outcome.cancelled'),
            ],
        ];
    }

    protected function getOptions(): array
    {
        return $this->barChartOptions(3);
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
