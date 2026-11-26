<?php

namespace Webkul\Support\Filament\Forms\Components;

use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Component;
use Webkul\Support\Filament\DashboardPeriod;

/**
 * The two filter keys a dashboard's widgets read its period from.
 *
 * Dates are set only from the period bar (user, 2026-11-25: "if these 2 are
 * basically doing the same thing, why there"). The Filters panel used to
 * carry a date-range picker too, so a dashboard had two controls for one
 * value. The keys stay in the filters form as hidden fields, so the form
 * still fills them with the default period on first load and every widget
 * keeps reading the same names.
 */
class DashboardDateRange
{
    /**
     * @return array<int, Component>
     */
    public static function keys(string $startKey = 'startDate', string $endKey = 'endDate'): array
    {
        [$start, $end] = DashboardPeriod::defaultRange();

        return [
            // The year to date, the default of every dashboard (U68).
            Hidden::make($startKey)->default($start->toDateString()),

            Hidden::make($endKey)->default($end->toDateString()),
        ];
    }
}
