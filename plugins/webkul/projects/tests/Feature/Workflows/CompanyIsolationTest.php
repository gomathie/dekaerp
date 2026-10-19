<?php

use Webkul\Project\Models\Project;
use Webkul\Project\Models\ProjectStage;
use Webkul\Project\Models\TaskStage;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('projects');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('hides project stages owned by another company', function () {
    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    $ownStage = ProjectStage::factory()->company($companyA)->create();
    $otherStage = ProjectStage::factory()->company($companyB)->create();

    CompanyHelper::actingAsCompanyUser($companyA);

    $visible = ProjectStage::query()->pluck('id');

    expect($visible)->toContain($ownStage->id)
        ->not->toContain($otherStage->id);
});

it('keeps a new project stage shared when no company is chosen', function () {
    $companyB = CompanyHelper::company();

    CompanyHelper::actingAsCompanyUser($companyB);

    $stage = ProjectStage::factory()->create();

    expect($stage->company_id)->toBeNull();
});

it('derives a task stage company from its project', function () {
    $companyB = CompanyHelper::company();

    CompanyHelper::actingAsCompanyUser($companyB);

    $project = Project::factory()->create(['company_id' => $companyB->id]);

    $stage = TaskStage::factory()->create(['project_id' => $project->id]);

    expect($stage->company_id)->toBe($companyB->id);
});

it('derives a task stage company from a project the actor does not own', function () {
    $company = CompanyHelper::company();

    // The project belongs to somebody else. Project carries a global
    // OwnershipScope, so reading `$stage->project` here resolves to null for this
    // actor - which used to leave company_id null, and CompanyScope reads a null
    // company as *shared*, i.e. visible to every company on the installation.
    // The stage saved and looked correct, which is what made it dangerous.
    $owner = SecurityHelper::authenticateWithPermissions([]);

    $project = Project::factory()->create([
        'company_id' => $company->id,
        'creator_id' => $owner->getKey(),
        'user_id'    => $owner->getKey(),
    ]);

    $actor = CompanyHelper::actingAsCompanyUser($company);

    expect($actor->getKey())->not->toBe($owner->getKey())
        ->and(Project::query()->whereKey($project->getKey())->exists())->toBeFalse();

    $stage = TaskStage::factory()->create(['project_id' => $project->id]);

    expect($stage->company_id)->toBe($company->id);
});
