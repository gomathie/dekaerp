<?php

namespace Webkul\Security\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\Invitation;
use Webkul\Security\Models\Role;
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

        $invitation = DB::transaction(fn (): Invitation => Invitation::query()->create([
            'inviter_id'        => $actor->getKey(),
            'email'             => $email,
            'default_company_id' => $defaultCompanyId,
            'company_ids'       => $companyIds,
            'role_ids'          => $roleIds,
        ]));

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
        [$actor, $companyIds, $defaultCompanyId, $roleIds] = $this->resolveContext($invitation);

        return DB::transaction(function () use ($invitation, $name, $password, $actor, $companyIds, $defaultCompanyId, $roleIds): User {
            $isMultiCompanyAdmin = Role::query()
                ->whereIn('id', $roleIds)
                ->whereRaw('LOWER(name) = ?', [mb_strtolower(Role::MULTI_COMPANY_ADMIN)])
                ->exists();

            $user = User::query()->create([
                'creator_id'         => $actor?->getKey(),
                'name'               => $name,
                'password'           => $password,
                'email'              => $invitation->email,
                'default_company_id' => $defaultCompanyId,
                'resource_permission' => $isMultiCompanyAdmin
                    ? PermissionType::GLOBAL
                    : PermissionType::INDIVIDUAL,
            ]);

            $user->allowedCompanies()->sync($companyIds);
            $user->syncRoles($roleIds);
            $user->unsetRelation('roles');

            $snapshot = $this->administration->userSnapshot($user);

            $this->audit->record(
                'security.user.created_from_invitation',
                $user,
                $actor,
                $defaultCompanyId,
                after: $snapshot,
            );

            $invitation->delete();

            return $user;
        });
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
