<?php

use Illuminate\Support\Carbon;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Project\Filament\Pages\Dashboard;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('projects');
    PluginAccess::flush();
});

/**
 * The shared period and filters row (user, 2026-11-25) on a dashboard whose
 * widgets read the older startDate / endDate keys: the period bar writes
 * those keys, the Filters panel has no date field, and the dates are not a
 * filter in use.
 */
it('sets the projects dashboard period keys from the bar and keeps dates out of the filters', function (): void {
    $this->travelTo(Carbon::parse('2026-11-25 10:00'));
    CompanyHelper::actingAsCompanyUser(Company::factory()->create(), ['page_project_dashboard']);

    $page = new Dashboard;
    $page->filters = [];

    $page->selectPeriod('last-year');

    expect($page->filters['startDate'])->toBe('2025-01-01')
        ->and($page->filters['endDate'])->toBe('2025-12-31')
        ->and($page->filters)->not->toHaveKey('start_date');

    $page->applyCustomPeriod('2026-02-01', '2026-02-14');

    $bar = fn (): array => (fn (): array => $this->dashboardFilterBarData())->call($page);

    expect($page->filters['startDate'])->toBe('2026-02-01')
        ->and($bar()['active'])->toBeNull()
        ->and($bar()['inUse'])->toBe([]);

    $names = collect($page->getFiltersForm()->getFlatFields())->map(fn ($field): string => $field->getName())->values()->all();

    expect($names)->not->toContain('date_range')
        ->and($names)->toContain('startDate', 'endDate', 'selectedProjects');

    $page->filters['selectedProjects'] = [7];

    expect($bar()['inUse'])->toBe([__('projects::filament/pages/dashboard.filters-form.project')]);

    $page->clearDashboardFilters();

    expect($page->filters['selectedProjects'])->toBeNull()
        ->and($page->filters['startDate'])->toBe('2026-02-01');
});
