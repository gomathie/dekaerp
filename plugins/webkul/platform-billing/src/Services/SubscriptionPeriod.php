<?php

namespace Webkul\PlatformBilling\Services;

use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;

/**
 * Derives each boundary from the original local anchor (SAAS-09). Repeatedly
 * adding months to the previous end would drift after February or month-end.
 */
class SubscriptionPeriod
{
    public function boundary(DateTimeImmutable $anchor, int $intervalMonths, int $periodIndex): DateTimeImmutable
    {
        if (! in_array($intervalMonths, [1, 3, 6, 12], true) || $periodIndex < 0) {
            throw new InvalidArgumentException('Invalid subscription interval or period index.');
        }

        $year = (int) $anchor->format('Y');
        $month = (int) $anchor->format('n');
        $targetMonth = $year * 12 + $month - 1 + $intervalMonths * $periodIndex;
        $targetYear = intdiv($targetMonth, 12);
        $targetMonthOfYear = $targetMonth % 12 + 1;
        $first = $anchor->setDate($targetYear, $targetMonthOfYear, 1);
        $daysInTarget = (int) $first->format('t');
        $isMonthEnd = (int) $anchor->format('j') === (int) $anchor->format('t');
        $day = $isMonthEnd ? $daysInTarget : min((int) $anchor->format('j'), $daysInTarget);

        return $first->setDate($targetYear, $targetMonthOfYear, $day);
    }

    /** @return array{start: DateTimeImmutable, end: DateTimeImmutable} */
    public function period(DateTimeImmutable $anchor, int $intervalMonths, int $periodIndex): array
    {
        $start = $this->boundary($anchor, $intervalMonths, $periodIndex);
        $end = $this->boundary($anchor, $intervalMonths, $periodIndex + 1);
        $utc = new DateTimeZone('UTC');

        return ['start' => $start->setTimezone($utc), 'end' => $end->setTimezone($utc)];
    }
}
