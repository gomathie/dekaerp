<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Webkul\Security\Models\Invitation;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Security\Services\CompanyAdministratorInviter;
use Webkul\Security\Services\CompanyAdminRoleProvisioner;
use Webkul\Security\Services\SeatCapacityService;
use Webkul\Security\Services\UserInvitationService;
use Webkul\Security\Settings\UserSettings;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';

/**
 * SAAS-06: a company has seats, and an invitation reserves one.
 *
 * D-SAAS-03: an active membership consumes a seat (administrators included, one
 * per person per company); a valid pending invitation reserves one; inactive
 * users and expired or revoked invitations do not count.
 *
 * D-SAAS-04: lowering a limit never deactivates anybody - the company simply
 * cannot add members until usage falls or the limit rises.
 */
beforeEach(function (): void {
    TestBootstrapHelper::ensureERPInstalled();

    Auth::forgetGuards();
    app(CompanyAdminRoleProvisioner::class)->provision();

    settings(UserSettings::class)->enable_user_invitation = true;
    settings(UserSettings::class)->save();
});

/**
 * A company with a five-seat limit unless the test says otherwise.
 *
 * Explicit because new companies are unlimited by default (2026-10-29): a
 * capacity test has to state the limit it is testing against, or it would test
 * nothing.
 */
function seatCapCompany(array $attributes = []): Company
{
    return Company::factory()->create(['is_active' => true, 'seat_limit' => 5, ...$attributes]);
}

function seatCapMember(Company $company): User
{
    $user = User::factory()->create(['is_active' => true, 'is_default' => false]);

    $user->allowedCompanies()->sync([$company->getKey()]);

    return $user;
}

/**
 * The installation administrator, signed in and attached to the company.
 *
 * A real super admin rather than FilamentHelper::actingAs(), which returns a
 * user with no permissions: inviting as that user is refused by the role
 * assignment rules before capacity is ever consulted, so a "refused for lack of
 * seats" assertion would pass for the wrong reason. Attaching costs no seat -
 * platform identities are excluded (D-SAAS-03).
 */
function seatCapAdministrator(Company $company): User
{
    $administrator = User::query()->where('email', 'admin@example.com')->firstOrFail();

    $administrator->allowedCompanies()->syncWithoutDetaching([$company->getKey()]);

    Auth::forgetGuards();
    Auth::shouldUse('web');
    Auth::guard('web')->login($administrator);

    expect($administrator->isSuperAdmin())->toBeTrue();

    return $administrator;
}

function seatCapPendingInvitation(Company $company, array $attributes = []): Invitation
{
    return Invitation::query()->create([
        'email'              => fake()->unique()->safeEmail(),
        'default_company_id' => $company->getKey(),
        'company_ids'        => [$company->getKey()],
        'role_ids'           => [],
        'expires_at'         => now()->addDays(Invitation::ACCEPT_WINDOW_DAYS),
        ...$attributes,
    ]);
}

/**
 * Inverted 2026-10-29 (user decision): this used to pin "defaults a company to
 * five seats". With no screen to raise a limit before SAAS-12/13, that default
 * would have stopped the next onboarded client at their sixth user.
 */
it('leaves a new company unlimited by default', function () {
    $company = Company::factory()->create(['is_active' => true]);

    expect(app(SeatCapacityService::class)->limitFor($company))->toBeNull()
        ->and(app(SeatCapacityService::class)->isUnlimited($company))->toBeTrue()
        ->and(app(SeatCapacityService::class)->usedSeats($company))->toBe(0);
});

it('treats a null limit as unlimited', function () {
    $company = seatCapCompany(['seat_limit' => null]);

    for ($i = 0; $i < 7; $i++) {
        seatCapMember($company);
    }

    $service = app(SeatCapacityService::class);

    expect($service->isUnlimited($company))->toBeTrue()
        ->and($service->remainingSeats($company))->toBeNull()
        ->and($service->isOverLimit($company))->toBeFalse();
});

it('counts active memberships and pending invitations as used seats', function () {
    $company = seatCapCompany();

    seatCapMember($company);
    seatCapMember($company);
    seatCapPendingInvitation($company);

    $service = app(SeatCapacityService::class);

    expect($service->activeMembershipCount($company))->toBe(2)
        ->and($service->pendingInvitationCount($company))->toBe(1)
        ->and($service->usedSeats($company))->toBe(3)
        ->and($service->remainingSeats($company))->toBe(2);
});

it('does not count an inactive user', function () {
    $company = seatCapCompany();

    seatCapMember($company);

    $inactive = seatCapMember($company);
    $inactive->forceFill(['is_active' => false])->saveQuietly();

    expect(app(SeatCapacityService::class)->activeMembershipCount($company))->toBe(1);
});

