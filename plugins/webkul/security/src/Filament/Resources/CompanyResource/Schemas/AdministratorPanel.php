<?php

namespace Webkul\Security\Filament\Resources\CompanyResource\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Enums\FontWeight;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Password;
use Webkul\Security\Filament\Resources\UserResource;
use Webkul\Security\Models\Invitation;
use Webkul\Security\Models\User;
use Webkul\Security\Services\CompanyAdministratorAccount;
use Webkul\Security\Services\CompanyAdministratorInviter;
use Webkul\Support\Models\Company;

/**
 * The company form's Administrator section on edit and view (user,
 * 2026-11-22, options A + B + C, kept in place): a row per administrator with
 * its own actions, an "Add administrator" window, and each pending invitation
 * with Resend and Copy link.
 *
 * It replaces a plain list plus two loose fields whose meaning changed with
 * what was typed (invite, create, or reset). Every write still goes through
 * CompanyAdministratorAccount or CompanyAdministratorInviter, which hold the
 * membership, seat and assignment rules; a hidden button here is never the
 * guard. The section itself is visible to installation staff only
 * (CompanyAdminContributions::administersInstallation()).
 */
class AdministratorPanel
{
    public const LANG = 'security::filament/contributions/company.administrator';

    protected const OUTCOME_LANG = 'security::filament/resources/company.pages';

    /**
     * One row per administrator, then one per pending invitation.
     *
     * @return array<int, Component>
     */
    public static function rows(?Company $record): array
    {
        if (! $record instanceof Company || ! $record->exists) {
            return [];
        }

        $rows = [];

        // One definition of "administers this company", shared with the
        // password reset, which refuses anyone this list does not show.
        foreach (app(CompanyAdministratorAccount::class)->administrators($record) as $administrator) {
            $rows[] = static::administratorRow($record, $administrator);
        }

        // Pending, not "a row exists": since SAAS-06 the row survives
        // acceptance, and an expired link can no longer be resent.
        $invitations = Invitation::query()
            ->where('default_company_id', $record->getKey())
            ->pending()
            ->orderBy('id')
            ->get();

        foreach ($invitations as $invitation) {
            $rows[] = static::invitationRow($record, $invitation);
        }

        if ($rows === []) {
            $rows[] = Text::make(__(static::LANG.'.none'))->color('gray');
        }

        return $rows;
    }

    protected static function administratorRow(Company $company, User $administrator): Flex
    {
        $id = (int) $administrator->getKey();

        return Flex::make([
            Group::make([
                Text::make($administrator->name)->weight(FontWeight::SemiBold),
                Text::make($administrator->email)->color('gray')->copyable(),
            ]),
            Text::make(__(static::LANG.($administrator->is_active ? '.status.active' : '.status.inactive')))
                ->badge()
                ->color($administrator->is_active ? 'success' : 'gray')
                ->grow(false),
            Actions::make([
                static::setPasswordAction($company, $administrator),
                Action::make("openAdministrator{$id}")
                    ->label(__(static::LANG.'.manage'))
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('gray')
                    ->link()
                    ->url(UserResource::getUrl('edit', ['record' => $id]))
                    ->openUrlInNewTab(),
            ])->key("administrator-{$id}-actions")->grow(false),
        ])->from('md')->verticallyAlignCenter()->key("administrator-{$id}", isInheritable: false);
    }

