<?php

namespace Webkul\Support\Filament\Forms\Components;

use Filament\Schemas\Components\Grid;

/**
 * The fields inside a dashboard's Filters panel.
 *
 * *Superseded 2026-11-25 (user, approved mockup):* this was a full-width
 * section folded under a "Filters" header (2026-11-11: seven stacked filters
 * took about 640px on a phone). The fields now live in the panel that the
 * "Filters" button of the period bar opens (`HasDashboardFilterBar`), so the
 * period, the dates and the filters sit on one row. The panel is narrow, so
 * it sets its own columns: a page's xl column count would squeeze six fields
 * into a 40rem panel.
 */
class DashboardFilters
{
    /**
     * @param  array<int, mixed>  $fields
     */
    public static function make(array $fields): Grid
    {
        return Grid::make(['default' => 1, 'sm' => 2])
            ->schema($fields)
            ->columnSpanFull();
    }
}
