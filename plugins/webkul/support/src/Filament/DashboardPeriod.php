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
     * The period of the same number of days ending the day before $start.
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    public static function previous(Carbon $start, Carbon $end): array
    {
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
