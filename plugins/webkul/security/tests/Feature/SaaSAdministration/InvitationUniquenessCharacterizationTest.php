<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Webkul\Security\Models\Invitation;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Security\Services\CompanyAdminRoleProvisioner;
use Webkul\Security\Services\UserInvitationService;
use Webkul\Security\Settings\UserSettings;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

/**
 * SAAS-00 characterization, **inverted by SAAS-06**.
 *
 * It used to pin that invitation uniqueness covered only existing `users.email`,
 * so a second pending invitation for the same address and company was accepted
 * without error. SAAS-06 added the lifecycle (`pending`/`accepted`/`revoked`/
 * `expired`) and duplicate-pending protection, so the first test now asserts the
 * refusal and the second still pins that the database carries no *unique* index
 * on `email` (the protection is a locked service check, not a constraint that
 * would forbid re-inviting an address after a revoke).
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

it('refuses a duplicate pending invitation for the same email and company', function () {
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
        ->and($firstInvitation->email)->toBe($email)
        ->and($firstInvitation->expires_at)->not->toBeNull();

    // The inversion: a second invitation for the same address and company is now
    // refused while the first is still pending, so the customer cannot be
    // promised two seats for one person.
    expect(fn () => $service->create(
        $superAdmin,
        $email,
        $company->getKey(),
        [$tenantRole->getKey()],
    ))->toThrow(ValidationException::class);

    expect(Invitation::query()->where('email', $email)->count())->toBe(1);
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
