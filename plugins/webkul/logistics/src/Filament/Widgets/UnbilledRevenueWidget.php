<?php

namespace Webkul\Logistics\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Webkul\Logistics\Models\ShipmentCharge;
use Webkul\Logistics\Support\LogisticsAccess;

/**
 * Money earned but not yet invoiced.
 *
 * Gated on `view_financials_logistics_shipment`, not on the shipment list
 * permission: an operations user who may see shipments has no business seeing
 * what the company is owed. Same pairing as the UnbilledCharges page, which
 * this links to in spirit.
 *
 * Totalled per currency rather than summed across all of them - a single
 * figure mixing currencies is a meaningless number that looks authoritative.
 */
class UnbilledRevenueWidget extends BaseWidget
{
    use HasWidgetShield;

    protected ?string $pollingInterval = '60s';

    protected static ?int $sort = 3;

    protected static string $lang = 'logistics::filament/widgets/unbilled-revenue';

    protected static function getPagePermission(): ?string
    {
        return 'widget_logistics_unbilled_revenue_widget';
    }

    public static function canView(): bool
    {
        return LogisticsAccess::enabledForCurrent()
            && (auth()->user()?->can('view_financials_logistics_shipment') ?? false);
    }

    protected function getHeading(): ?string
    {
        return __(static::$lang.'.heading');
    }

    protected function getStats(): array
    {
        // One grouped query: sum per currency, not a query per currency.
        $totals = ShipmentCharge::query()
            ->uninvoiced()
            ->select('currency_id', DB::raw('sum(subtotal) as aggregate'))
            ->groupBy('currency_id')
            ->with('currency:id,name')
            ->get();

        if ($totals->isEmpty()) {
            return [
                Stat::make(__(static::$lang.'.stats.unbilled'), '0')
                    ->description(__(static::$lang.'.stats.nothing-outstanding'))
                    ->icon('heroicon-o-banknotes')
                    ->color('success'),
            ];
        }

        return $totals
            ->map(fn ($row): Stat => Stat::make(
                __(static::$lang.'.stats.unbilled'),
                number_format((float) $row->aggregate, 2),
            )
                ->description($row->currency?->name ?? __(static::$lang.'.stats.unknown-currency'))
                ->icon('heroicon-o-banknotes')
                ->color('warning'))
            ->all();
    }
}
