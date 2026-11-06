<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Webkul\Partner\Models\Partner;
use Webkul\Support\Filament\Concerns\HasDashboardMetrics;

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

        /** @var array<string, mixed> */
        public ?array $pageFilters = [];

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

it('reports a full increase when the previous period was empty', function () {
    expect(dashboardMetricsHost()->call('statTrend', 5.0, 0.0))
        ->toBe(['100%', 'heroicon-m-arrow-trending-up', 'success']);
});

it('reports a drop as a negative trend with its percentage', function () {
    expect(dashboardMetricsHost()->call('statTrend', 75.0, 100.0))
        ->toBe(['25%', 'heroicon-m-arrow-trending-down', 'danger']);
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

    CompanyHelper::actingAsCompanyUser($company);

    $create = function ($companyId, string $day, int $count = 1): void {
        Partner::factory()->count($count)->create(['company_id' => $companyId])
            ->each(fn (Partner $partner) => $partner->forceFill(['created_at' => Carbon::parse($day.' 10:00')])->saveQuietly());
    };

    $create($company->id, '2026-03-01', 2);
    $create($company->id, '2026-03-03');
    $create($otherCompany->id, '2026-03-01', 5);

    $host = dashboardMetricsHost(['start_date' => '2026-03-01', 'end_date' => '2026-03-04']);

    DB::flushQueryLog();
    DB::enableQueryLog();

    $series = $host->call('dailyBuckets', Partner::query(), false);

    $queries = collect(DB::getQueryLog())->filter(fn (array $entry): bool => str_contains($entry['query'], 'partners_partners'));

    DB::disableQueryLog();

    expect($series)->toBe([2.0, 0.0, 1.0, 0.0])
        ->and($queries)->toHaveCount(1);
});
