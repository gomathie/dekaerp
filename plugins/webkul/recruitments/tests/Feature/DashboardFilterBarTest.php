<?php

use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Recruitment\Filament\Pages\Recruitments;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('recruitments');
    PluginAccess::flush();
});

/**
 * The Filters button counts filters that narrow the figures (user,
 * 2026-11-25). Recruitment's status starts on "All", its default, which is
 * not a filter in use; "Clear all" puts it back to "All", not to empty.
 */
it('does not count a filter left on its default, and clears back to the default', function (): void {
    CompanyHelper::actingAsCompanyUser(Company::factory()->create(), ['page_recruitment_recruitments']);

    $page = new Recruitments;
    $page->filters = ['status' => 'all', 'startDate' => '2026-01-01', 'endDate' => '2026-03-31'];

    $bar = fn (): array => (fn (): array => $this->dashboardFilterBarData())->call($page);

    expect($bar()['inUse'])->toBe([]);

    $page->filters['status'] = 'hired';

    expect($bar()['inUse'])->toBe([__('recruitments::filament/pages/recruitment.filters-form.status.title')]);

    $page->clearDashboardFilters();

    expect($page->filters['status'])->toBe('all')
        ->and($page->filters['startDate'])->toBe('2026-01-01')
        ->and($bar()['inUse'])->toBe([]);
});
