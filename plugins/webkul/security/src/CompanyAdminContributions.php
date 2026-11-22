<?php

namespace Webkul\Security;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Webkul\Security\Filament\Resources\CompanyResource\Schemas\AdministratorPanel;
use Webkul\Security\Models\User;
use Webkul\Support\Filament\Resources\CompanyResource\Support\CompanySchemaRegistry;
use Webkul\Support\Models\Company;

/**
 * The "Administrator" section on the company form.
 *
 * Two jobs, both asked for by the user (2026-10-23): let a new company's
 * administrator be invited at an address of their own rather than the
 * company's shared mailbox, and let support see who administers an existing
 * company instead of searching the Users list for it. A third since
 * 2026-11-03 (user): set or reset the administrator's password, on create and
 * on edit, for clients who phone DEKA to have it done.
 *
 * Contributed through CompanySchemaRegistry rather than by overriding the
 * company form: `support` is the base plugin and must not reference `security`.
 * CompanyAdministratorInviter's docblock deferred the dedicated field because
 * "the company form is inherited and monolithic" - the registry is what removed
 * that obstacle.
 */
class CompanyAdminContributions
{
    /**
     * Guarded because schema-registry contributions accumulate and
     * packageBooted() can run more than once (the install flow and the test
     * bootstrapper both re-boot plugin providers), which would render the
     * section twice.
     */
    protected static bool $registered = false;

    public static function register(): void
    {
        if (static::$registered) {
            return;
        }

        static::$registered = true;

        CompanySchemaRegistry::form('main.after', fn (): array => [static::administratorSection()]);
        CompanySchemaRegistry::form('main.after', fn (): array => [static::accountTypeSection()]);
        CompanySchemaRegistry::form('main.after', fn (): array => [static::currenciesSection()]);
    }

    protected static function administratorSection(): Section
    {
        return Section::make(__('security::filament/contributions/company.administrator.title'))
            ->description(__('security::filament/contributions/company.administrator.description'))
            ->icon('heroicon-o-shield-check')
            ->visible(fn (): bool => static::administersInstallation())
            ->key('companyAdministratorSection', isInheritable: false)
            // Edit and view (user, 2026-11-22, A + B + C): a row per
            // administrator with its own actions, "Add administrator" here,
            // and pending invitations with Resend and Copy link. Supersedes
            // the 2026-11-03 email and password fields on edit, whose action
            // depended on which of them was filled.
            ->afterHeader([AdministratorPanel::addAction()])
            ->schema([
                Group::make(fn (?Company $record): array => AdministratorPanel::rows($record))
                    ->visibleOn(['edit', 'view']),

                // Create only. None of these is a company column: the create
                // page reads them from the form state.
                TextInput::make('administrator_email')
                    ->label(__('security::filament/contributions/company.administrator.fields.email'))
                    ->helperText(__('security::filament/contributions/company.administrator.fields.email-helper'))
                    ->email()
                    ->maxLength(255)
                    ->visibleOn('create')
                    ->dehydrated(false)
                    // Create falls back to the company's address.
                    ->placeholder(fn (Get $get): string => (string) $get('email'))
                    ->requiredWith('administrator_password'),

                // Clients phone DEKA to have their administrator's password set
                // or reset (user, 2026-11-03). Supersedes "passwords only on the
                // Users page"; the password is set by CompanyAdministratorAccount
                // and never emailed. Same rule as the Users page's change action.
                TextInput::make('administrator_password')
                    ->label(__('security::filament/contributions/company.administrator.fields.password'))
                    ->helperText(__('security::filament/contributions/company.administrator.fields.password-helper'))
                    ->password()
                    ->revealable()
                    ->rule(Password::default())
                    ->maxLength(255)
                    ->visibleOn('create')
                    ->dehydrated(false)
                    ->live(onBlur: true),

                // CR-4: only with a password. An invited administrator types
                // their own name when accepting; without this field a password
                // account was named after its mailbox. Create only: on edit the
                // person already exists and keeps the name they chose.
                TextInput::make('administrator_name')
                    ->label(__('security::filament/contributions/company.administrator.fields.name'))
                    ->maxLength(255)
                    ->visible(fn (Get $get, string $operation): bool => $operation === 'create' && filled($get('administrator_password')))
                    ->dehydrated(false),

                // On by default: a password someone else chose should be
                // replaced by its owner, and the RequirePasswordChange middleware
                // enforces it at their next sign-in.
                Toggle::make('administrator_must_change_password')
                    ->label(__('security::filament/contributions/company.administrator.fields.must-change-password'))
                    ->default(true)
                    ->visible(fn (Get $get, string $operation): bool => $operation === 'create'
                        && filled($get('administrator_password')))
                    ->dehydrated(false),
            ])
            ->columns(1);
    }

    /**
     * Extra transaction currencies (CR-6, 2026-11-13). Installation staff
     * choose them, not the company's own administrator (plan decision): a
     * hidden field is not saved by Filament, so a Company Admin's save cannot
     * change the list. Empty keeps every active currency available.
     */
    protected static function currenciesSection(): Section
    {
        return Section::make(__('security::filament/contributions/company.currencies.title'))
            ->description(__('security::filament/contributions/company.currencies.description'))
            ->icon('heroicon-o-currency-dollar')
            ->visible(fn (): bool => static::administersInstallation())
            ->schema([
                Select::make('permittedCurrencies')
                    ->label(__('security::filament/contributions/company.currencies.fields.permitted'))
                    ->relationship(
                        name: 'permittedCurrencies',
                        titleAttribute: 'full_name',
                        modifyQueryUsing: fn ($query) => $query->where('active', true),
                    )
                    ->multiple()
                    ->searchable()
                    ->preload(),
            ])
            ->collapsible()
            ->columns(1);
    }

    /**
     * Chosen once, at creation, by installation staff (D-SAAS-11). A tenant
     * Company Admin never sees it. The day count is only for a test account.
     */
    protected static function accountTypeSection(): Section
    {
        return Section::make(__('security::filament/contributions/company.account.title'))
            ->description(__('security::filament/contributions/company.account.description'))
            ->icon('heroicon-o-beaker')
            ->visible(fn (string $operation): bool => $operation === 'create' && static::administersInstallation())
            ->schema([
                Select::make('account_type')
                    ->label(__('security::filament/contributions/company.account.fields.type'))
                    ->options([
                        'commercial' => __('security::filament/contributions/company.account.fields.commercial'),
                        'test'       => __('security::filament/contributions/company.account.fields.test'),
                    ])
                    ->default('commercial')
                    ->required()
                    ->native(false)
                    ->live(),

                TextInput::make('test_days')
                    ->label(__('security::filament/contributions/company.account.fields.days'))
                    ->helperText(__('security::filament/contributions/company.account.fields.days-helper'))
                    ->integer()
                    ->minValue(1)
                    ->required(fn (Get $get): bool => $get('account_type') === 'test')
                    ->visible(fn (Get $get): bool => $get('account_type') === 'test'),
            ])
            ->columns(1);
    }

    /**
     * Only an actor who administers the whole installation may see this.
     *
     * A company's own administrator has no business reading or reaching the
     * other administrators of their company through this section.
     */
    public static function administersInstallation(): bool
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            return false;
        }

        return $user->isSuperAdmin() || $user->isMultiCompanyAdmin();
    }
}
