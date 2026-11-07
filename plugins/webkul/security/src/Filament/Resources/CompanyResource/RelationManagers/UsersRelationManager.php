<?php

namespace Webkul\Security\Filament\Resources\CompanyResource\RelationManagers;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Password;
use Webkul\Security\CompanyAdminContributions;
use Webkul\Security\Models\User;
use Webkul\Security\Models\Invitation;
use Webkul\Security\Services\CompanyAdministratorAccount;
use Webkul\Security\Services\CompanyAdministratorInviter;

/**
 * The company's users and their logins, with a password button per user
 * (user, 2026-11-05): when invitation emails do not arrive, support sets each
 * user's password by hand from the company itself.
 *
 * A current password is never shown, because it cannot be: it is stored as a
 * one-way hash. What support needs is the login to hand over and a way to set
 * a new password, which is what this offers. Every write goes through
 * CompanyAdministratorAccount, so the membership boundary, the Users-page
 * policy and the audit apply exactly as they do to the Administrator section.
 */
class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $recordTitleAttribute = 'email';

    protected static string $lang = 'security::filament/resources/company/relation-managers/users';

    /**
     * @var array<int, int>|null
     */
    protected ?array $administratorIds = null;

    /**
     * Installation staff only, the same rule as the Administrator section. A
     * company's own administrator manages their people from the Users page,
     * where its policy applies; this list exists for DEKA support.
     */
    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return CompanyAdminContributions::administersInstallation();
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __(static::$lang.'.title');
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            // Users are ownership-scoped, which would show support only the
            // users they created themselves. The pivot join still limits this
            // to the company's members; deleted users are left out by hand
            // because removing the scopes also removes SoftDeletes'.
            ->modifyQueryUsing(fn (Builder $query): Builder => $query
                ->withoutGlobalScopes()
                ->whereNull('users.deleted_at'))
            ->columns([
                TextColumn::make('name')
                    ->label(__(static::$lang.'.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label(__(static::$lang.'.table.columns.login'))
                    ->copyable()
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_company_admin')
                    ->label(__(static::$lang.'.table.columns.company-admin'))
                    ->state(fn (User $record): bool => in_array((int) $record->getKey(), $this->administratorIds(), true))
                    ->boolean(),
                IconColumn::make('is_active')
                    ->label(__(static::$lang.'.table.columns.active'))
                    ->boolean(),
                IconColumn::make('must_change_password')
                    ->label(__(static::$lang.'.table.columns.must-change-password'))
                    ->boolean(),
            ])
            ->defaultSort('name')
            ->emptyStateHeading(__(static::$lang.'.table.empty-state.heading'))
            ->emptyStateDescription(__(static::$lang.'.table.empty-state.description'))
            ->headerActions([
                Action::make('copyInvitationLink')
                    ->label(__(static::$lang.'.table.actions.copy-invitation-link.label'))
                    ->icon('heroicon-o-link')
                    ->visible(fn (): bool => CompanyAdminContributions::administersInstallation())
                    ->schema([
                        Select::make('invitation_id')
                            ->label(__(static::$lang.'.table.actions.copy-invitation-link.invitation'))
                            ->options(fn (): array => Invitation::query()
                                ->where('default_company_id', $this->getOwnerRecord()->getKey())
                                ->pending()
                                ->pluck('email', 'id')->all())
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?string $state): void {
                                $actor = Auth::user();
                                $set('link', $actor instanceof User && $state
                                    ? app(CompanyAdministratorInviter::class)->pendingLink($this->getOwnerRecord(), $actor, (int) $state)
                                    : '');
                            }),
                        TextInput::make('link')
                            ->label(__(static::$lang.'.table.actions.copy-invitation-link.link'))
                            ->readOnly()
                            ->copyable(),
                    ])
                    ->modalSubmitAction(false),
            ])
            ->recordActions([
                Action::make('setPassword')
                    ->label(__(static::$lang.'.table.actions.set-password.label'))
                    ->icon('heroicon-o-key')
                    ->modalHeading(fn (User $record): string => __(static::$lang.'.table.actions.set-password.modal-heading', ['email' => $record->email]))
                    ->modalDescription(__(static::$lang.'.table.actions.set-password.modal-description'))
                    // Hidden where the Users page would refuse too; the service
                    // re-checks, since a hidden button is not a guard.
                    ->visible(fn (User $record): bool => Gate::allows('update', $record))
                    ->schema([
                        TextInput::make('password')
                            ->label(__(static::$lang.'.table.actions.set-password.form.password'))
                            ->password()
                            ->revealable()
                            ->required()
                            ->rule(Password::default())
                            ->maxLength(255),
                        Toggle::make('must_change_password')
                            ->label(__(static::$lang.'.table.actions.set-password.form.must-change-password'))
                            ->default(true),
                    ])
                    ->action(function (User $record, array $data): void {
                        $actor = Auth::user();

                        if (! $actor instanceof User) {
                            return;
                        }

                        $reason = app(CompanyAdministratorAccount::class)->setPasswordFor(
                            $this->getOwnerRecord(),
                            $actor,
                            $record,
                            (string) $data['password'],
                            (bool) ($data['must_change_password'] ?? true),
                        );

                        $lang = 'security::filament/resources/company.pages.administrator-account';

                        if ($reason === CompanyAdministratorAccount::PASSWORD_RESET) {
                            Notification::make()
                                ->title(__($lang.'.password-reset.title'))
                                ->body(__($lang.'.password-reset.body', ['email' => $record->email]))
                                ->success()
                                ->send();

                            return;
                        }

                        Notification::make()
                            ->title(__($lang.'.refused.title'))
                            ->body(__($lang.'.refused.'.$reason, ['email' => $record->email]))
                            ->warning()
                            ->persistent()
                            ->send();
                    }),
            ]);
    }

    /**
     * Read once per render rather than once per row.
     *
     * @return array<int, int>
     */
    protected function administratorIds(): array
    {
        return $this->administratorIds ??= app(CompanyAdministratorAccount::class)
            ->administrators($this->getOwnerRecord())
            ->map(fn (User $user): int => (int) $user->getKey())
            ->all();
    }
}
