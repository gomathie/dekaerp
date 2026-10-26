<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Webkul\Security\Models\Invitation;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Security\Services\CompanyAdminRoleProvisioner;
use Webkul\Security\Services\UserInvitationService;
use Webkul\Security\Settings\UserSettings;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

/**
 * SAAS-00 characterization: pins a known defect, it does not endorse it.
 *
 * Invitation uniqueness covers only existing `users.email` — multiple pending
 * invitations for the same email/company are accepted without error. There is
 * also no expiry tracking beyond the signed-URL timeout.
 *
 * SAAS-06 owns invitation lifecycle (status, expiry, revocation, duplicate
 * protection) and must invert the duplicate expectation.
 *
 * See: docs/saas-administration-billing-audit-plan.md § Known related defects.
 */
beforeEach(function (): void {
    Auth::forgetGuards();
    app(CompanyAdminRoleProvisioner::class)->provision();

    settings(UserSettings::class)->enable_user_invitation = true;
    settings(UserSettings::class)->save();

    Mail::fake();
});

afterEach(function (): void {
    Auth::forgetGuards();
});

it('allows duplicate pending invitations for the same email', function () {
    $superAdmin = User::query()->where('email', 'admin@example.com')->firstOrFail();

    Auth::forgetGuards();
    Auth::shouldUse('web');
    Auth::guard('web')->login($superAdmin);

    $company = Company::factory()->create(['is_active' => true]);
    $superAdmin->allowedCompanies()->syncWithoutDetaching([$company->getKey()]);

    $tenantRole = Role::query()
        ->whereNotIn(DB::raw('LOWER(name)'), Role::getSystemRoleNames())
        ->where('is_default', false)
        ->first();

    if (! $tenantRole) {
        $tenantRole = Role::query()->create([
            'name'       => 'SaaS Characterization Role '.fake()->unique()->numerify('####'),
            'guard_name' => 'web',
            'is_default' => false,
        ]);
    }

    $email = 'duplicate-invite-test-'.fake()->unique()->numerify('####').'@example.com';
    $service = app(UserInvitationService::class);

    // Preconditions: no user with that email exists, invitations are enabled.
    expect(User::query()->where('email', $email)->exists())->toBeFalse()
        ->and(settings(UserSettings::class)->enable_user_invitation)->toBeTrue();

    $firstInvitation = $service->create(
        $superAdmin,
        $email,
        $company->getKey(),
        [$tenantRole->getKey()],
    );

    expect($firstInvitation)->toBeInstanceOf(Invitation::class)
        ->and($firstInvitation->email)->toBe($email);

    // The defect: a second invitation for the same email succeeds without error.
    $secondInvitation = $service->create(
        $superAdmin,
        $email,
        $company->getKey(),
        [$tenantRole->getKey()],
    );

    expect($secondInvitation)->toBeInstanceOf(Invitation::class)
        ->and($secondInvitation->email)->toBe($email)
        ->and($secondInvitation->getKey())->not->toBe($firstInvitation->getKey());

    $pendingCount = Invitation::query()->where('email', $email)->count();

    // Two pending invitations for the same email — the defect.
    expect($pendingCount)->toBe(2);
});

it('confirms the invitations table has no unique constraint on email', function () {
    $indexes = DB::select(
        "SELECT indexname, indexdef FROM pg_indexes WHERE tablename = 'user_invitations'"
    );

    $hasUniqueOnEmail = collect($indexes)
        ->filter(fn ($index) => str_contains(strtolower($index->indexdef), 'unique')
            && str_contains($index->indexdef, 'email'))
        ->isNotEmpty();

    // The defect: no unique constraint on email in the invitations table.
    expect($hasUniqueOnEmail)->toBeFalse();
});
