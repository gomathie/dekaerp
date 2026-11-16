<?php

use Illuminate\Support\Facades\DB;
use Webkul\Project\Filament\Widgets\TaskByStageChart;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\Task;
use Webkul\Project\Models\TaskStage;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('projects');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

/**
 * Upstream counted tasks once per stage of TaskStage::all(), so the dashboard's
 * query count grew with every stage created (the same N+1 as Sentry 156271115
 * on the state chart). It now counts every stage in one grouped query, and must
 * still give each stage its own figure, empty stages included.
 */
it('counts tasks per stage in one grouped query', function () {
    $company = CompanyHelper::company();

    $user = CompanyHelper::actingAsCompanyUser($company);

    $project = Project::factory()->create(['company_id' => $company->id]);

    $busy = TaskStage::factory()->create(['project_id' => $project->id]);
    $quiet = TaskStage::factory()->create(['project_id' => $project->id]);
    $empty = TaskStage::factory()->create(['project_id' => $project->id]);

    $attributes = ['project_id' => $project->id, 'company_id' => $company->id, 'creator_id' => $user->id];

    Task::factory()->count(2)->create([...$attributes, 'stage_id' => $busy->id]);
    Task::factory()->create([...$attributes, 'stage_id' => $quiet->id]);

    DB::flushQueryLog();
    DB::enableQueryLog();

    $data = (fn (): array => $this->getData())->call(new TaskByStageChart);

    // Data queries only, not one-off column lookups (pg_attribute).
    $countQueries = collect(DB::getQueryLog())
        ->filter(fn (array $entry): bool => str_contains($entry['query'], 'count(*)')
            && str_contains($entry['query'], 'projects_tasks'))
        ->count();

    DB::disableQueryLog();

    $byStage = array_combine($data['labels'], $data['datasets'][0]['data']);

    expect($countQueries)->toBe(1)
        ->and($byStage[$busy->name])->toBe(2)
        ->and($byStage[$quiet->name])->toBe(1)
        ->and($byStage[$empty->name])->toBe(0);
});
