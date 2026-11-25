<?php

use Illuminate\Support\Facades\URL;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Project\Filament\OverviewCard;
use Webkul\Project\Models\Task;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('projects');
    PluginAccess::flush();
});

/**
 * Dashboard plan DB-1 (2026-11-25): unavailable data is not shown as 0. A
 * company with no tasks has no "% of tasks done"; the Overview card leaves
 * the bar out rather than drawing "0% done", as Logistics' card does.
 */
it('leaves out the tasks done bar when there are no tasks', function (): void {
    FilamentHelper::bootAdminPanel();
    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    $company = Company::factory()->create();

    CompanyHelper::actingAsCompanyUser($company, ['view_any_project_task']);

    expect(Task::query()->count())->toBe(0)
        ->and(OverviewCard::data()['bar'])->toBeNull()
        // The other figures stay real zeros: those are counts, not ratios.
        ->and(OverviewCard::data()['items'][1]['value'])->toBe('0');
});
