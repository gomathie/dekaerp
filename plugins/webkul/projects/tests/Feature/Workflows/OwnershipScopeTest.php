<?php

use Webkul\Partner\Models\Partner;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\Task;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\Team;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

/**
 * What `OwnershipScope` actually does, on a model that carries it globally.
 *
 * Until 2026-09-25 nothing in this application tested this. The scope began with
 * a bare `app()->runningInConsole()`, and `artisan test` is the console, so every
 * ownership rule was inert under Pest while being live in the browser. These
 * tests exist so that `individual` cannot quietly stop restricting.
 *
 * `Project` is used because it carries the scope globally and has the richest set
 * of sources: `creator_id`, `user_id` and followers. `users.resource_permission`
 * defaults to `individual` at the column level, so this is the common case for
 * real users, not an exotic one.
 *
 * See docs/company-admin-role-plan.md §4c and §4d.
 */
beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('projects');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

/**
 * A project belonging to the given owner, in the given company.
 */
function ownershipProject($company, User $owner, array $overrides = []): Project
{
    return Project::factory()->create(array_merge([
        'company_id' => $company->id,
        'creator_id' => $owner->getKey(),
        'user_id'    => $owner->getKey(),
    ], $overrides));
}

function ownershipActor($company, PermissionType $permission): User
{
    $actor = CompanyHelper::actingAsCompanyUser($company);

    // Set on the authenticated instance itself, which is what `auth()->user()`
    // returns, so the Bouncer sees the new value. Its cache key includes the
    // resource permission, so changing it invalidates any earlier entry rather
    // than needing a flush - `Bouncer::clearCache()` is protected in any case.
    $actor->forceFill(['resource_permission' => $permission])->saveQuietly();

    return $actor;
}

it('hides a project the individual user has nothing to do with', function () {
    $company = CompanyHelper::company();

    $stranger = SecurityHelper::authenticateWithPermissions([]);
    $theirs = ownershipProject($company, $stranger);

    $actor = ownershipActor($company, PermissionType::INDIVIDUAL);
    $mine = ownershipProject($company, $actor);

    // Same company throughout, so the company scope is not what is doing this.
    expect($theirs->company_id)->toBe($mine->company_id);

    $visible = Project::query()->pluck('id');

    expect($visible)->toContain($mine->id)
        ->not->toContain($theirs->id);
});

it('shows an individual user a project they were assigned but did not create', function () {
    $company = CompanyHelper::company();

    $creator = SecurityHelper::authenticateWithPermissions([]);

    $actor = ownershipActor($company, PermissionType::INDIVIDUAL);

    // user_id is the assignee, and it is one of Project's ownership sources - so
    // "individual" must not mean "only what I created".
    $assigned = ownershipProject($company, $creator, ['user_id' => $actor->getKey()]);

    expect(Project::query()->pluck('id'))->toContain($assigned->id);
});

it('shows a global user every project in the company', function () {
    $company = CompanyHelper::company();

    $stranger = SecurityHelper::authenticateWithPermissions([]);
    $theirs = ownershipProject($company, $stranger);

    $actor = ownershipActor($company, PermissionType::GLOBAL);
    $mine = ownershipProject($company, $actor);

    $visible = Project::query()->pluck('id');

    expect($visible)->toContain($mine->id)
        ->and($visible)->toContain($theirs->id);
});

it('shows a group user their teammates projects but not a stranger from another team', function () {
    $company = CompanyHelper::company();

    $teammate = SecurityHelper::authenticateWithPermissions([]);
    $theirs = ownershipProject($company, $teammate);

    $stranger = SecurityHelper::authenticateWithPermissions([]);
    $strangersProject = ownershipProject($company, $stranger);

    $actor = ownershipActor($company, PermissionType::GROUP);

    // The team is what `group` means: Bouncer resolves it to every user who
    // shares a team with the actor.
    $team = Team::query()->create(['name' => 'Delivery '.uniqid()]);
    $team->users()->sync([$actor->getKey(), $teammate->getKey()]);
    $actor->unsetRelation('teams');

    $visible = Project::query()->pluck('id');

    expect($visible)->toContain($theirs->id)
        ->not->toContain($strangersProject->id);
});

