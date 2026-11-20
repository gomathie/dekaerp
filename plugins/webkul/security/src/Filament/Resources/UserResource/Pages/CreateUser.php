<?php

namespace Webkul\Security\Filament\Resources\UserResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Filament\Resources\UserResource;
use Webkul\Security\Models\User;
use Webkul\Security\Services\MultiCompanyAdminService;
use Webkul\Security\Services\SecurityAuditLogger;

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

        if (app(MultiCompanyAdminService::class)->includesMultiCompanyAdminRole($roleIds)) {
            $data['resource_permission'] = PermissionType::GLOBAL;
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        /** @var User $record */
        $record = $this->record;
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
    }
}
