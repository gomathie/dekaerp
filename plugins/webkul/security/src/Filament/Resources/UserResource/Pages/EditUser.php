<?php

namespace Webkul\Security\Filament\Resources\UserResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Filament\Resources\UserResource;
use Webkul\Security\Models\User;
use Webkul\Security\Services\MultiCompanyAdminService;
use Webkul\Security\Services\SecurityAuditLogger;
use Webkul\Security\Settings\UserSettings;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected ?bool $hasDatabaseTransactions = true;

    protected array $administrationBefore = [];

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('security::filament/resources/user/pages/edit-user.notification.title'))
            ->body(__('security::filament/resources/user/pages/edit-user.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('changePassword')
                ->label(__('security::filament/resources/user/pages/edit-user.header-actions.change-password.label'))
                ->visible(fn (UserSettings $userSettings) => $userSettings->enable_reset_password)
                ->action(function (User $record, array $data): void {
                    $record->update([
                        'password' => Hash::make($data['new_password']),
                    ]);

                    Notification::make()
                        ->title(__('security::filament/resources/user/pages/edit-user.header-actions.change-password.notification.title'))
                        ->body(__('security::filament/resources/user/pages/edit-user.header-actions.change-password.notification.body'))
                        ->success()
                        ->send();
                })
                ->schema([
                    TextInput::make('new_password')
                        ->password()
                        ->revealable()
                        ->label(__('security::filament/resources/user/pages/edit-user.header-actions.change-password.form.new-password'))
                        ->required()
                        ->rule(Password::default()),
                    TextInput::make('new_password_confirmation')
                        ->password()
                        ->revealable()
                        ->label(__('security::filament/resources/user/pages/edit-user.header-actions.change-password.form.confirm-new-password'))
                        ->rule('required', fn ($get) => (bool) $get('new_password'))
                        ->same('new_password'),
                ])
                ->icon('heroicon-o-key'),
            ViewAction::make(),
            DeleteAction::make()
                ->before(function (DeleteAction $action, User $record): void {
                    if (! self::getResource()::canDeleteUser($record)) {
                        Notification::make()
                            ->danger()
                            ->title(__('security::filament/resources/user/pages/edit-user.header-actions.delete.notification.error.title'))
                            ->body(__('security::filament/resources/user/pages/edit-user.header-actions.delete.notification.error.body'))
                            ->send();

                        $action->cancel();
                    }
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('security::filament/resources/user/pages/edit-user.header-actions.delete.notification.title'))
                        ->body(__('security::filament/resources/user/pages/edit-user.header-actions.delete.notification.body'))
                ),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $partner = $this->record?->partner;

        if (! $partner) {
            return $data;
        }

        return [
            ...$data,
            ...$partner->toArray(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $roleIds = (array) ($this->form->getRawState()['roles'] ?? []);

        if (app(MultiCompanyAdminService::class)->includesMultiCompanyAdminRole($roleIds)) {
            $data['resource_permission'] = PermissionType::GLOBAL;
        }

        if (! (Auth::id() === $this->record->id && array_key_exists('resource_permission', $data))) {
            return $data;
        }

        $submittedPermission = $data['resource_permission'];

        if ($submittedPermission instanceof PermissionType) {
            $submittedPermission = $submittedPermission->value;
        }

        $currentPermission = $this->record->resource_permission?->value;

        if ((string) $submittedPermission !== (string) $currentPermission) {
            throw ValidationException::withMessages([
                'resource_permission' => __('security::filament/resources/user.form.sections.permissions.fields.resource-permission-self-change-disabled'),
            ]);
        }

        return $data;
    }

    protected function beforeSave(): void
    {
        $state = $this->form->getRawState();
        $roleIds = (array) ($state['roles'] ?? []);
        $companyIds = (array) ($state['allowed_companies'] ?? []);
        $defaultCompanyId = $state['default_company_id'] ?? null;

        UserResource::ensureAdminRoleConstraints($this->record, $roleIds);
        UserResource::ensureUserAssignmentConstraints(
            $this->record,
            $roleIds,
            $companyIds,
            $defaultCompanyId ? (int) $defaultCompanyId : null,
        );

        $this->administrationBefore = app(MultiCompanyAdminService::class)->userSnapshot($this->record);
    }

    protected function afterSave(): void
    {
        $this->record->unsetRelation('roles');

        $administration = app(MultiCompanyAdminService::class);
        $after = $administration->userSnapshot($this->record->refresh());

        if ($after === $this->administrationBefore) {
            return;
        }

        $audit = app(SecurityAuditLogger::class);
        $audit->record(
            'security.user.administration_updated',
            $this->record,
            companyId: $this->record->default_company_id,
            before: $this->administrationBefore,
            after: $after,
        );

        if (($this->administrationBefore['role_ids'] ?? []) !== $after['role_ids']) {
            $audit->record(
                'security.user.roles_updated',
                $this->record,
                companyId: $this->record->default_company_id,
                before: ['role_ids' => $this->administrationBefore['role_ids'] ?? []],
                after: ['role_ids' => $after['role_ids']],
            );
        }

        if (($this->administrationBefore['company_ids'] ?? []) !== $after['company_ids']) {
            $audit->record(
                'security.user.companies_updated',
                $this->record,
                companyId: $this->record->default_company_id,
                before: ['company_ids' => $this->administrationBefore['company_ids'] ?? []],
                after: ['company_ids' => $after['company_ids']],
            );
        }
    }
}
