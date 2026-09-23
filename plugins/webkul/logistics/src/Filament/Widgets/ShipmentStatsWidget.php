<?php

namespace Webkul\Logistics\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Webkul\Logistics\Enums\ShipmentState;
use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Support\LogisticsAccess;

/**
 * Today's operational picture, in one query.
 *
 * Every figure comes from a single grouped count rather than one query per
 * stat. Nine separate counts would be nine round trips to a managed database
 * on every dashboard load, for every user, and the dashboard is the first
 * screen most people open.
 *
 * Rows are company-scoped by CompanyScope on the model; nothing here removes
 * that scope.
 */
class ShipmentStatsWidget extends BaseWidget
{
    use HasWidgetShield;

    protected ?string $pollingInterval = '60s';

    protected static ?int $sort = 1;

    protected static string $lang = 'logistics::filament/widgets/shipment-stats';

    protected static function getPagePermission(): ?string
    {
        return 'widget_logistics_shipment_stats_widget';
    }

    public static function canView(): bool
    {
        // Hidden entirely for a company that does not do logistics, like every
        // other Logistics screen.
        return LogisticsAccess::enabledForCurrent()
            && (auth()->user()?->can('view_any_logistics_shipment') ?? false);
    }

    protected function getHeading(): ?string
    {
        return __(static::$lang.'.heading');
    }

    protected function getStats(): array
    {
        $counts = $this->stateCounts();

        return [
            Stat::make(__(static::$lang.'.stats.created-today'), $this->createdToday())
                ->icon('heroicon-o-plus-circle'),

            Stat::make(__(static::$lang.'.stats.awaiting-pickup'), $counts[ShipmentState::AWAITING_PICKUP->value] ?? 0)
                ->icon('heroicon-o-clock')
                ->color('info'),

            Stat::make(__(static::$lang.'.stats.in-transit'), $counts[ShipmentState::IN_TRANSIT->value] ?? 0)
                ->icon('heroicon-o-truck')
                ->color('primary'),

            Stat::make(__(static::$lang.'.stats.out-for-delivery'), $counts[ShipmentState::OUT_FOR_DELIVERY->value] ?? 0)
                ->icon('heroicon-o-map-pin')
                ->color('warning'),

            Stat::make(__(static::$lang.'.stats.delivered-today'), $this->deliveredToday())
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make(__(static::$lang.'.stats.overdue'), $this->overdue())
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger'),

            Stat::make(__(static::$lang.'.stats.failed'), $counts[ShipmentState::FAILED_DELIVERY->value] ?? 0)
                ->icon('heroicon-o-x-circle')
                ->color('danger'),
        ];
    }

    /**
     * One grouped count for every state.
     *
     * @return array<string, int>
     */
    protected function stateCounts(): array
    {
        return Shipment::query()
            ->select('state', DB::raw('count(*) as aggregate'))
            ->groupBy('state')
            ->pluck('aggregate', 'state')
            ->map(fn ($count): int => (int) $count)
            ->all();
    }

    protected function createdToday(): int
    {
        return Shipment::query()->whereDate('created_at', today())->count();
    }

    protected function deliveredToday(): int
    {
        return Shipment::query()
            ->where('state', ShipmentState::DELIVERED)
            ->whereDate('actual_delivery_at', today())
            ->count();
    }

    /**
     * Past its expected delivery and still open.
     */
    protected function overdue(): int
    {
        return Shipment::query()
            ->whereNotNull('expected_delivery_at')
            ->where('expected_delivery_at', '<', now())
            ->whereNotIn('state', [
                ShipmentState::DELIVERED,
                ShipmentState::CANCELLED,
                ShipmentState::RETURNED,
            ])
            ->count();
    }
}
