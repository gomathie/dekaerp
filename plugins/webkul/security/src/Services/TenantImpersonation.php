<?php

namespace Webkul\Security\Services;

use Carbon\CarbonImmutable;
use DateTimeImmutable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\PermissionRegistrar as SpatiePermissionRegistrar;
use Webkul\Security\Models\Permission;
use Webkul\Security\Models\User;
use Webkul\Security\PermissionRegistrar;
use Webkul\Support\Models\Company;
use Webkul\Support\Services\CompanyContext;

/**
 * Tenant impersonation (SAAS-15, user decisions 2026-11-10).
 *
 * The rules, and the only place they live:
 *  - A Super Admin may impersonate. A Multi-Company Admin may impersonate only
 *    with an explicit `tenant.impersonate` grant and only in an assigned
 *    company. Nobody else may, whatever permission they hold.
 *  - The target is an active tenant member of that company. Never a Super
 *    Admin, a Multi-Company Admin or the administrator themselves, so
 *    impersonation can never be used to gain installation-staff powers.
 *  - Starting requires the administrator's own password and a reason, every
 *    time. The session ends by itself after SESSION_MINUTES.
 *  - While it lasts the request runs as the target: their roles, their
 *    company, the read-only guard. Nothing extra is blocked (user decision);
 *    every write is logged with the administrator's id instead.
 */
class TenantImpersonation
{
    public const PERMISSION = 'tenant.impersonate';

    public const SESSION_KEY = 'security.impersonation';

    public const SESSION_MINUTES = 60;

    public const NOT_ALLOWED = 'You are not allowed to impersonate users in this company.';

    public const NOT_ELIGIBLE = 'This user cannot be impersonated. Only active tenant users of this company can be.';

    public const ALREADY_ACTIVE = 'End the current impersonation before starting another.';

    public const WRONG_PASSWORD = 'The password is incorrect.';

    public const REASON_REQUIRED = 'Give a reason for the impersonation.';

    public const ONLY_SUPER_ADMIN_GRANTS = 'Only a Super Admin may grant or revoke impersonation access.';

    public const ONLY_MULTI_COMPANY_ADMIN = 'Impersonation access can only be granted to a Multi-Company Admin.';

    public function __construct(protected SecurityAuditLogger $audit) {}

    /**
     * The permission check reads the permission store directly. Both the
     * Multi-Company Admin Gate::before and User::hasPermissionTo() deny any
     * ability containing "impersonate" for that role, which is what stops
     * every Multi-Company Admin receiving it automatically. An explicit grant
     * still has to be readable, so this goes underneath both.
     */
    public function mayImpersonate(User $actor, User $target, int $companyId): bool
    {
        return ! $this->isActive()
            && $this->actorMayImpersonateIn($actor, $companyId)
            && $this->isEligibleTarget($actor, $target, $companyId);
    }

    public function actorMayImpersonateIn(User $actor, int $companyId): bool
    {
        if (! $actor->is_active) {
            return false;
        }

        if ($actor->isSuperAdmin()) {
            return true;
        }

        return $actor->isMultiCompanyAdmin()
            && $this->holdsGrant($actor)
            && app(MultiCompanyAdminService::class)->isCompanyAssigned($actor, $companyId);
    }

    public function isEligibleTarget(User $actor, User $target, int $companyId): bool
    {
        if (! $target->exists || $target->trashed() || ! $target->is_active) {
            return false;
        }

        if ((int) $target->getKey() === (int) $actor->getKey()) {
            return false;
        }

        if ($target->isSuperAdmin() || $target->isMultiCompanyAdmin()) {
            return false;
        }

        // Scope-free: the company scope follows the administrator's own
        // selection, so a scoped read would call a real member a stranger
        // whenever support is looking at a different company.
        return $target->allowedCompanies()
            ->withoutGlobalScopes()
            ->where('companies.id', $companyId)
            ->exists();
    }

    public function holdsGrant(User $user): bool
    {
        return $user->getAllPermissions()->contains('name', self::PERMISSION);
    }

