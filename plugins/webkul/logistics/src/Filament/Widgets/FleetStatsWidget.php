<?php

namespace Webkul\Logistics\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Webkul\Logistics\Enums\TripState;
use Webkul\Logistics\Models\Driver;
use Webkul\Logistics\Models\Trip;
use Webkul\Logistics\Models\Vehicle;
use Webkul\Logistics\Support\LogisticsAccess;

/**
 * What is on the road, and what is free to send.
 *
 * "Available" means active and not already on a trip that is out. A driver
 * with an expired licence is counted as unavailable, because DispatchService
 * refuses to dispatch them - a dashboard that counted them free would send a
 * dispatcher looking for a driver they cannot use.
 */
class FleetStatsWidget extends BaseWidget
{
    use HasWidgetShield;

    protected ?string $pollingInterval = '60s';

    protected static ?int $sort = 2;

    protected static string $lang = 'logistics::filament/widgets/fleet-stats';

    protected static function getPagePermission(): ?string
    {
        return 'widget_logistics_fleet_stats_widget';
    }

    public static function canView(): bool
    {
        return LogisticsAccess::enabledForCurrent()
            && (auth()->user()?->can('view_any_logistics_trip') ?? false);
    }

    protected function getHeading(): ?string
    {
        return __(static::$lang.'.heading');
    }

    protected function getStats(): array
    {
        $tripCounts = Trip::query()
            ->select('state', DB::raw('count(*) as aggregate'))
            ->groupBy('state')
            ->pluck('aggregate', 'state')
            ->map(fn ($count): int => (int) $count)
            ->all();

        $onRoad = ($tripCounts[TripState::DISPATCHED->value] ?? 0)
            + ($tripCounts[TripState::IN_PROGRESS->value] ?? 0);

        return [
            Stat::make(__(static::$lang.'.stats.active-trips'), $onRoad)
                ->icon('heroicon-o-map')
                ->color('primary'),

            Stat::make(__(static::$lang.'.stats.planned-trips'), $tripCounts[TripState::PLANNED->value] ?? 0)
                ->icon('heroicon-o-calendar'),

            Stat::make(__(static::$lang.'.stats.available-vehicles'), $this->availableVehicles())
                ->icon('heroicon-o-truck')
                ->color('success'),

            Stat::make(__(static::$lang.'.stats.available-drivers'), $this->availableDrivers())
                ->icon('heroicon-o-identification')
                ->color('success'),
        ];
    }

    protected function availableVehicles(): int
    {
        return Vehicle::query()
            ->where('is_active', true)
            ->whereDoesntHave('trips', fn ($query) => $query->whereIn('state', [
                TripState::DISPATCHED,
                TripState::IN_PROGRESS,
            ]))
            ->count();
    }

    protected function availableDrivers(): int
    {
        return Driver::query()
            ->where('is_active', true)
            // Matches DispatchService::assertDispatchable(): an expired licence
            // blocks dispatch, so such a driver is not available.
            ->where(fn ($query) => $query
                ->whereNull('license_expires_at')
                ->orWhereDate('license_expires_at', '>=', today()))
            ->whereDoesntHave('trips', fn ($query) => $query->whereIn('state', [
                TripState::DISPATCHED,
                TripState::IN_PROGRESS,
            ]))
            ->count();
    }
}
