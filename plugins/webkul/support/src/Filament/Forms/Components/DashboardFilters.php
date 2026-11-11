<?php

namespace Webkul\Support\Filament\Forms\Components;

use Filament\Schemas\Components\Section;

/**
 * The filter box at the top of a dashboard.
 *
 * On phones it starts folded under a "Filters" header (user, 2026-11-11:
 * dashboards were 4–5 screens long on a phone, and the seven stacked
 * filters alone took about 640px). On wider screens it opens as before.
 * The width is decided in the browser, because the server cannot know it.
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
            ->extraAlpineAttributes([
                'x-init' => "if (window.matchMedia('(max-width: 767px)').matches) { isCollapsed = true }",
            ]);
    }
}