    public function start(User $actor, User $target, Company $company, string $password, string $reason): void
    {
        $companyId = (int) $company->getKey();

        if ($this->isActive()) {
            throw new AuthorizationException(self::ALREADY_ACTIVE);
        }

        if (! $this->actorMayImpersonateIn($actor, $companyId)) {
            $this->audit->denied('security.impersonation_started', $actor, ['target_id' => $target->getKey(), 'company_id' => $companyId]);

            throw new AuthorizationException(self::NOT_ALLOWED);
        }

        if (! $this->isEligibleTarget($actor, $target, $companyId)) {
            throw new AuthorizationException(self::NOT_ELIGIBLE);
        }

        // Re-authentication: an unlocked staff session left on a desk must not
        // be enough to become a client's user.
        if (! Hash::check($password, (string) $actor->getAuthPassword())) {
            $this->audit->denied('security.impersonation_started', $actor, ['reason' => 'wrong_password', 'target_id' => $target->getKey()]);

            throw ValidationException::withMessages(['password' => self::WRONG_PASSWORD]);
        }

        $reason = trim($reason);

        if ($reason === '') {
            throw ValidationException::withMessages(['reason' => self::REASON_REQUIRED]);
        }

        $startedAt = CarbonImmutable::now('UTC');
        $state = [
            'impersonator_id'  => (int) $actor->getKey(),
            'target_id'        => (int) $target->getKey(),
            'company_id'       => $companyId,
            'reason'           => $reason,
            'started_at'       => $startedAt->format(DATE_ATOM),
            'expires_at'       => $startedAt->modify('+'.self::SESSION_MINUTES.' minutes')->format(DATE_ATOM),
            'restore_context'  => [
                CompanyContext::SESSION_KEY            => session(CompanyContext::SESSION_KEY),
                CompanyContext::SCOPE_MODE_SESSION_KEY => session(CompanyContext::SCOPE_MODE_SESSION_KEY),
            ],
        ];

        $this->audit->record(
            'security.impersonation_started',
            $target,
            $actor,
            $companyId,
            after: array_diff_key($state, ['restore_context' => true]),
        );

        $this->switchTo($target);
        session()->put(self::SESSION_KEY, $state);
        session()->forget(CompanyContext::SCOPE_MODE_SESSION_KEY);
        session()->put(CompanyContext::SESSION_KEY, [$companyId]);
        app()->forgetInstance(CompanyContext::class);
    }

    /**
     * Returns the administrator, signed back in, or null when nothing was
     * active. $why is recorded: ended, expired, invalid or logout.
     */
    public function end(string $why = 'ended'): ?User
    {
        $state = $this->state();

        if ($state === null) {
            return null;
        }

        session()->forget(self::SESSION_KEY);

        $impersonator = User::withoutGlobalScopes()->find($state['impersonator_id']);
        $target = User::withoutGlobalScopes()->withTrashed()->find($state['target_id']);

        if ($target instanceof User) {
            $this->audit->record(
                'security.impersonation_ended',
                $target,
                $impersonator instanceof User ? $impersonator : null,
                (int) $state['company_id'],
                after: [
                    'impersonator_id' => $state['impersonator_id'],
                    'target_id'       => $state['target_id'],
                    'company_id'      => $state['company_id'],
                    'started_at'      => $state['started_at'],
                    'ended_at'        => CarbonImmutable::now('UTC')->format(DATE_ATOM),
                    'end_reason'      => $why,
                ],
            );
        }

        if ($why === 'logout') {
            return null;
        }

        // A deactivated administrator is not signed back in: their own
        // session would have been refused, so this must not restore it.
        if (! $impersonator instanceof User || ! $impersonator->is_active) {
            Auth::guard('web')->logout();
            session()->invalidate();
            session()->regenerateToken();

            return null;
        }

        $this->switchTo($impersonator);

        foreach ((array) ($state['restore_context'] ?? []) as $key => $value) {
            $value === null ? session()->forget($key) : session()->put($key, $value);
        }

        app()->forgetInstance(CompanyContext::class);

        return $impersonator;
    }

