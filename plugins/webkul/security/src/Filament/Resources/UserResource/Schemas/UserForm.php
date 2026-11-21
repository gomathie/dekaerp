<?php

namespace Webkul\Security\Filament\Resources\UserResource\Schemas;

use Closure;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Filament\Resources\CompanyResource;
use Webkul\Security\Filament\Resources\TeamResource;
use Webkul\Security\Filament\Resources\UserResource;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Security\Services\CompanyAdministratorInviter;
use Webkul\Support\Models\Company;
use Webkul\Support\Services\Usernames;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make(__('security::filament/resources/user.form.sections.general-information.title'))
                                    ->schema([
                                        TextInput::make('name')
                                            ->label(__('security::filament/resources/user.form.sections.general-information.fields.name'))
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true),
                                        TextInput::make('email')
                                            ->label(__('security::filament/resources/user.form.sections.general-information.fields.email'))
                                            ->email()
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255),
                                        // Optional sign-in name (user, 2026-11-19); rules in Usernames.
                                        TextInput::make('username')
                                            ->label(__('security::filament/resources/user.form.sections.general-information.fields.username'))
                                            ->helperText(__('security::filament/resources/user.form.sections.general-information.fields.username-helper'))
                                            ->maxLength(50)
                                            ->rules(fn (?User $record): array => Usernames::rules($record?->getKey()))
                                            ->dehydrateStateUsing(fn (?string $state): ?string => Usernames::normalize($state)),
                                        TextInput::make('password')
                                            ->label(__('security::filament/resources/user.form.sections.general-information.fields.password'))
                                            ->password()
                                            ->required()
                                            ->revealable(filament()->arePasswordsRevealable())
                                            ->hiddenOn('edit')
                                            ->maxLength(255)
                                            ->rule('min:8'),
                                        TextInput::make('password_confirmation')
                                            ->label(__('security::filament/resources/user.form.sections.general-information.fields.password-confirmation'))
                                            ->password()
                                            ->revealable(filament()->arePasswordsRevealable())
                                            ->hiddenOn('edit')
                                            ->rule('required', fn ($get) => (bool) $get('password'))
                                            ->same('password'),
                                        // Shown on edit too, where the password
                                        // fields are not: it is the only way to
                                        // make an existing user rotate a
                                        // password that was shared with them.
                                        Toggle::make('must_change_password')
                                            ->label(__('security::filament/resources/user.form.sections.general-information.fields.must-change-password'))
                                            ->helperText(__('security::filament/resources/user.form.sections.general-information.fields.must-change-password-helper'))
                                            ->default(false)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),

                                Section::make(__('security::filament/resources/user.form.sections.permissions.title'))
                                    ->schema([
                                        Select::make('roles')
                                            ->label(__('security::filament/resources/user.form.sections.permissions.fields.roles'))
                                            /*
                                             * Deliberately not `->relationship('roles')`.
                                             * Tenant roles live on company memberships, so the
                                             * global pivot is only half the answer, and letting
                                             * Filament sync it would detach every tenant role the
                                             * user already holds the moment the form is saved.
                                             * TenantRoleAssigner writes both stores.
                                             */
                                            ->options(fn (): array => UserResource::assignableRoleOptions())
                                            ->multiple()
                                            ->required()
                                            ->preload()
                                            ->dehydrated(false)
                                            ->live()
                                            /*
                                             * "Company Admin" alone opens almost no screens; the business
                                             * role carries them (user, 2026-11-19: "Director" was created
                                             * with only Company Admin). Add it the moment Company Admin is
                                             * picked, from the same list the invitation uses, and only when
                                             * this person may assign it. It can still be removed by hand.
                                             */
                                            ->afterStateUpdated(function (Select $component, $state, $old): void {
                                                $state = array_map('intval', (array) $state);
                                                $administratorRoles = CompanyAdministratorInviter::administratorRoleIds();

                                                if ($administratorRoles === []
                                                    || ! in_array($administratorRoles[0], $state, true)
                                                    || in_array($administratorRoles[0], array_map('intval', (array) $old), true)) {
                                                    return;
                                                }

                                                $assignable = array_map('intval', array_keys(UserResource::assignableRoleOptions()));
                                                $extra = array_values(array_intersect(array_slice($administratorRoles, 1), $assignable));

                                                $component->state(array_values(array_unique([...$state, ...$extra])));
                                            })
                                            ->afterStateHydrated(function (Select $component, ?User $record): void {
                                                $component->state($record ? UserResource::currentRoleIds($record) : []);
                                            })
                                            ->rule(function (?User $record) {
                                                return function (string $attribute, $value, Closure $fail) use ($record): void {
                                                    try {
                                                        UserResource::ensureAdminRoleConstraints($record, (array) $value);
                                                    } catch (ValidationException $exception) {
                                                        $messages = $exception->errors()['roles'] ?? [];

                                                        foreach ($messages as $message) {
                                                            $fail($message);
                                                        }
                                                    }
                                                };
                                            })
                                            ->searchable(),
                                        Select::make('resource_permission')
                                            ->label(__('security::filament/resources/user.form.sections.permissions.fields.resource-permission'))
                                            ->live()
                                            ->options(PermissionType::class)
                                            ->required()
                                            ->disabled(fn (?User $record): bool => filled($record) && Auth::id() === $record->id)
                                            ->dehydrated(fn (?User $record): bool => ! (filled($record) && Auth::id() === $record->id))
                                            ->helperText(fn (?User $record): ?string => filled($record) && Auth::id() === $record->id
                                                ? __('security::filament/resources/user.form.sections.permissions.fields.resource-permission-self-change-disabled')
                                                : null)
                                            ->preload()
                                            ->default(PermissionType::GLOBAL->value)
                                            ->afterStateUpdated(function (Get $get, Set $set) {
                                                if ($get('resource_permission') != PermissionType::GROUP) {
                                                    $set('teams', []);
                                                }
                                            })
                                            ->searchable(),
                                        Select::make('teams')
                                            ->label(__('security::filament/resources/user.form.sections.permissions.fields.teams'))
                                            ->relationship(
                                                name: 'teams',
                                                titleAttribute: 'name'
                                            )
                                            ->live()
                                            ->multiple()
                                            ->preload()
                                            ->searchable()
                                            ->required(fn (Get $get) => $get('resource_permission') == PermissionType::GROUP)
                                            ->createOptionForm(fn (Schema $schema) => TeamResource::form($schema)),
                                        /*
                                         * Dashboard switches (user, 2026-11-20), read by
                                         * Support\Filament\DashboardAccess. Set by whoever may
                                         * edit this user (Company Admin and up); never on one's
                                         * own record, so nobody turns their own back on.
                                         */
                                        Toggle::make('can_see_dashboard')
                                            ->label(__('security::filament/resources/user.form.sections.permissions.fields.can-see-dashboard'))
                                            ->helperText(__('security::filament/resources/user.form.sections.permissions.fields.can-see-dashboard-helper'))
                                            ->default(true)
                                            ->live()
                                            ->disabled(fn (?User $record): bool => filled($record) && Auth::id() === $record->id)
                                            ->dehydrated(fn (?User $record): bool => ! (filled($record) && Auth::id() === $record->id)),
                                        Toggle::make('can_see_money_figures')
                                            ->label(__('security::filament/resources/user.form.sections.permissions.fields.can-see-money-figures'))
                                            ->helperText(__('security::filament/resources/user.form.sections.permissions.fields.can-see-money-figures-helper'))
                                            ->default(true)
                                            ->visible(fn (Get $get): bool => (bool) $get('can_see_dashboard'))
                                            ->disabled(fn (?User $record): bool => filled($record) && Auth::id() === $record->id)
                                            ->dehydrated(fn (?User $record): bool => ! (filled($record) && Auth::id() === $record->id)),
                                    ])
                                    ->columns(2),
                            ])
                            ->columnSpan(['lg' => 2]),
                        Group::make()
                            ->schema([
                                Section::make(__('security::filament/resources/user.form.sections.avatar.title'))
                                    ->relationship('partner', 'avatar')
                                    ->schema([
                                        FileUpload::make('avatar')
                                            ->hiddenLabel()
                                            ->automaticallyResizeImagesMode('cover')
                                            ->image()
                                            ->imageEditor()
                                            ->directory('users/avatars')
                                            ->visibility('public'),
                                    ])
                                    ->columns(1),
                                Section::make(__('security::filament/resources/user.form.sections.lang-and-status.title'))
                                    ->schema([
                                        Select::make('language')
                                            ->label(__('security::filament/resources/user.form.sections.lang-and-status.fields.language'))
                                            ->options(
                                                collect(config('app.supported_locales', []))
                                                    ->mapWithKeys(fn ($meta, $code) => [
                                                        $code => ($meta['native'] ?? $code).' ('.($meta['label'] ?? $code).')',
                                                    ])
                                                    ->all()
                                            )
                                            ->default(config('app.locale'))
                                            ->native(false)
                                            ->searchable(),
                                        Toggle::make('is_active')
                                            ->label(__('security::filament/resources/user.form.sections.lang-and-status.fields.status'))
                                            ->default(true),
                                    ])
                                    ->columns(1),
                                Section::make(__('security::filament/resources/user.form.sections.multi-company.title'))
                                    ->schema([
                                        Select::make('allowed_companies')
                                            ->label(__('security::filament/resources/user.form.sections.multi-company.allowed-companies'))
                                            ->relationship(
                                                'allowedCompanies',
                                                'name',
                                                fn (Builder $query): Builder => UserResource::scopeAssignableCompanies($query),
                                            )
                                            ->multiple()
                                            // A new user starts in the creator's current company (TRACE365,
                                            // 2026-11-19): left empty, the required default company stopped
                                            // the save and Company Admins could not tell why.
                                            ->default(fn (): array => array_filter([current_company_id()]))
                                            ->preload()
                                            ->searchable(),
                                        Select::make('default_company_id')
                                            ->default(fn (): ?int => current_company_id())
                                            ->label(__('security::filament/resources/user.form.sections.multi-company.default-company'))
                                            ->relationship(
                                                'defaultCompany',
                                                'name',
                                                modifyQueryUsing: fn (Builder $query): Builder => UserResource::scopeAssignableCompanies($query)->withTrashed(),
                                            )
                                            ->getOptionLabelFromRecordUsing(function ($record): string {
                                                return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                            })
                                            ->disableOptionWhen(fn ($label) => str_contains($label, ' (Deleted)'))
                                            ->required()
                                            ->rule(fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                                $allowed = $get('allowed_companies') ?? [];

                                                if (! empty($value) && ! in_array((string) $value, array_map('strval', (array) $allowed), true)) {
                                                    $fail(__('security::filament/resources/user.form.sections.multi-company.default-company-not-allowed'));
                                                }
                                            })
                                            ->searchable()
                                            ->createOptionForm(fn (Schema $schema) => CompanyResource::form($schema))
                                            ->createOptionAction(function (Action $action) {
                                                $action
                                                    ->visible(fn (): bool => Auth::user()?->isSuperAdmin() ?? false)
                                                    ->fillForm(function (array $arguments): array {
                                                        return [
                                                            'user_id' => Auth::id(),
                                                        ];
                                                    })
                                                    ->mutateDataUsing(function (array $data) {
                                                        $data['user_id'] = Auth::id();

                                                        return $data;
                                                    });
                                            })
                                            ->afterStateHydrated(function (Select $component, $state) {
                                                if (empty($state)) {
                                                    $component->state(null);

                                                    return;
                                                }

                                                $company = Company::find($state);

                                                if (! $company) {
                                                    $component->state(null);
                                                }
                                            })
                                            ->preload(),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 1]),
                    ])
                    ->columns(3),
            ])
            ->columns(1);
    }
}
