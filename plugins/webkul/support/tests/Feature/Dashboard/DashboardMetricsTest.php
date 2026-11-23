<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Webkul\Partner\Models\Partner;
use Webkul\Purchase\Filament\Admin\Widgets\Concerns\HasPurchaseDashboardFilters;
use Webkul\Sale\Filament\Widgets\Concerns\HasSaleDashboardFilters;
use Webkul\Support\Filament\Concerns\HasDashboardMetrics;
use Webkul\Support\Filament\DashboardPeriod;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensureERPInstalled();
});

/**
 * A bare host for the trait, so its protected methods can be exercised
 * without a Filament page around them.
 *
 * @param  array<string, mixed>  $filters
 */
function dashboardMetricsHost(array $filters = []): object
{
    $host = new class
    {
        use HasDashboardMetrics;

        public function call(string $method, mixed ...$arguments): mixed
        {
            return $this->{$method}(...$arguments);
        }
    };

    $host->pageFilters = $filters;

    return $host;
}

it('reports no change when both periods are empty', function () {
    expect(dashboardMetricsHost()->call('statTrend', 0.0, 0.0))
        ->toBe(['0%', 'heroicon-m-minus', 'gray']);
});

/**
 * Inverted 2026-11-22 (dashboard plan DB-1, D-DB-1): this used to pin "+100%"
 * in green, which made a first sale look like a doubling. An empty previous
 * period is now "New", neutral.
 */
it('reports a new figure, neutrally, when the previous period was empty', function () {
    expect(dashboardMetricsHost()->call('statTrend', 5.0, 0.0))
        ->toBe([__('support::support.trend.new'), 'heroicon-m-sparkles', 'gray']);
});

it('reports a drop as a negative trend with its percentage', function () {
    expect(dashboardMetricsHost()->call('statTrend', 75.0, 100.0))
        ->toBe(['25%', 'heroicon-m-arrow-trending-down', 'danger']);
});

it('shows the real change, neutrally, on a base of fewer than five records', function () {
    expect(dashboardMetricsHost()->call('statTrend', 5.0, 2.0))
        ->toBe(['+3', 'heroicon-m-arrow-trending-up', 'gray'])
        // A money figure with a small record count says so explicitly.
        ->and(dashboardMetricsHost()->call('statTrend', 900.0, 300.0, false, 2.0, true)[2])->toBe('gray')
        // Without a record count, money keeps the percentage rule.
        ->and(dashboardMetricsHost()->call('statTrend', 900.0, 300.0, false, null, true))
        ->toBe(['200%', 'heroicon-m-arrow-trending-up', 'success']);
});

it('colours a rise red and a fall green when lower is better', function () {
    expect(dashboardMetricsHost()->call('statTrend', 120.0, 100.0, true))
        ->toBe(['20%', 'heroicon-m-arrow-trending-up', 'danger'])
        ->and(dashboardMetricsHost()->call('statTrend', 80.0, 100.0, true))
        ->toBe(['20%', 'heroicon-m-arrow-trending-down', 'success']);
});

/**
 * The previous period must be exactly as long as the chosen one and end the
 * day before it starts, or every "% change" compares unequal spans.
 */
it('compares against a previous period of the same length ending the day before', function () {
    $host = dashboardMetricsHost(['start_date' => '2026-03-01', 'end_date' => '2026-03-31']);

    [$previousStart, $previousEnd] = $host->call('previousPeriodRange');

    expect($previousEnd->toDateString())->toBe('2026-02-28')
        ->and($previousStart->toDateString())->toBe('2026-01-29')
        ->and((int) floor($previousStart->diffInDays($previousEnd)) + 1)->toBe(31);
});

/**
 * Upstream loaded every row of the period and grouped them in PHP. The port
 * asks for one grouped row per day, so the series costs a single query, and
 * it must still respect the company boundary.
 */
