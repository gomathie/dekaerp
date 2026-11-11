<?php

namespace Webkul\Support\Filament\Forms\Components;

use Filament\Schemas\Components\Section;

/**
 * The filter box at the top of a dashboard.
 *
 * Starts folded under a "Filters" header on every screen size (user,
 * 2026-11-11: first only on phones, where seven stacked filters took about
 * 640px; then everywhere, so the figures are the first thing seen). One
 * click opens it.
 */
class DashboardFilters
{
    /**
     * @param  array<int, mixed>  $fields
     * @param  array<string, int>  $columns
     */
    public static function make(array $fields, array $columns): Section
    {
        return static::apply(Section::make())
            ->schema($fields)
            ->columnSpanFull()
            ->columns($columns);
    }

    /** Give an existing filter section the folding behaviour. */
    public static function apply(Section $section): Section
    {
        return $section
            ->heading(__('support::filament/dashboard.filters'))
            ->collapsible()
            ->collapsed();
    }
}
