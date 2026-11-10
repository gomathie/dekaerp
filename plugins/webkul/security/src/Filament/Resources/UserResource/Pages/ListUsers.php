<?php

namespace Webkul\Security\Filament\Resources\UserResource\Pages;

use Exception;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Webkul\Security\Filament\Resources\UserResource;
use Webkul\Security\Mail\UserInvitationMail;
use Webkul\Security\Models\Invitation;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Security\Services\CompanyAdministratorInviter;
use Webkul\Security\Services\MultiCompanyAdminService;
use Webkul\Security\Services\UserInvitationService;
use Webkul\Security\Settings\UserSettings;
use Webkul\Support\Models\Company;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    public function getTabs(): array
    {
        $query = static::getResource()::getEloquentQuery();

        return [
            'all' => Tab::make(__('security::filament/resources/user/pages/list-user.tabs.all'))
                ->badge($query->count()),
            'archived' => Tab::make(__('security::filament/resources/user/pages/list-user.tabs.archived'))
                ->badge($query->onlyTrashed()->count())
                ->modifyQueryUsing(function ($query) {
                    return $query->onlyTrashed();
                }),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-user-plus')
                ->label(__('security::filament/resources/user/pages/list-user.header-actions.create.label')),
            Action::make('inviteUser')
                ->label(__('security::filament/resources/user/pages/list-user.header-actions.invite.title'))
                ->icon('heroicon-o-envelope')
                ->modalIcon('heroicon-o-envelope')
                ->modalSubmitActionLabel(__('security::filament/resources/user/pages/list-user.header-actions.invite.modal.submit-action-label'))
                ->visible(fn (UserSettings $userSettings): bool => $userSettings->enable_user_invitation
                    && (Auth::user()?->can('create_security_user') ?? false))
                ->schema([
                    TextInput::make('email')
                        ->email()
                        ->label(__('security::filament/resources/user/pages/list-user.header-actions.invite.form.email'))
                        ->unique('users', 'email')
                        ->required(),
                    Select::make('company_id')
                        ->label(__('security::filament/resources/user.form.sections.multi-company.default-company'))
                        ->options(function (): array {
                            $actor = Auth::user();

                            if (! $actor instanceof User) {
                                return [];
                            }

                            return app(MultiCompanyAdminService::class)
                                ->scopeAssignableCompanies(Company::query(), $actor)
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->all();
                        })
                        ->default(fn (): ?int => current_company_id())
                        ->searchable()
                        ->required(),
                    Select::make('roles')
                        ->label(__('security::filament/resources/user.form.sections.permissions.fields.roles'))
                        ->options(function (): array {
                            $actor = Auth::user();

                            if (! $actor instanceof User) {
                                return [];
                            }

                            return app(MultiCompanyAdminService::class)
                                ->scopeAssignableRoles(Role::query(), $actor)
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->all();
                        })
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $actor = Auth::user();
                    abort_unless($actor instanceof User, 403);

                    $invitation = app(UserInvitationService::class)->create(
                        $actor,
                        $data['email'],
                        (int) $data['company_id'],
                        (array) $data['roles'],
                    );

                    try {
                        Mail::to($invitation->email)->send(new UserInvitationMail($invitation));

                        Notification::make('invitedSuccess')
                            ->title(__('security::filament/resources/user/pages/list-user.header-actions.invite.notification.success.title'))
                            ->body(__('security::filament/resources/user/pages/list-user.header-actions.invite.notification.success.body'))
                            ->success()
                            ->send();
                    } catch (Exception $e) {
                        report($e);

                        Notification::make('invitedFailed')
                            ->title(__('security::filament/resources/user/pages/list-user.header-actions.invite.notification.error.title'))
                            ->body(__('security::filament/resources/user/pages/list-user.header-actions.invite.notification.error.body'))
                            ->danger()
                            ->send();
                    }
                }),
            Action::make('resendInvitation')
                ->label(__('security::filament/resources/user/pages/list-user.header-actions.resend.title'))
                ->icon('heroicon-o-arrow-path')
                ->visible(fn (): bool => Auth::user()?->can('create_security_user') ?? false)
                ->schema([
                    Select::make('invitation_id')
                        ->label(__('security::filament/resources/user/pages/list-user.header-actions.invitation'))
                        ->options(fn (): array => $this->pendingInvitationOptions())
                        ->searchable()
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $actor = Auth::user();
                    abort_unless($actor instanceof User, 403);

                    // The options are only a convenience; the service checks
                    // the actor again, including a forged invitation id.
                    $invitation = Invitation::query()->findOrFail((int) $data['invitation_id']);
                    $reason = app(CompanyAdministratorInviter::class)->resend($invitation, $actor);

                    $notification = Notification::make('invitationResent')
                        ->title(__('security::filament/resources/user/pages/list-user.header-actions.resend.'.$reason))
                        ->body($reason === CompanyAdministratorInviter::MAIL_FAILED
                            ? __('security::filament/resources/user/pages/list-user.header-actions.resend.recovery', ['link' => $invitation->acceptUrl()])
                            : null);

                    if ($reason === CompanyAdministratorInviter::INVITED) {
                        $notification->success();
                    } else {
                        $notification->warning()->persistent();
                    }

                    $notification->send();
                }),
            Action::make('revokeInvitation')
                ->label(__('security::filament/resources/user/pages/list-user.header-actions.revoke.title'))
                ->icon('heroicon-o-x-mark')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn (): bool => Auth::user()?->can('create_security_user') ?? false)
                ->schema([
                    Select::make('invitation_id')
                        ->label(__('security::filament/resources/user/pages/list-user.header-actions.invitation'))
                        ->options(fn (): array => $this->pendingInvitationOptions())
                        ->searchable()
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $actor = Auth::user();
                    abort_unless($actor instanceof User, 403);

                    $invitation = Invitation::query()->findOrFail((int) $data['invitation_id']);
                    app(UserInvitationService::class)->revoke($invitation, $actor);

                    Notification::make('invitationRevoked')
                        ->title(__('security::filament/resources/user/pages/list-user.header-actions.revoke.done'))
                        ->success()
                        ->send();
                }),
        ];
    }

    /** @return array<int, string> */
    protected function pendingInvitationOptions(): array
    {
        $actor = Auth::user();

        if (! $actor instanceof User || ! $actor->can('create_security_user')) {
            return [];
        }

        $query = Invitation::query()->pending()->with('defaultCompany');

        if (! $actor->isSuperAdmin()) {
            $query->whereIn('default_company_id', app(MultiCompanyAdminService::class)->assignedCompanyIds($actor));
        }

        return $query->orderBy('email')->get()
            ->mapWithKeys(fn (Invitation $invitation): array => [
                (int) $invitation->getKey() => $invitation->email.' — '.$invitation->defaultCompany?->name,
            ])
            ->all();
    }
}