it('builds the daily series from one grouped query within the active company', function () {
    $company = CompanyHelper::company();
    $otherCompany = CompanyHelper::company();

    $create = function ($companyId, string $day, int $count = 1): void {
        Partner::factory()->count($count)->create(['company_id' => $companyId])
            ->each(fn (Partner $partner) => $partner->forceFill(['created_at' => Carbon::parse($day.' 10:00')])->saveQuietly());
    };

    // Another client's rows exist before this user signs in: since UF-025
    // (2026-11-16) a company A user may not write into company B.
    $create($otherCompany->id, '2026-03-01', 5);

    CompanyHelper::actingAsCompanyUser($company);

    $create($company->id, '2026-03-01', 2);
    $create($company->id, '2026-03-03');

    $host = dashboardMetricsHost(['start_date' => '2026-03-01', 'end_date' => '2026-03-04']);

    DB::flushQueryLog();
    DB::enableQueryLog();

    $series = $host->call('dailyBuckets', Partner::query(), false);

    $queries = collect(DB::getQueryLog())->filter(fn (array $entry): bool => str_contains($entry['query'], 'partners_partners'));

    DB::disableQueryLog();

    expect($series)->toBe([2.0, 0.0, 1.0, 0.0])
        ->and($queries)->toHaveCount(1);
});

/**
 * DB-1 (2026-11-22): each trend names the period it compares with. March's
 * 31 days compare with the 31 days before.
 */
it('names the period a trend is compared with', function () {
    $this->travelTo(Carbon::parse('2026-04-15'));

    $host = dashboardMetricsHost(['start_date' => '2026-03-01', 'end_date' => '2026-03-31']);

    expect($host->call('baselineLabel'))->toBe('29 Jan – 28 Feb');
});

/**
 * Production, 2026-11-22: the Sales dashboard's default period is the whole
 * year, and its own copy of this rule rounded 365.99 days up, so the trend
 * read "vs 31 Dec – 30 Dec". Sales and Purchases now call DashboardPeriod.
 */
it('compares a whole year with the whole of the year before, naming the year', function () {
    $this->travelTo(Carbon::parse('2026-11-22 10:00'));

    [$previousStart, $previousEnd] = DashboardPeriod::previous(now()->startOfYear(), now()->endOfYear());

    expect($previousStart->toDateTimeString())->toBe('2025-01-01 00:00:00')
        ->and($previousEnd->toDateString())->toBe('2025-12-31')
        ->and(DashboardPeriod::label($previousStart, $previousEnd))->toBe('1 Jan 2025 – 31 Dec 2025');
});

/**
 * Inverted 2026-11-23 (user decision, DB-4 / U68): the Sales dashboard no
 * longer opens on the whole calendar year but on the year to date, so with
 * no filter its previous period is the same days of last year
 * (1 Jan – 22 Nov 2025), not all of 2025.
 */
it('gives the sales and purchases dashboards the same previous period as the shared rule', function () {
    $this->travelTo(Carbon::parse('2026-11-22 10:00'));

    $sales = new class
    {
        use HasSaleDashboardFilters;

        public function previous(): array
        {
            return $this->previousPeriodRange();
        }
    };

    $purchases = new class
    {
        use HasPurchaseDashboardFilters;

        public function previous(): array
        {
            return $this->previousPeriodRange();
        }
    };

    $purchases->pageFilters = ['start_date' => '2026-03-01', 'end_date' => '2026-03-31'];

    expect(array_map(fn (Carbon $date): string => $date->toDateString(), $sales->previous()))->toBe(['2025-01-01', '2025-11-22'])
        ->and(array_map(fn (Carbon $date): string => $date->toDateString(), $purchases->previous()))->toBe(['2026-01-29', '2026-02-28']);
});

/**
 * DB-4 (user decision, 2026-11-23, U68): every dashboard opens on the year
 * to date and compares it with the same days of last year. Sales opened on
 * the whole calendar year and the others on the last 30 days.
 */
