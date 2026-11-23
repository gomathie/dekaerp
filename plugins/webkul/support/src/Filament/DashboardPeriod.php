<?php

namespace Webkul\Support\Filament;

use Illuminate\Support\Carbon;

/**
 * The period a dashboard trend compares with, in one place (DB-1,
 * 2026-11-22).
 *
 * Sales and Purchases each had their own copy, and both took
 * `diffInDays() + 1` as the length. A period runs start-of-day to
 * end-of-day, so that is 365.99… for a year, which Carbon rounds up: the
 * Sales comparison became "31 Dec – 30 Dec" instead of last year, and
 * Purchases compared with one day too many. Every dashboard calls this.
 */
class DashboardPeriod
{
    /**
     * The period every dashboard opens on: the year to date (user decision,
     * 2026-11-23, DB-4 / U68). Sales opened on the whole calendar year,
     * future months included, while Invoices and Purchases opened on the last
     * 30 days, so the same company saw different periods per tab, and small
     * companies saw empty dashboards.
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    public static function defaultRange(): array
    {
        return [now()->startOfYear(), now()->endOfDay()];
    }

    /**
     * The period a trend compares with.
     *
     * A period starting on 1 January and ending in the same year (the year
     * to date, or a whole year) compares with the same dates a year earlier
     * (user decision, 2026-11-23): "1 Jan – 23 Nov" against "1 Jan – 23 Nov
     * last year", not against the 327 days before it, which began in
     * February. Any other period compares with the same number of days
     * ending the day before it starts.
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    public static function previous(Carbon $start, Carbon $end): array
    {
        if ($start->isSameDay((clone $start)->startOfYear()) && $start->year === $end->year) {
            // NoOverflow: 29 February compares with 28 February.
            return [(clone $start)->subYearNoOverflow()->startOfDay(), (clone $end)->subYearNoOverflow()->endOfDay()];
        }

        $length = (int) floor($start->diffInDays($end)) + 1;

        $previousEnd = (clone $start)->subDay()->endOfDay();

        return [(clone $previousEnd)->subDays($length - 1)->startOfDay(), $previousEnd];
    }

    /**
     * "1 Oct – 31 Oct" within the current year, "1 Jan 2025 – 31 Dec 2025"
     * otherwise: a comparison with last year must say which year.
     */
    public static function label(Carbon $start, Carbon $end): string
    {
        $format = ($start->year === now()->year && $end->year === now()->year) ? 'j M' : 'j M Y';

        return $start->translatedFormat($format).' – '.$end->translatedFormat($format);
    }

    /**
     * How a chart groups a period (DB-4, 2026-11-23): by day up to 31 days,
     * by week up to about four months, by month beyond. A year drawn per day
     * turned ten payments into hairline spikes nobody could read (U75).
     */
    public static function granularity(Carbon $start, Carbon $end): string
    {
        $days = (int) floor($start->diffInDays($end)) + 1;

        return match (true) {
            $days <= 31  => 'day',
            $days <= 124 => 'week',
            default      => 'month',
        };
    }

    /**
     * Groups a day-by-day series (one value per day from $start) by the
     * period's granularity, with one label per group. Queries stay one row
     * per day, so nothing database-specific is needed; the sums are added
     * here. A week starts on Monday and is labelled by its first day inside
     * the period.
     *
     * @param  list<float>  $daily
     * @return array{labels: list<string>, values: list<float>}
     */
    public static function rollUp(array $daily, Carbon $start, Carbon $end): array
    {
        $unit = static::granularity($start, $end);
        $monthFormat = $start->year === $end->year ? 'M' : 'M Y';

        $labels = [];
        $values = [];
        $currentKey = null;
        $date = (clone $start)->startOfDay();

        foreach ($daily as $value) {
            $key = match ($unit) {
                'day'   => $date->toDateString(),
                'week'  => (clone $date)->startOfWeek()->toDateString(),
                default => $date->format('Y-m'),
            };

            if ($key !== $currentKey) {
                $currentKey = $key;
                $labels[] = $date->translatedFormat($unit === 'month' ? $monthFormat : 'j M');
                $values[] = 0.0;
            }

            $values[count($values) - 1] = round($values[count($values) - 1] + (float) $value, 2);

            $date->addDay();
        }

        return ['labels' => $labels, 'values' => $values];
    }

    /**
     * The periods offered as one-click buttons above a dashboard (DB-4
     * mockup, approved 2026-11-23). Anything else goes through the date
     * picker in Filters.
     *
     * @return array<string, array{0: Carbon, 1: Carbon}>
     */
    public static function presets(): array
    {
        return [
            'this-month'   => [now()->startOfMonth(), now()->endOfDay()],
            'last-30-days' => [now()->subDays(29)->startOfDay(), now()->endOfDay()],
            'year-to-date' => static::defaultRange(),
            'last-year'    => [now()->subYear()->startOfYear(), now()->subYear()->endOfYear()],
        ];
    }

    /** The preset a period matches, or null for a custom one. */
    public static function presetOf(Carbon $start, Carbon $end): ?string
    {
        foreach (static::presets() as $key => [$presetStart, $presetEnd]) {
            if ($start->isSameDay($presetStart) && $end->isSameDay($presetEnd)) {
                return $key;
            }
        }

        return null;
    }

    /**
     * The locale a dashboard hands the browser's Intl date formatter.
     *
     * Plain "en" is US English there ("Monday, November 23", "01:29 AM"),
     * while the server writes "23 November" and "22:29", so the banner's
     * date and clock visibly changed format on every load (production check,
     * 2026-11-23). Our clients write day before month with a 24-hour clock,
     * as en-GB does; the other languages are already region-neutral.
     */
    public static function browserLocale(): string
    {
        $locale = str_replace('_', '-', app()->getLocale());

        return $locale === 'en' ? 'en-GB' : $locale;
    }
}
