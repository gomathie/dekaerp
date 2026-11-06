<?php

use Illuminate\Support\Facades\DB;
use Webkul\Project\Enums\TaskState;
use Webkul\Project\Filament\Widgets\TaskByStateChart;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\Task;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('projects');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

/**
 * @return array<string, mixed>
 */
function taskByStateChartData(): array
{
    $widget = new TaskByStateChart;

    return (fn (): array => $this->getData())->call($widget);
}

/**
 * Sentry 156271115 reported the chart as an N+1: it ran one count query per
 * task state. It now runs a single grouped count, which must still give each
 * state its own figure and still respect the company boundary.
 */
it('counts tasks per state in one query, within the active company', function () {
    $company = CompanyHelper::company();
    $otherCompany = CompanyHelper::company();

    CompanyHelper::actingAsCompanyUser($company);

    $project = Project::factory()->create(['company_id' => $company->id]);
    $otherProject = Project::factory()->create(['company_id' => $otherCompany->id]);

    $states = TaskState::cases();
    $first = $states[0];
    $second = $states[1];

    Task::factory()->count(2)->create(['project_id' => $project->id, 'company_id' => $company->id, 'state' => $first->value]);
    Task::factory()->create(['project_id' => $project->id, 'company_id' => $company->id, 'state' => $second->value]);
    Task::factory()->count(3)->create(['project_id' => $otherProject->id, 'company_id' => $otherCompany->id, 'state' => $first->value]);

    DB::flushQueryLog();
    DB::enableQueryLog();

    $data = taskByStateChartData();

    $taskCounts = collect(DB::getQueryLog())
        ->filter(fn (array $entry): bool => str_contains($entry['query'], 'projects_tasks'))
        ->count();

    DB::disableQueryLog();
    fwrite(STDERR, json_encode(array_column(DB::getQueryLog(), 'query'), JSON_PRETTY_PRINT));

    $byLabel = array_combine($data['labels'], $data['datasets'][0]['data']);

    expect($taskCounts)->toBe(1)
        ->and(count($data['labels']))->toBe(count($states))
        ->and($byLabel[TaskState::options()[$first->value]])->toBe(2)
        ->and($byLabel[TaskState::options()[$second->value]])->toBe(1);
});
