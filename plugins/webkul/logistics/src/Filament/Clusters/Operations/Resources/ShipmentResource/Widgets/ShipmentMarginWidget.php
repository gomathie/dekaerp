<?php

namespace Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Webkul\Logistics\Enums\ExpenseState;
use Webkul\Logistics\Models\Shipment;

/**
 * Revenue less cost for one shipment (D2).
 *
 * Behind view_financials, like the charges and the unbilled figures: an
 * operations user who may see a shipment has no business seeing what it earns.
 *
 * Two aggregate queries, not a loop over rows, and both filtered to what the
 * company has actually committed to:
 *
 *  - revenue is billable charges, whether or not they are invoiced yet;
 *  - cost is approved and billed expenses only. A draft or submitted expense is
 *    a claim someone has made, not money the company has agreed to pay, and
 *    counting it would make the margin swing on paperwork rather than on work.
 *
 * No currency conversion. Charges and expenses are recorded in the shipment's
 * currency, and a figure that silently mixed currencies would look authoritative
 * while being meaningless.
 */
class ShipmentMarginWidget extends BaseWidget
{
    public ?Model $record = null;

    protected static string $lang = 'logistics::expenses.margin';

    public static function canView(): bool
    {
        // The per-record check is in getStats(): this one only decides whether
        // the widget class is offered at all.
        return Auth::user()?->can('view_financials_logistics_shipment') ?? false;
    }

    protected function getHeading(): ?string
    {
        return __(static::$lang.'.heading');
    }

    protected function getStats(): array
    {
        $shipment = $this->record;

        if (! $shipment instanceof Shipment || ! (Auth::user()?->can('viewFinancials', $shipment) ?? false)) {
            return [];
        }

        $revenue = (float) $shipment->charges()->where('is_billable', true)->sum('subtotal');

        $costs = (float) $shipment->expenses()
            ->whereIn('state', [ExpenseState::APPROVED->value, ExpenseState::BILLED->value])
            ->sum('amount');

        $margin = $revenue - $costs;

        return [
            Stat::make(__(static::$lang.'.revenue'), number_format($revenue, 2))
                ->icon('heroicon-o-banknotes')
                ->color('success'),

            Stat::make(__(static::$lang.'.costs'), number_format($costs, 2))
                ->icon('heroicon-o-arrow-trending-down')
                ->color('warning'),

            Stat::make(__(static::$lang.'.margin'), number_format($margin, 2))
                ->description(__(static::$lang.'.helper'))
                ->icon('heroicon-o-calculator')
                // A shipment costing more than it earns is worth seeing as red
                // on the page, not worked out from two other numbers.
                ->color($margin < 0 ? 'danger' : 'success'),
        ];
    }
}