it('does not turn a group user with no team into a global one', function () {
    $company = CompanyHelper::company();

    $stranger = SecurityHelper::authenticateWithPermissions([]);
    $theirs = ownershipProject($company, $stranger);

    // No team attached. Bouncer's group branch resolves through
    // `whereIn('teams.id', $user->teams()->pluck('id'))`, which matches nothing
    // and used to hand back an empty array - and OwnershipScope reads an empty
    // array as *no restriction*, so a group user who had lost their team, or
    // never been given one, silently saw everything. Fails closed now: no team
    // means only your own rows.
    $actor = ownershipActor($company, PermissionType::GROUP);
    $mine = ownershipProject($company, $actor);

    expect($actor->teams()->count())->toBe(0);

    $visible = Project::query()->pluck('id');

    expect($visible)->toContain($mine->id)
        ->not->toContain($theirs->id);
});

it('shows an individual user a task assigned to them through the users relation', function () {
    $company = CompanyHelper::company();

    $creator = SecurityHelper::authenticateWithPermissions([]);
    $project = ownershipProject($company, $creator);

    $actor = ownershipActor($company, PermissionType::INDIVIDUAL);

    $mine = Task::factory()->create(['project_id' => $project->id, 'creator_id' => $creator->getKey()]);
    $theirs = Task::factory()->create(['project_id' => $project->id, 'creator_id' => $creator->getKey()]);

    // Task's second ownership source is OwnerSource::relation('users') - a
    // many-to-many, exercised by OwnershipScope::applyRelation(), which nothing
    // tested before. Assignment through a pivot has to count as ownership or an
    // assignee cannot open their own work.
    $mine->users()->sync([$actor->getKey()]);

    $visible = Task::query()->pluck('id');

    expect($visible)->toContain($mine->id)
        ->not->toContain($theirs->id);
});

it('shows an individual user a project they follow', function () {
    $company = CompanyHelper::company();

    $owner = SecurityHelper::authenticateWithPermissions([]);
    $followed = ownershipProject($company, $owner);
    $unfollowed = ownershipProject($company, $owner);

    $actor = ownershipActor($company, PermissionType::INDIVIDUAL);

    // `OwnershipScope::applyFollowers()` matches on the follower's *partner*, not
    // the user, and gives up when the actor has no `partner_id`. Production users
    // always have one - `User::saved()` creates a Partner for them - but
    // SecurityHelper builds users with `withoutEvents()`, so that hook never
    // fires in tests and the partner has to be attached by hand. That gap is why
    // this branch had never been exercised.
    $partner = Partner::factory()->create(['company_id' => $company->id]);
    $actor->forceFill(['partner_id' => $partner->getKey()])->saveQuietly();

    $followed->followers()->create(['partner_id' => $partner->getKey()]);

    $visible = Project::query()->pluck('id');

    expect($visible)->toContain($followed->id)
        ->not->toContain($unfollowed->id);
});

it('still keeps the company boundary for a global user', function () {
    $mineCompany = CompanyHelper::company();
    $otherCompany = CompanyHelper::company();

    $stranger = SecurityHelper::authenticateWithPermissions([]);
    $otherCompanyProject = ownershipProject($otherCompany, $stranger);

    $actor = ownershipActor($mineCompany, PermissionType::GLOBAL);
    $mine = ownershipProject($mineCompany, $actor);

    // `global` is about *ownership*, not about companies. Confusing the two is
    // how a tenant boundary gets dropped, so it is pinned here.
    $visible = Project::query()->pluck('id');

    expect($visible)->toContain($mine->id)
        ->not->toContain($otherCompanyProject->id);
});