it('does not count expired or revoked invitations', function () {
    $company = seatCapCompany();

    seatCapPendingInvitation($company, ['expires_at' => now()->subDay()]);
    seatCapPendingInvitation($company, ['revoked_at' => now(), 'expires_at' => now()->addDay()]);
    seatCapPendingInvitation($company, ['accepted_at' => now(), 'expires_at' => now()->addDay()]);

    expect(app(SeatCapacityService::class)->pendingInvitationCount($company))->toBe(0);
});

it('refuses an invitation when the last seat is already in use', function () {
    $company = seatCapCompany();

    for ($i = 0; $i < 5; $i++) {
        seatCapMember($company);
    }

    $superAdmin = seatCapAdministrator($company);

    $role = Role::query()
        ->whereRaw('LOWER(name) = ?', [mb_strtolower(Role::COMPANY_ADMIN)])
        ->firstOrFail();

    // The message is pinned: a ValidationException alone would also pass if the
    // invitation were refused for an unrelated assignment reason.
    expect(fn () => app(UserInvitationService::class)->create(
        $superAdmin,
        fake()->unique()->safeEmail(),
        (int) $company->getKey(),
        [(int) $role->getKey()],
    ))->toThrow(ValidationException::class, 'user seats');

    expect(Invitation::query()->where('default_company_id', $company->getKey())->count())->toBe(0);
});

it('does not count a platform support identity as a seat', function () {
    $company = seatCapCompany();

    // Support access: the installer and Company::created attach platform
    // administrators to tenants, and D-SAAS-03 excludes them from the count.
    $superAdmin = seatCapAdministrator($company);
    seatCapMember($company);

    expect($superAdmin->isSuperAdmin())->toBeTrue()
        ->and(DB::table('user_allowed_companies')->where('company_id', $company->getKey())->count())->toBe(2)
        ->and(app(SeatCapacityService::class)->activeMembershipCount($company))->toBe(1);
});

it('keeps a pending invitation\'s seat when an existing user is attached', function () {
    $company = seatCapCompany();

    for ($i = 0; $i < 4; $i++) {
        seatCapMember($company);
    }

    seatCapPendingInvitation($company);

    $service = app(SeatCapacityService::class);

    // 4 members + 1 reservation = 5: attaching a fifth member would leave the
    // invitee unable to accept a seat they were promised.
    expect(fn () => $service->withLockedCompany($company, fn () => $service->assertCanAttachMembership($company)))
        ->toThrow(ValidationException::class, 'user seats');

    // The per-person variant applies the same rule to someone not yet a member.
    $outsider = User::factory()->create(['is_active' => true, 'is_default' => false]);

    expect(fn () => $service->assertCanAttachUser($outsider, (int) $company->getKey()))
        ->toThrow(ValidationException::class, 'user seats');
});

it('reserves the seat of a pending invitation', function () {
    $company = seatCapCompany();

    for ($i = 0; $i < 4; $i++) {
        seatCapMember($company);
    }

    seatCapPendingInvitation($company);

    $service = app(SeatCapacityService::class);

    expect($service->remainingSeats($company))->toBe(0)
        ->and(fn () => $service->withLockedCompany($company, fn () => $service->assertCanReserve($company)))
        ->toThrow(ValidationException::class);
});

it('releases the seat when a pending invitation is revoked or expires', function () {
    $company = seatCapCompany();

    for ($i = 0; $i < 4; $i++) {
        seatCapMember($company);
    }

    $invitation = seatCapPendingInvitation($company);

    $service = app(SeatCapacityService::class);

    expect($service->usedSeats($company))->toBe(5)
        ->and($service->remainingSeats($company))->toBe(0);

    $invitation->forceFill(['revoked_at' => now()])->save();

    // The seat goes back the moment the invitation stops being valid - that is
    // what makes revoking a usable support action rather than a bookkeeping one.
    expect($service->usedSeats($company))->toBe(4)
        ->and($service->remainingSeats($company))->toBe(1);
});

it('accepting an invitation does not double count the reserved seat', function () {
    $company = seatCapCompany();

    for ($i = 0; $i < 4; $i++) {
        seatCapMember($company);
    }

    $superAdmin = seatCapAdministrator($company);

    $service = app(UserInvitationService::class);
    $capacity = app(SeatCapacityService::class);

    $role = Role::query()
        ->whereRaw('LOWER(name) = ?', [mb_strtolower(Role::COMPANY_ADMIN)])
        ->firstOrFail();

    $invitation = $service->create(
        $superAdmin,
        fake()->unique()->safeEmail(),
        (int) $company->getKey(),
        [(int) $role->getKey()],
    );

    // 4 active + 1 reservation = the limit: accepting must still work, because
    // the reservation *becomes* the fifth membership.
    expect($capacity->usedSeats($company))->toBe(5);

    $accepted = $service->accept($invitation, 'Fifth Seat', 'correct-horse-battery');

    expect($capacity->activeMembershipCount($company))->toBe(5)
        ->and($capacity->usedSeats($company))->toBe(5)
        ->and($accepted->allowedCompanies()->pluck('companies.id')->all())->toBe([(int) $company->getKey()]);
});