it('opens every dashboard on the year to date, compared with the same days last year', function () {
    $this->travelTo(Carbon::parse('2026-11-23 10:00'));

    $dates = fn (array $range): array => array_map(fn (Carbon $date): string => $date->toDateString(), $range);

    $shared = new class
    {
        use HasDashboardMetrics;

        public function range(): array
        {
            return [$this->periodRange(), $this->previousPeriodRange()];
        }
    };

    $sales = new class
    {
        use HasSaleDashboardFilters;

        public function range(): array
        {
            return [$this->periodRange(), $this->previousPeriodRange()];
        }
    };

    $purchases = new class
    {
        use HasPurchaseDashboardFilters;

        public function range(): array
        {
            return [$this->periodRange(), $this->previousPeriodRange()];
        }
    };

    foreach ([$shared, $sales, $purchases] as $dashboard) {
        [$period, $previous] = $dashboard->range();

        expect($dates($period))->toBe(['2026-01-01', '2026-11-23'])
            ->and($dates($previous))->toBe(['2025-01-01', '2025-11-23']);
    }

    // 29 February compares with 28 February, never 1 March.
    expect($dates(DashboardPeriod::previous(Carbon::parse('2028-01-01'), Carbon::parse('2028-02-29')->endOfDay())))
        ->toBe(['2027-01-01', '2027-02-28']);
});

/**
 * DB-4 (2026-11-23, U75): a year drawn per day was hairline spikes. Up to
 * 31 days a chart is by day, up to about four months by week, beyond that
 * by month; the totals are unchanged by the grouping.
 */
it('groups a chart by day, week or month for the length of the period', function () {
    $days = fn (string $start, string $end): array => array_fill(0, (int) floor(Carbon::parse($start)->diffInDays(Carbon::parse($end))) + 1, 1.0);

    $short = DashboardPeriod::rollUp($days('2026-03-01', '2026-03-03'), Carbon::parse('2026-03-01'), Carbon::parse('2026-03-03')->endOfDay());
    // 2 Mar 2026 is a Monday, so 1 Mar is a week of its own.
    $weeks = DashboardPeriod::rollUp($days('2026-03-01', '2026-04-30'), Carbon::parse('2026-03-01'), Carbon::parse('2026-04-30')->endOfDay());
    $months = DashboardPeriod::rollUp($days('2026-01-01', '2026-11-23'), Carbon::parse('2026-01-01'), Carbon::parse('2026-11-23')->endOfDay());

    expect($short)->toBe(['labels' => ['1 Mar', '2 Mar', '3 Mar'], 'values' => [1.0, 1.0, 1.0]])
        ->and($weeks['labels'][0])->toBe('1 Mar')
        ->and($weeks['values'][0])->toBe(1.0)
        ->and($weeks['labels'][1])->toBe('2 Mar')
        ->and($weeks['values'][1])->toBe(7.0)
        ->and(array_sum($weeks['values']))->toBe(61.0)
        ->and($months['labels'])->toHaveCount(11)
        ->and($months['labels'][0])->toBe('Jan')
        ->and($months['values'][1])->toBe(28.0)
        ->and($months['values'][10])->toBe(23.0);
});

/**
 * 2026-11-23: plain "en" made the browser write the banner's date and clock
 * in US form ("November 23", "01:29 AM") over the server's "23 November",
 * "22:29", so the text changed format on every load.
 */
it('hands the browser a day-first, 24-hour English and keeps other languages', function () {
    $locales = [];

    foreach (['en', 'fr', 'pt_BR'] as $locale) {
        app()->setLocale($locale);
        $locales[$locale] = DashboardPeriod::browserLocale();
    }

    app()->setLocale('en');

    expect($locales)->toBe(['en' => 'en-GB', 'fr' => 'fr', 'pt_BR' => 'pt-BR']);
});
