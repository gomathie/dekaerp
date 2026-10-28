<?php

namespace Webkul\Security\Services;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\Invitation;
use Webkul\Security\Models\User;
use Webkul\Security\Settings\UserSettings;

class UserInvitationService
{
    public function __construct(
        protected MultiCompanyAdminService $administration,
        protected SecurityAuditLogger $audit,
    ) {}

    public function create(
        User $actor,
        string $email,
        int $defaultCompanyId,
        array $roleIds,
    ): Invitation {
        Validator::make(
            ['email' => $email],
            ['email' => ['required', 'email', 'max:255', 'unique:users,email']],
        )->validate();

        $companyIds = [$defaultCompanyId];

        $this->administration->assertUserAssignment(
            $actor,
            null,
            $roleIds,
            $companyIds,
            $defaultCompanyId,
        );

        $capacity = app(SeatCapacityService::class);

        /*
         * Duplicate protection and the seat reservation happen under one lock on
         * the company row. Without it two administrators inviting at the same
         * moment both see "one seat free", both succeed, and the customer ends
         * up with an invitation that cannot be accepted.
         */
        $invitation = $capacity->withLockedCompany($defaultCompanyId, function () use ($actor, $email, $defaultCompanyId, $companyIds, $roleIds, $capacity): Invitation {
            // Case-insensitive: PostgreSQL compares text exactly, and
            // "Ann@x.com" and "ann@x.com" are one mailbox holding two seats.
            $alreadyPending = Invitation::query()
                ->whereRaw('LOWER(email) = ?', [mb_strtolower(trim($email))])
                ->where('default_company_id', $defaultCompanyId)
                ->pending()
                ->exists();

            if ($alreadyPending) {
                throw ValidationException::withMessages([
                    'email' => __('This address already has a pending invitation for this company.'),
                ]);
            }

            $capacity->assertCanReserve($defaultCompanyId);

            return Invitation::query()->create([
                'inviter_id'         => $actor->getKey(),
                'email'              => $email,
                'default_company_id' => $defaultCompanyId,
                'company_ids'        => $companyIds,
                'role_ids'           => $roleIds,
                'expires_at'         => now()->addDays(Invitation::ACCEPT_WINDOW_DAYS),
            ]);
        });

        $this->audit->record(
            'security.user.invited',
            $invitation,
            $actor,
            $defaultCompanyId,
            after: [
                'email'       => $email,
                'company_ids' => $companyIds,
                'role_ids'    => array_values(array_map('intval', $roleIds)),
            ],
        );

        return $invitation;
    }

    public function accept(Invitation $invitation, string $name, string $password): User
    {
        $this->assertStillPending($invitation);

        [$actor, $companyIds, $defaultCompanyId, $roleIds] = $this->resolveContext($invitation);

        $capacity = app(SeatCapacityService::class);

        // Accepting turns a *reservation* into a membership, so the seat is
        // already accounted for; what has to be re-checked is that the company
        // has not filled up from another direction while the link sat in an
        // inbox. Hence `assertCanAccept()`, which counts active memberships only.
        return $capacity->withLockedCompany($defaultCompanyId, function () use ($invitation, $name, $password, $actor, $companyIds, $defaultCompanyId, $roleIds, $capacity): User {
            // Re-read under the lock: two clicks on the same link, or a revoke
            // that landed a moment ago, must not both win.
            $locked = Invitation::query()->lockForUpdate()->find($invitation->getKey());

            if (! $locked instanceof Invitation) {
                throw ValidationException::withMessages([
                    'email' => __('security::livewire/accept-invitation.invitation-no-longer-authorized'),
                ]);
            }

            $this->assertStillPending($locked);

            $capacity->assertCanAccept($defaultCompanyId);

            $user = User::query()->create([
                'creator_id'         => $actor?->getKey(),
                'name'               => $name,
                'password'           => $password,
                'email'              => $invitation->email,
                'default_company_id' => $defaultCompanyId,
                /*
                 * Company-wide for everyone, not only Multi-Company Admins: people
                 * in the same company are meant to see that company's records, and
                 * `CompanyScope` is what keeps tenants apart. This used to write
                 * `INDIVIDUAL` for anyone who was not a Multi-Company Admin, which
                 * left an invited colleague unable to see any invoice they had not
                 * raised themselves.
                 *
                 * The Multi-Company Admin lookup that used to pick between the two
                 * values went with it - the role no longer changes the outcome here.
                 */
                'resource_permission' => PermissionType::GLOBAL,
            ]);

            $user->allowedCompanies()->sync($companyIds);

            /*
             * Tenant roles go to the memberships, platform roles stay global.
             * `syncRoles()` here used to write every role against the user and
             * nothing else, which is why a role assigned for one company applied
             * in all of them.
             */
            app(TenantRoleAssigner::class)->sync($user, $roleIds, $companyIds);

            $user->unsetRelation('roles');

            $snapshot = $this->administration->userSnapshot($user);

            $this->audit->record(
                'security.user.created_from_invitation',
                $user,
                $actor,
                $defaultCompanyId,
                after: $snapshot,
            );

            /*
             * The row is kept. It used to be deleted on acceptance, which meant
             * the only record that someone had been invited disappeared - and a
             * seat count had nothing to count against.
             */
            $locked->forceFill(['accepted_at' => now()])->save();

            return $user;
        });
    }

    /**
     * Revoke a pending invitation, releasing its reserved seat.
     */
    public function revoke(Invitation $invitation, User $actor): void
    {
        if (! $invitation->isPending()) {
            return;
        }

        $invitation->revoke($actor);

        $this->audit->record(
            'security.user.invitation_revoked',
            $invitation,
            $actor,
            $invitation->default_company_id ? (int) $invitation->default_company_id : null,
            after: ['email' => $invitation->email],
        );
    }

    protected function assertStillPending(Invitation $invitation): void
    {
        if ($invitation->isPending()) {
            return;
        }

        throw ValidationException::withMessages([
            'email' => __('security::livewire/accept-invitation.invitation-no-longer-authorized'),
        ]);
    }

    protected function resolveContext(Invitation $invitation): array
    {
        if ($invitation->inviter_id) {
            $actor = User::withTrashed()->find($invitation->inviter_id);

            if (! $actor || $actor->trashed() || ! $actor->is_active) {
                throw ValidationException::withMessages([
                    'email' => __('security::livewire/accept-invitation.invitation-no-longer-authorized'),
                ]);
            }

            $companyIds = array_values(array_map('intval', $invitation->company_ids ?? []));
            $defaultCompanyId = (int) $invitation->default_company_id;
            $roleIds = array_values(array_map('intval', $invitation->role_ids ?? []));

            if (! Gate::forUser($actor)->allows('create_security_user')) {
                throw ValidationException::withMessages([
                    'email' => __('security::livewire/accept-invitation.invitation-no-longer-authorized'),
                ]);
            }

            $this->administration->assertUserAssignment(
                $actor,
                null,
                $roleIds,
                $companyIds,
                $defaultCompanyId,
            );

            return [$actor, $companyIds, $defaultCompanyId, $roleIds];
        }

        $defaultCompanyId = settings(UserSettings::class)->default_company_id;
        $defaultRoleId = settings(UserSettings::class)->default_role_id;

        if (! $defaultCompanyId || ! $defaultRoleId) {
            throw ValidationException::withMessages([
                'email' => __('security::livewire/accept-invitation.invitation-no-longer-authorized'),
            ]);
        }

        return [null, [(int) $defaultCompanyId], (int) $defaultCompanyId, [(int) $defaultRoleId]];
    }
}
