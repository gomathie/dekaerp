<?php

namespace Webkul\Security\Filament\Resources\UserResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Filament\Resources\UserResource;
use Webkul\Security\Models\User;
use Webkul\Security\Services\MultiCompanyAdminService;
use Webkul\Security\Services\SeatCapacityService;
use Webkul\Security\Services\SecurityAuditLogger;
use Webkul\Security\Services\TenantRoleAssigner;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected ?bool $hasDatabaseTransactions = true;

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('security::filament/resources/user/pages/create-user.notification.title'))
            ->body(__('security::filament/resources/user/pages/create-user.notification.body'));
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->validateAdministrationState($data);

        $roleIds = (array) ($data['roles'] ?? $this->form->getRawState()['roles'] ?? []);

        $administration = app(MultiCompanyAdminService::class);

        // Both administration roles need `global`: ownership on the User model
        // matches creator_id and id, so anything narrower would show an
        // administrator only themselves and the users they personally created,
        // never the colleagues already there. For a Company Admin that is bounded
        // to their own companies by scopeManageableUsers() and UserPolicy.
        if ($administration->includesMultiCompanyAdminRole($roleIds)
            || $administration->includesCompanyAdminRole($roleIds)) {
            $data['resource_permission'] = PermissionType::GLOBAL;
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        /** @var User $record */
        $record = $this->record;

        /*
         * Roles are written here, not by the form: the roles field is not a
         * relationship any more because a tenant role belongs to a company
         * membership rather than to the user. TenantRoleAssigner splits the
         * selection into the global pivot (platform roles) and
         * company_membership_roles (tenant roles).
         */
        app(TenantRoleAssigner::class)->sync(
            $record,
            (array) ($this->form->getRawState()['roles'] ?? []),
            app(MultiCompanyAdminService::class)->assignedCompanyIds($record),
        );

        $record->unsetRelation('roles');

        app(SecurityAuditLogger::class)->record(
            'security.user.created',
            $record,
            companyId: $record->default_company_id,
            after: app(MultiCompanyAdminService::class)->userSnapshot($record),
        );
    }

    protected function validateAdministrationState(array $state): void
    {
        $roleIds = (array) ($state['roles'] ?? $this->data['roles'] ?? []);
        $companyIds = (array) ($state['allowed_companies'] ?? $this->data['allowed_companies'] ?? []);
        $defaultCompanyId = $state['default_company_id'] ?? $this->data['default_company_id'] ?? null;

        UserResource::ensureAdminRoleConstraints(null, $roleIds);
        UserResource::ensureUserAssignmentConstraints(
            null,
            $roleIds,
            $companyIds,
            $defaultCompanyId ? (int) $defaultCompanyId : null,
        );

        /*
         * Seats (D-SAAS-03): creating a user attaches a membership, which
         * consumes a seat. Checked here as well as on invitation, so "add a
         * user" cannot walk past the limit the invitations respect.
         *
         * `assertCanAttachMembership` throws a ValidationException keyed to
         * `seats`, which Filament renders on the form; the company that is full
         * is named by the message, not by the field.
         */
        $capacity = app(SeatCapacityService::class);

        foreach (array_unique(array_map('intval', $companyIds)) as $companyId) {
            if ($companyId > 0) {
                $capacity->assertCanAttachMembership($companyId);
            }
        }
    }
}
