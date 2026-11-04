<?php

namespace Webkul\Security\Services;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\Invitation;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

/**
 * Sets a company administrator's password from the company form.
 *
 * Asked for by the user (2026-11-03): clients phone DEKA to have their
 * administrator's password set or reset, and an existing company had no way to
 * get an administrator at all, because the company form only invited one on
 * create. This supersedes "the company form never handles a password"
 * (2026-10-23, CompanyAdminContributions). The invitation path is unchanged and
 * still the default when no password is given.
 *
 * The password is set here and **never emailed**: the person setting it hands
 * it over themselves. Like CompanyAdministratorInviter, every outcome is a
 * returned reason rather than an exception, because the company has already
 * been saved by the time a page calls this.
 */
class CompanyAdministratorAccount
{
    public const CREATED = 'created';

    public const PASSWORD_RESET = 'password-reset';

    public const MISSING_EMAIL = 'missing-email';

    public const MISSING_ROLE = 'missing-role';

    /**
     * The address belongs to someone who is not a member of this company.
     *
     * Refused rather than handled: resetting that password from this form would
     * let whoever administers this company sign in as a user of another one -
     * an account takeover across tenants. Their password belongs to the Users
     * page, where the policy for *that* user applies. (Until 2026-11-05 this
     * refused anyone but the company's administrators; see isMember().)
     */
    public const NOT_MEMBER = 'not-member';

    public const NOT_ALLOWED = 'not-allowed';

    public const SEATS_EXHAUSTED = 'seats-exhausted';

    public function __construct(
        protected MultiCompanyAdminService $administration,
        protected SecurityAuditLogger $audit,
    ) {}

    /**
     * Create this company's administrator with the given password, or reset
     * the password of the company's user at that address.
     *
     * @return self::CREATED|self::PASSWORD_RESET|self::MISSING_EMAIL|self::MISSING_ROLE|self::NOT_MEMBER|self::NOT_ALLOWED|self::SEATS_EXHAUSTED
     *
     * @throws ValidationException when the password breaks the password rules
     */
    public function setPassword(
        Company $company,
        User $actor,
        string $email,
        string $password,
        bool $mustChangePassword = true,
    ): string {
        $email = trim($email);

        if ($email === '') {
            return self::MISSING_EMAIL;
        }

        // The form applies the same rules; repeated here because this is the
        // only place the password is written, whoever calls it.
        Validator::make(
            ['email' => $email, 'password' => $password],
            ['email' => ['email', 'max:255'], 'password' => ['required', Password::default()]],
        )->validate();

        $roleId = CompanyAdministratorInviter::companyAdminRoleId();

        if (! $roleId) {
            return self::MISSING_ROLE;
        }

        // Case-insensitive, deleted users included: "Ann@x.com" and
        // "ann@x.com" are one mailbox, and a deleted user still owns the
        // address under the unique index.
        $existing = User::query()
            ->withoutGlobalScopes()
            ->withTrashed()
            ->whereRaw('LOWER(email) = ?', [mb_strtolower($email)])
            ->first();

        if ($existing instanceof User) {
            return $this->resetPassword($company, $actor, $existing, $password, $mustChangePassword);
        }

        return $this->createAdministrator($company, $actor, $roleId, $email, $password, $mustChangePassword);
    }

    /**
     * Everyone who administers the company today.
     *
     * Read past the global scopes: Users are company- and ownership-scoped, so
     * a scoped read would quietly find nobody for the support staff who use
     * this. Two stores since SAAS-01: a legacy global assignment (read against
     * the user's default company) and the per-company membership role.
     *
     * @return Collection<int, User>
     */
    public function administrators(Company $company): Collection
    {
        $resolver = app(TenantRoleResolver::class);

        $membershipAdminIds = $resolver->supported()
            ? $resolver->userIdsWithRoleInCompany(Role::COMPANY_ADMIN, (int) $company->getKey())
            : [];

        return User::query()
            ->withoutGlobalScopes()
            ->where(fn ($query) => $query
                ->whereIn('id', $membershipAdminIds)
                ->orWhere(fn ($legacy) => $legacy
                    ->where('default_company_id', $company->getKey())
                    ->whereHas('roles', fn ($roles) => $roles
                        ->whereRaw('LOWER(name) = ?', [mb_strtolower(Role::COMPANY_ADMIN)]))))
            ->orderBy('id')
            ->get();
    }

    /**
     * Set the password of one of the company's users, chosen from its Users
     * list (user, 2026-11-05: when invitation emails do not arrive, support
     * sets each user's password by hand).
     *
     * @return self::PASSWORD_RESET|self::NOT_MEMBER|self::NOT_ALLOWED
     *
     * @throws ValidationException when the password breaks the password rules
     */
    public function setPasswordFor(
        Company $company,
        User $actor,
        User $target,
        string $password,
        bool $mustChangePassword = true,
    ): string {
        Validator::make(
            ['password' => $password],
            ['password' => ['required', Password::default()]],
        )->validate();

        return $this->resetPassword($company, $actor, $target, $password, $mustChangePassword);
    }

