<?php

namespace Webkul\Security;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rules\Password;
use Webkul\Security\Filament\Resources\UserResource;
use Webkul\Security\Models\Invitation;
use Webkul\Security\Models\User;
use Webkul\Security\Services\CompanyAdministratorAccount;
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
    }

    protected static function administratorSection(): Section
    {
        return Section::make(__('security::filament/contributions/company.administrator.title'))
            ->description(__('security::filament/contributions/company.administrator.description'))
            ->icon('heroicon-o-shield-check')
            ->visible(fn (): bool => static::administersInstallation())
            ->schema([
                Text::make(fn (?Company $record): HtmlString => static::administratorSummary($record))
                    ->visibleOn(['edit', 'view']),

                // On edit too since 2026-11-03 (user): an existing company had no
                // way to get an administrator, because this field only existed
                // on create. None of these is a company column: the create and
                // edit pages read them from the form state.
                TextInput::make('administrator_email')
                    ->label(__('security::filament/contributions/company.administrator.fields.email'))
                    ->helperText(fn (string $operation): string => __($operation === 'create'
                        ? 'security::filament/contributions/company.administrator.fields.email-helper'
                        : 'security::filament/contributions/company.administrator.fields.email-helper-edit'))
                    ->email()
                    ->maxLength(255)
                    ->visibleOn(['create', 'edit'])
                    ->dehydrated(false)
                    // Only create falls back to the company's address. On edit a
                    // blank field means "no change", or every save would invite.
                    ->placeholder(fn (Get $get, string $operation): ?string => $operation === 'create' ? (string) $get('email') : null)
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
                    ->visibleOn(['create', 'edit'])
                    ->dehydrated(false)
                    ->live(onBlur: true),

                // On by default: a password someone else chose should be
                // replaced by its owner, and the RequirePasswordChange middleware
                // enforces it at their next sign-in.
                Toggle::make('administrator_must_change_password')
                    ->label(__('security::filament/contributions/company.administrator.fields.must-change-password'))
                    ->default(true)
                    ->visible(fn (Get $get, string $operation): bool => in_array($operation, ['create', 'edit'], true)
                        && filled($get('administrator_password')))
                    ->dehydrated(false),
            ])
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
     * Who administers this company today.
     *
     * Read past the global scopes: Users are company-scoped and ownership
     * scoped, so a scoped read would quietly show nothing for exactly the
     * support case this section exists to serve.
     */
    protected static function administratorSummary(?Company $record): HtmlString
    {
        if (! $record instanceof Company || ! $record->exists) {
            return new HtmlString(e(__('security::filament/contributions/company.administrator.none')));
        }

        // One definition of "administers this company", shared with the
        // password reset, which must refuse anyone this list does not show.
        $admins = app(CompanyAdministratorAccount::class)->administrators($record);

        $lines = [];

        foreach ($admins as $admin) {
            $lines[] = sprintf(
                '<li><strong>%s</strong> &mdash; %s%s<br><a href="%s" class="fi-link" target="_blank">%s</a></li>',
                e($admin->name),
                e($admin->email),
                $admin->is_active
                    ? ''
                    : ' <em>('.e(__('security::filament/contributions/company.administrator.inactive')).')</em>',
                e(static::userEditUrl($admin)),
                e(__('security::filament/contributions/company.administrator.manage')),
            );
        }

        // An invitation nobody has accepted yet is the normal state for a
        // company created moments ago, and the support question it answers -
        // "why can nobody sign in?" - is the whole point of showing it.
        //
        // Since SAAS-06 the row survives acceptance (so the history is
        // answerable and a seat count has something to count), which means
        // "a row exists" is no longer the same as "still pending": the scope is
        // what says it.
        $pending = Invitation::query()
            ->where('default_company_id', $record->getKey())
            ->pending()
            ->orderBy('id')
            ->pluck('email');

        foreach ($pending as $email) {
            $lines[] = sprintf(
                '<li>%s &mdash; <em>%s</em></li>',
                e($email),
                e(__('security::filament/contributions/company.administrator.pending')),
            );
        }

        if ($lines === []) {
            return new HtmlString(e(__('security::filament/contributions/company.administrator.none')));
        }

        return new HtmlString('<ul class="fi-list">'.implode('', $lines).'</ul>');
    }

    /**
     * A direct link to the user, for everything about them except the password.
     *
     * *Superseded in part 2026-11-03 (user):* this used to be the only way to
     * change an administrator's password, on the grounds that credential
     * handling should have one implementation. The company form now sets it
     * too, through CompanyAdministratorAccount, which uses the same password
     * rule and must-change flag as the Users page.
     */
    protected static function userEditUrl(User $user): string
    {
        return UserResource::getUrl('edit', ['record' => $user->getKey()]);
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
