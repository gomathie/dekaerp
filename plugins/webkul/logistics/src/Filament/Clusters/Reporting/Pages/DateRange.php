<?php

namespace Webkul\Logistics\Filament\Clusters\Reporting\Pages;

use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

/**
 * The from/to filter every report shares.
 *
 * Five reports filter on a date range, and the only thing that differs is which
 * column. Written once so all five behave the same way - in particular so "to"
 * always means the whole of that day, which is what a person filling in a report
 * filter means by it and what a naive `<=` on a timestamp column does not give.
 */
class DateRange
{
    /**
     * @return array<int, DatePicker>
     */
    public static function components(string $lang): array
    {
        return [
            DatePicker::make('from')->label(__($lang.'.filters.from')),
            DatePicker::make('until')->label(__($lang.'.filters.until')),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function apply(Builder $query, array $data, string $column): Builder
    {
        return $query
            ->when(
                $data['from'] ?? null,
                fn (Builder $query, $date): Builder => $query->whereDate($column, '>=', $date),
            )
            ->when(
                $data['until'] ?? null,
                // whereDate, not <=: comparing a timestamp column to a bare date
                // silently excludes everything after midnight on the closing day.
                fn (Builder $query, $date): Builder => $query->whereDate($column, '<=', $date),
            );
    }

    /**
     * What the active range should say in the filter indicator.
     *
     * @param  array<string, mixed>  $data
     */
    public static function indicator(array $data, string $lang): ?string
    {
        $from = $data['from'] ?? null;
        $until = $data['until'] ?? null;

        return match (true) {
            $from && $until => __($lang.'.filters.between', ['from' => $from, 'until' => $until]),
            (bool) $from    => __($lang.'.filters.since', ['from' => $from]),
            (bool) $until   => __($lang.'.filters.up-to', ['until' => $until]),
            default         => null,
        };
    }
}