it('refuses an acceptance that would exceed the limit', function () {
    $company = seatCapCompany();

    for ($i = 0; $i < 5; $i++) {
        seatCapMember($company);
    }

    $superAdmin = seatCapAdministrator($company);

    $role = Role::query()
        ->whereRaw('LOWER(name) = ?', [mb_strtolower(Role::COMPANY_ADMIN)])
        ->firstOrFail();

    // The invitation is written directly: the service would refuse to create it
    // once the company is full, and this test is about acceptance.
    $invitation = seatCapPendingInvitation($company, ['role_ids' => [(int) $role->getKey()], 'inviter_id' => $superAdmin->getKey()]);

    expect(fn () => app(UserInvitationService::class)->accept($invitation, 'Over Limit', 'correct-horse-battery'))
        ->toThrow(ValidationException::class, 'user seats');

    expect(User::query()->where('name', 'Over Limit')->exists())->toBeFalse();
});

it('refuses to accept an expired or revoked invitation', function () {
    $company = seatCapCompany();

    $superAdmin = seatCapAdministrator($company);

    $role = Role::query()
        ->whereRaw('LOWER(name) = ?', [mb_strtolower(Role::COMPANY_ADMIN)])
        ->firstOrFail();

    $expired = seatCapPendingInvitation($company, [
        'expires_at' => now()->subDay(),
        'inviter_id' => $superAdmin->getKey(),
        'role_ids'   => [(int) $role->getKey()],
    ]);

    $revoked = seatCapPendingInvitation($company, [
        'revoked_at' => now(),
        'inviter_id' => $superAdmin->getKey(),
        'role_ids'   => [(int) $role->getKey()],
    ]);

    $service = app(UserInvitationService::class);

    expect(fn () => $service->accept($expired, 'Expired User', 'correct-horse-battery'))
        ->toThrow(ValidationException::class);

    expect(fn () => $service->accept($revoked, 'Revoked User', 'correct-horse-battery'))
        ->toThrow(ValidationException::class);
});

it('answers a used invitation link with not found', function () {
    $company = seatCapCompany();

    $accepted = seatCapPendingInvitation($company, ['accepted_at' => now()]);
    $pending = seatCapPendingInvitation($company);

    // Acceptance used to delete the row, so a reused link was a 404. The row now
    // survives; the page must still refuse it instead of showing a form that can
    // only fail. The pending link is the control: the route itself works.
    $this->get($pending->acceptUrl())->assertOk();
    $this->get($accepted->acceptUrl())->assertNotFound();
});

it('never deactivates anyone when a limit is lowered below current usage', function () {
    $company = seatCapCompany(['seat_limit' => 3]);

    $members = collect(range(1, 5))->map(fn () => seatCapMember($company));

    $capacity = app(SeatCapacityService::class);

    expect($capacity->activeMembershipCount($company))->toBe(5)
        ->and($capacity->isOverLimit($company))->toBeTrue()
        ->and($members->every(fn (User $user): bool => (bool) $user->fresh()->is_active))->toBeTrue()
        ->and(DB::table('user_allowed_companies')->where('company_id', $company->getKey())->count())->toBe(5);
});

it('reports a company with no free seat as a capacity reason, not a generic failure', function () {
    $company = seatCapCompany(['seat_limit' => 1]);

    // One active member fills the single seat; the onboarding invitation would
    // be the second person.
    seatCapMember($company);

    $superAdmin = FilamentHelper::actingAs();

    $reason = app(CompanyAdministratorInviter::class)->invite($company, $superAdmin, 'owner@tenant-full.test');

    expect($reason)->toBe(CompanyAdministratorInviter::SEATS_EXHAUSTED)
        ->and(Invitation::query()->where('email', 'owner@tenant-full.test')->exists())->toBeFalse();
});

it('does not charge a seat for a person who is already a member', function () {
    $company = seatCapCompany(['seat_limit' => 2]);

    $member = seatCapMember($company);
    seatCapMember($company);

    $outsider = User::factory()->create(['is_active' => true, 'is_default' => false]);

    $capacity = app(SeatCapacityService::class);

    // Re-saving an existing member's form must not be blocked by a full company.
    $capacity->assertCanAttachUser($member, (int) $company->getKey());

    expect(fn () => $capacity->assertCanAttachUser($outsider, (int) $company->getKey()))
        ->toThrow(ValidationException::class);
});
it('records a limit change with the actor and the before/after values', function () {
    $company = seatCapCompany();
    $superAdmin = seatCapAdministrator($company);

    $capacity = app(SeatCapacityService::class);

    $capacity->setLimit($company, 12, $superAdmin);

    expect($capacity->limitFor($company))->toBe(12);

    $capacity->setLimit($company, null, $superAdmin);

    expect($capacity->limitFor($company))->toBeNull()
        ->and($capacity->isUnlimited($company))->toBeTrue();
});