    /**
     * @return array{impersonator_id: int, target_id: int, company_id: int, reason: string, started_at: string, expires_at: string, restore_context?: array<string, mixed>}|null
     */
    public function state(): ?array
    {
        // Console commands and queued jobs have no session to impersonate in.
        if (! app()->bound('session')) {
            return null;
        }

        $state = session(self::SESSION_KEY);

        return is_array($state) && isset($state['impersonator_id'], $state['target_id']) ? $state : null;
    }

    public function isActive(): bool
    {
        return $this->state() !== null;
    }

    public function impersonator(): ?User
    {
        $state = $this->state();

        return $state === null ? null : User::withoutGlobalScopes()->find($state['impersonator_id']);
    }

    /**
     * Why the current session must end now, or null when it may continue.
     * Checked on every panel request (EnforceImpersonationSession).
     */
    public function invalidReason(): ?string
    {
        $state = $this->state();

        if ($state === null) {
            return null;
        }

        if (CarbonImmutable::now('UTC') >= new DateTimeImmutable($state['expires_at'])) {
            return 'expired';
        }

        // The signed-in user must still be the target. Anything else means
        // the session was changed underneath the impersonation.
        if ((int) Auth::guard('web')->id() !== (int) $state['target_id']) {
            return 'invalid';
        }

        $impersonator = User::withoutGlobalScopes()->find($state['impersonator_id']);
        $target = Auth::guard('web')->user();

        // Re-checked per request: revoking the grant, deactivating either
        // account, or removing the target's membership ends the session at
        // once rather than when it times out.
        if (! $impersonator instanceof User || ! $target instanceof User) {
            return 'invalid';
        }

        $allowed = $this->actorMayImpersonateIn($impersonator, (int) $state['company_id'])
            && $this->isEligibleTarget($impersonator, $target, (int) $state['company_id']);

        return $allowed ? null : 'invalid';
    }

    public function grant(User $actor, User $user): void
    {
        $this->assertMayManageGrant($actor, $user);

        $permission = Permission::query()->firstOrCreate(['name' => self::PERMISSION, 'guard_name' => 'web']);
        $user->givePermissionTo($permission);
        $this->forgetPermissionCaches($user);

        $this->audit->record('security.impersonation_granted', $user, $actor, $user->default_company_id, after: ['permission' => self::PERMISSION]);
    }

    public function revoke(User $actor, User $user): void
    {
        $this->assertMayManageGrant($actor, $user, requireMultiCompanyAdmin: false);

        if ($user->permissions()->where('name', self::PERMISSION)->exists()) {
            $user->revokePermissionTo(self::PERMISSION);
        }

        $this->forgetPermissionCaches($user);

        $this->audit->record('security.impersonation_revoked', $user, $actor, $user->default_company_id, before: ['permission' => self::PERMISSION]);
    }

    protected function assertMayManageGrant(User $actor, User $user, bool $requireMultiCompanyAdmin = true): void
    {
        if (! $actor->is_active || ! $actor->isSuperAdmin() || $this->isActive()) {
            throw new AuthorizationException(self::ONLY_SUPER_ADMIN_GRANTS);
        }

        // Super Admins impersonate without a grant, and anyone else may not
        // impersonate at all, so a grant on them would only look meaningful.
        if ($requireMultiCompanyAdmin && ! $user->isMultiCompanyAdmin()) {
            throw ValidationException::withMessages(['user' => self::ONLY_MULTI_COMPANY_ADMIN]);
        }
    }

    /**
     * Both registrars: they cache separately, and a grant invisible to one of
     * them silently reads as not granted (see AGENTS.md).
     */
    protected function forgetPermissionCaches(User $user): void
    {
        app(SpatiePermissionRegistrar::class)->forgetCachedPermissions();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $user->unsetRelation('permissions');
        $user->unsetRelation('roles');
    }

    /**
     * A new session id on every switch, so an id captured before cannot ride
     * across, and the password hash AuthenticateSession compares is the new
     * user's, or the very next request would sign everyone out.
     */
    protected function switchTo(User $user): void
    {
        Auth::guard('web')->login($user);
        session()->migrate(true);
        // AuthenticateSession stores this for whoever is signed in when it is
        // missing. Left as the previous user's, it logs the session out.
        session()->forget('password_hash_web');
    }
}