    protected static function invitationRow(Company $company, Invitation $invitation): Flex
    {
        $id = (int) $invitation->getKey();

        return Flex::make([
            Group::make([
                Text::make($invitation->email)->weight(FontWeight::SemiBold),
                Text::make(__(static::LANG.'.invitation.waiting'))->color('gray'),
            ]),
            Text::make($invitation->expires_at
                ? __(static::LANG.'.invitation.expires', ['date' => $invitation->expires_at->translatedFormat('j M Y')])
                : __(static::LANG.'.invitation.pending'))
                ->badge()
                ->color('warning')
                ->grow(false),
            Actions::make([
                Action::make("resendInvitation{$id}")
                    ->label(__(static::LANG.'.invitation.resend'))
                    ->icon('heroicon-o-paper-airplane')
                    ->link()
                    ->requiresConfirmation()
                    ->modalHeading(__('security::filament/resources/company.provisioning.resend.heading'))
                    ->modalDescription(__('security::filament/resources/company.provisioning.resend.description', ['email' => $invitation->email]))
                    ->action(function () use ($id): void {
                        $actor = Auth::user();
                        $invitation = Invitation::query()->find($id);

                        // The inviter re-checks who may manage the invitation
                        // and that it is still pending.
                        $reason = $actor instanceof User && $invitation instanceof Invitation
                            ? app(CompanyAdministratorInviter::class)->resend($invitation, $actor)
                            : CompanyAdministratorInviter::INVITE_FAILED;

                        Notification::make()
                            ->title(__('security::filament/resources/company.provisioning.resend.'.$reason))
                            ->{$reason === CompanyAdministratorInviter::INVITED ? 'success' : 'danger'}()
                            ->send();
                    }),
                // The link signs someone in as this company's administrator,
                // so it is read on demand through pendingLink(), which allows
                // only installation staff for this company, never rendered
                // into the page.
                Action::make("copyInvitationLink{$id}")
                    ->label(__(static::LANG.'.invitation.copy-link'))
                    ->icon('heroicon-o-link')
                    ->color('gray')
                    ->link()
                    ->modalHeading(__(static::LANG.'.invitation.copy-heading', ['email' => $invitation->email]))
                    ->modalDescription(__(static::LANG.'.invitation.copy-description'))
                    ->fillForm(function () use ($company, $id): array {
                        $actor = Auth::user();

                        try {
                            return ['link' => $actor instanceof User
                                ? app(CompanyAdministratorInviter::class)->pendingLink($company, $actor, $id)
                                : ''];
                        } catch (AuthorizationException) {
                            return ['link' => ''];
                        }
                    })
                    ->schema([
                        TextInput::make('link')
                            ->label(__(static::LANG.'.invitation.link'))
                            ->readOnly()
                            ->copyable(),
                    ])
                    ->modalSubmitAction(false),
            ])->key("invitation-{$id}-actions")->grow(false),
        ])->from('md')->verticallyAlignCenter()->key("invitation-{$id}", isInheritable: false);
    }

    protected static function setPasswordAction(Company $company, User $administrator): Action
    {
        return Action::make('setAdministratorPassword'.$administrator->getKey())
            ->label(__(static::LANG.'.set-password.label'))
            ->icon('heroicon-o-key')
            ->link()
            ->modalHeading(__(static::LANG.'.set-password.heading', ['email' => $administrator->email]))
            ->modalDescription(__(static::LANG.'.set-password.description'))
            // Hidden where the Users page would refuse too; setPasswordFor()
            // re-checks membership and the Users policy.
            ->visible(fn (): bool => Gate::allows('update', $administrator))
            ->schema([
                TextInput::make('password')
                    ->label(__(static::LANG.'.fields.password'))
                    ->password()
                    ->revealable()
                    ->required()
                    ->rule(Password::default())
                    ->maxLength(255),
                Toggle::make('must_change_password')
                    ->label(__(static::LANG.'.fields.must-change-password'))
                    ->default(true),
            ])
            ->action(function (array $data) use ($company, $administrator): void {
                $actor = Auth::user();

                if (! $actor instanceof User) {
                    return;
                }

                $reason = app(CompanyAdministratorAccount::class)->setPasswordFor(
                    $company,
                    $actor,
                    $administrator,
                    (string) $data['password'],
                    (bool) ($data['must_change_password'] ?? true),
                );

                static::notifyPasswordOutcome($reason, (string) $administrator->email);
            });
    }

