<?php

namespace Webkul\Security;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Webkul\Security\Filament\Resources\UserResource;
use Webkul\Security\Models\Invitation;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Support\Filament\Resources\CompanyResource\Support\CompanySchemaRegistry;
use Webkul\Support\Models\Company;

/**
 * The "Administrator" section on the company form.
 *
 * Two jobs, both asked for by the user (2026-10-23): let a new company's
 * administrator be invited at an address of their own rather than the
 * company's shared mailbox, and let support see who administers an existing
 * company instead of searching the Users list for it.
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
    }

    protected static function administratorSection(): Section
    {
        return Section::make(__('security::filament/contributions/company.administrator.title'))
            ->description(__('security::filament/contributions/company.administrator.description'))
            ->icon('heroicon-o-shield-check')
            ->visible(fn (): bool => static::administersInstallation())
            ->schema([
                TextInput::make('administrator_email')
                    ->label(__('security::filament/contributions/company.administrator.fields.email'))
                    ->helperText(__('security::filament/contributions/company.administrator.fields.email-helper'))
                    ->email()
                    ->maxLength(255)
                    ->visibleOn('create')
                    // Not a company column. The create page reads it from the
                    // form state and hands it to CompanyAdministratorInviter.
                    ->dehydrated(false)
                    ->placeholder(fn (Get $get): string => (string) $get('email')),

                Text::make(fn (?Company $record): HtmlString => static::administratorSummary($record))
                    ->visibleOn(['edit', 'view']),
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

        $admins = User::query()
            ->withoutGlobalScopes()
            ->where('default_company_id', $record->getKey())
            ->whereHas('roles', fn ($query) => $query
                ->whereRaw('LOWER(name) = ?', [mb_strtolower(Role::COMPANY_ADMIN)]))
            ->orderBy('id')
            ->get();

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

        // An invitation that nobody has accepted yet is the normal state for a
        // company created moments ago, and the support question it answers -
        // "why can nobody sign in?" - is the whole point of showing it.
        //
        // A surviving row *is* a pending invitation: user_invitations has no
        // accepted_at column, because UserInvitationService deletes the row on
        // acceptance.
        $pending = Invitation::query()
            ->where('default_company_id', $record->getKey())
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
     * A direct link to the user, which is where passwords are changed.
     *
     * Deliberately a link rather than password fields repeated here: the Users
     * form already does confirmation, the minimum length and the
     * must-change-password flag, and credential handling should have one
     * implementation rather than two.
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
    protected static function administersInstallation(): bool
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            return false;
        }

        return $user->isSuperAdmin() || $user->isMultiCompanyAdmin();
    }
}