    /**
     * Whether the user belongs to the company: a member, or one of its
     * administrators (whose legacy assignment may predate memberships).
     *
     * This is the boundary for every password set from the company page.
     * Widened from administrators only on 2026-11-05 (user), so that support
     * can set any of the company's users' passwords; it still refuses anyone
     * outside the company, which is what stops one tenant's page from taking
     * over another tenant's user.
     */
    public function isMember(Company $company, User $user): bool
    {
        if ($user->trashed()) {
            return false;
        }

        $isMember = $user->allowedCompanies()
            ->withoutGlobalScopes()
            ->where('companies.id', $company->getKey())
            ->exists();

        return $isMember
            || $this->administrators($company)->contains(fn (User $admin): bool => $admin->is($user));
    }

    protected function resetPassword(
        Company $company,
        User $actor,
        User $target,
        string $password,
        bool $mustChangePassword,
    ): string {
        if (! $this->isMember($company, $target)) {
            return self::NOT_MEMBER;
        }

        // The same checks as editing the user on the Users page. Without the
        // assignment check, a Multi-Company Admin could reset an administrator
        // who also belongs to a company outside their own assignment.
        if (! Gate::forUser($actor)->allows('update', $target)) {
            return self::NOT_ALLOWED;
        }

        try {
            $this->administration->assertUserAssignment(
                $actor,
                $target,
                $this->administration->effectiveRoleIds($target),
                $this->administration->assignedCompanyIds($target),
                $target->default_company_id,
            );
        } catch (ValidationException|AuthorizationException) {
            return self::NOT_ALLOWED;
        }

        // Two saves on purpose. User's updating hook clears the flag whenever
        // the password changes and the flag itself is not dirty, so when the
        // flag is already true a single save would clear it and the person
        // would never be asked to replace a password someone else chose.
        $target->forceFill(['password' => $password])->save();

        if ($mustChangePassword) {
            $target->forceFill(['must_change_password' => true])->save();
        }

        // Never the password itself, hashed or not.
        $this->audit->record(
            'security.user.password_set_by_administrator',
            $target,
            $actor,
            (int) $company->getKey(),
            after: ['must_change_password' => (bool) $target->must_change_password],
        );

        return self::PASSWORD_RESET;
    }

    protected function createAdministrator(
        Company $company,
        User $actor,
        int $roleId,
        string $email,
        string $password,
        bool $mustChangePassword,
    ): string {
        $companyId = (int) $company->getKey();

        if (! Gate::forUser($actor)->allows('create', User::class)) {
            return self::NOT_ALLOWED;
        }

        try {
            $this->administration->assertUserAssignment($actor, null, [$roleId], [$companyId], $companyId);
        } catch (ValidationException|AuthorizationException) {
            return self::NOT_ALLOWED;
        }

        $capacity = app(SeatCapacityService::class);

        try {
            $user = $capacity->withLockedCompany($companyId, function () use ($actor, $companyId, $roleId, $email, $password, $mustChangePassword, $capacity): User {
                // A pending invitation to the same address would keep holding a
                // seat for a person who now has an account, and its link would
                // fail on the unique email. Revoked first so the seat check
                // below counts the account once, not twice.
                Invitation::query()
                    ->whereRaw('LOWER(email) = ?', [mb_strtolower($email)])
                    ->where('default_company_id', $companyId)
                    ->pending()
                    ->get()
                    ->each(fn (Invitation $invitation) => app(UserInvitationService::class)->revoke($invitation, $actor));

                $capacity->assertCanAttachMembership($companyId);

                $user = User::query()->create([
                    'name'                 => Str::before($email, '@'),
                    'email'                => $email,
                    'password'             => $password,
                    'default_company_id'   => $companyId,
                    'creator_id'           => $actor->getKey(),
                    // Company-wide, as for every other administrator: ownership on
                    // User matches creator_id and id, so anything narrower would
                    // show them only themselves.
                    'resource_permission'  => PermissionType::GLOBAL,
                    'must_change_password' => $mustChangePassword,
                ]);

                $user->allowedCompanies()->sync([$companyId]);

                app(TenantRoleAssigner::class)->sync($user, [$roleId], [$companyId]);

                $user->unsetRelation('roles');

                return $user;
            });
        } catch (ValidationException) {
            // Inside the lock this is the seat limit (or the company having
            // been deleted meanwhile); the transaction has rolled back the
            // revoked invitation with it.
            return self::SEATS_EXHAUSTED;
        }

        $this->audit->record(
            'security.user.created',
            $user,
            $actor,
            $companyId,
            after: $this->administration->userSnapshot($user),
        );

        return self::CREATED;
    }
}