    /**
     * "Add administrator" in the section header: invite them, or set a
     * password now for clients who phone DEKA to have it done (user,
     * 2026-11-03). Choosing the method explicitly replaces guessing it from
     * which loose fields were filled.
     */
    public static function addAction(): Action
    {
        return Action::make('addCompanyAdministrator')
            ->label(__(static::LANG.'.add.label'))
            ->icon('heroicon-o-user-plus')
            ->button()
            ->size('sm')
            ->visible(fn (?Company $record): bool => $record instanceof Company && $record->exists)
            ->modalHeading(__(static::LANG.'.add.heading'))
            ->modalSubmitActionLabel(__(static::LANG.'.add.submit'))
            ->schema([
                Radio::make('method')
                    ->label(__(static::LANG.'.add.method'))
                    ->options([
                        'invite'   => __(static::LANG.'.add.invite'),
                        'password' => __(static::LANG.'.add.password'),
                    ])
                    ->descriptions([
                        'invite'   => __(static::LANG.'.add.invite-description'),
                        'password' => __(static::LANG.'.add.password-description'),
                    ])
                    ->default('invite')
                    ->required()
                    ->live(),
                TextInput::make('email')
                    ->label(__(static::LANG.'.fields.email'))
                    ->email()
                    ->required()
                    ->maxLength(255),
                TextInput::make('name')
                    ->label(__(static::LANG.'.fields.name'))
                    ->maxLength(255)
                    ->visible(fn (Get $get): bool => $get('method') === 'password'),
                TextInput::make('password')
                    ->label(__(static::LANG.'.fields.password'))
                    ->password()
                    ->revealable()
                    ->required()
                    ->rule(Password::default())
                    ->maxLength(255)
                    ->visible(fn (Get $get): bool => $get('method') === 'password'),
                Toggle::make('must_change_password')
                    ->label(__(static::LANG.'.fields.must-change-password'))
                    ->default(true)
                    ->visible(fn (Get $get): bool => $get('method') === 'password'),
            ])
            ->action(function (array $data, Company $record): void {
                $actor = Auth::user();

                if (! $actor instanceof User) {
                    return;
                }

                $email = trim((string) $data['email']);

                if (($data['method'] ?? 'invite') === 'password') {
                    $reason = app(CompanyAdministratorAccount::class)->setPassword(
                        $record,
                        $actor,
                        $email,
                        (string) $data['password'],
                        (bool) ($data['must_change_password'] ?? true),
                        filled($data['name'] ?? null) ? (string) $data['name'] : null,
                    );

                    static::notifyPasswordOutcome($reason, $email);

                    return;
                }

                static::notifyInvitationOutcome(
                    app(CompanyAdministratorInviter::class)->invite($record, $actor, $email),
                    $email,
                );
            });
    }

    /** One wording for every place that sets an administrator's password. */
    public static function notifyPasswordOutcome(string $reason, string $email): void
    {
        $lang = static::OUTCOME_LANG.'.administrator-account';

        if (in_array($reason, [CompanyAdministratorAccount::CREATED, CompanyAdministratorAccount::PASSWORD_RESET], true)) {
            Notification::make('companyAdministratorPasswordSet')
                ->title(__($lang.'.'.$reason.'.title'))
                ->body(__($lang.'.'.$reason.'.body', ['email' => $email]))
                ->success()
                ->send();

            return;
        }

        // Persistent: nobody can sign in yet, and the reason has to stay on
        // screen long enough to be acted on.
        Notification::make('companyAdministratorPasswordRefused')
            ->title(__($lang.'.refused.title'))
            ->body(__($lang.'.refused.'.$reason, ['email' => $email]))
            ->warning()
            ->persistent()
            ->send();
    }

    public static function notifyInvitationOutcome(string $reason, string $email): void
    {
        if ($reason === CompanyAdministratorInviter::INVITED) {
            Notification::make('companyAdministratorInvited')
                ->title(__(static::OUTCOME_LANG.'.create.administrator.invited.title'))
                ->body(__(static::OUTCOME_LANG.'.create.administrator.invited.body', ['email' => $email]))
                ->success()
                ->send();

            return;
        }

        $lang = static::OUTCOME_LANG.'.edit.administrator.skipped';

        Notification::make('companyAdministratorNotInvited')
            ->title(__($lang.'.title'))
            // A failed email still leaves a pending row; its link is on the
            // row's Copy link action, so it is not repeated in a notification.
            ->body(__($lang.'.'.$reason, ['email' => $email, 'link' => '']))
            ->warning()
            ->persistent()
            ->send();
    }
}
