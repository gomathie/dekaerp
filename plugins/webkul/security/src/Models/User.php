<?php

namespace Webkul\Security\Models;

use App\Models\User as BaseUser;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthentication;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthenticationRecovery;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Filament\Auth\MultiFactor\Email\Concerns\InteractsWithEmailAuthentication;
use Filament\Auth\MultiFactor\Email\Contracts\HasEmailAuthentication;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;
use Webkul\Employee\Models\Department;
use Webkul\Employee\Models\Employee;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Services\MultiCompanyAdminService;
use Webkul\Security\Services\SecurityAuditLogger;
use Webkul\Security\Services\TenantRoleResolver;
use Webkul\Security\Support\OwnerSource;
use Webkul\Security\Traits\HasOwnershipScope;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Scopes\CompanyScope;

class User extends BaseUser implements FilamentUser, HasAppAuthentication, HasAppAuthenticationRecovery, HasEmailAuthentication
{
    use HasOwnershipScope;
    use HasRoles {
        hasPermissionTo as protected hasPermissionToWithoutMultiCompanyRestrictions;
    }
    use InteractsWithAppAuthentication,
        InteractsWithAppAuthenticationRecovery,
        InteractsWithEmailAuthentication,
        SoftDeletes;

    /**
     * Matches the `users.is_active` column default.
     *
     * Without it, a model built in memory (User::factory()->create(),
     * User::create(...)) carries a null is_active while the row it just wrote
     * is true, because a column default is applied by the database and not
     * read back. Everything that reads the attribute then treats the user as
     * suspended until the model is reloaded — hasPermissionTo() denies every
     * ability and canAccessPanel() returns false.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'is_active'            => true,
        'must_change_password' => false,
    ];

    public function __construct(array $attributes = [])
    {
        $this->mergeFillable([
            'partner_id',
            'language',
            'creator_id',
            'is_active',
            'default_company_id',
            'resource_permission',
            'is_default',
            'must_change_password',
        ]);

        $this->mergeCasts([
            'default_company_id'   => 'integer',
            'resource_permission'  => PermissionType::class,
            'is_default'           => 'boolean',
            'is_active'            => 'boolean',
            'must_change_password' => 'boolean',
        ]);

        parent::__construct($attributes);
    }

    protected static function ownershipScopeIsGlobal(): bool
    {
        return false;
    }

    public function ownershipSources(): array
    {
        return [
            OwnerSource::column('creator_id'),
            OwnerSource::column('id'),
        ];
    }

    protected $guard_name = ['web', 'sanctum'];

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active;
    }

    public function isSuperAdmin(): bool
    {
        $roleNames = $this->getRoleNames()
            ->map(fn (string $name): string => mb_strtolower(trim($name)))
            ->all();

        return count(array_intersect($roleNames, Role::getSuperAdminRoleNames())) > 0;
    }

    public function isMultiCompanyAdmin(): bool
    {
        return $this->getRoleNames()
            ->contains(fn (string $name): bool => mb_strtolower(trim($name)) === mb_strtolower(Role::MULTI_COMPANY_ADMIN));
    }

    /**
     * A customer's own administrator for their company.
     *
     * Deliberately only the role itself, with no super-admin or multi-company
     * fallback: callers that mean "this person administers something" compose
     * the three predicates themselves, so neither of the wider roles can be
     * mistaken for a tenant-scoped one. See Role::COMPANY_ADMIN.
     *
     * Since tenant roles live on company memberships, pass the company whose
     * administration is being judged. With no company this answers "somewhere",
     * which is what navigation and the guide picker want - never authorization
     * for a record. The global store is still consulted first because that is
     * where a pre-migration assignment, and any user with no membership row,
     * still lives.
     */
    public function isCompanyAdmin(?int $companyId = null): bool
    {
        $matchesCompanyAdmin = fn (string $name): bool => mb_strtolower(trim($name)) === mb_strtolower(Role::COMPANY_ADMIN);

        if ($this->getRoleNames()->contains($matchesCompanyAdmin)) {
            return true;
        }

        if (! app(TenantRoleResolver::class)->supported()) {
            return false;
        }

        $resolver = app(TenantRoleResolver::class);

        return $companyId === null
            ? $resolver->hasRoleInAnyCompany($this, Role::COMPANY_ADMIN)
            : $resolver->hasRoleInCompany($this, Role::COMPANY_ADMIN, $companyId);
    }

    /**
     * A permission, answered for one company.
     *
     * This is the check a policy or a service must use when a record's company
     * is known. Platform roles and directly assigned permissions are
     * installation-wide and therefore count in every company; tenant roles count
     * only where the membership grants them.
     */
    public function hasPermissionInCompany($permission, int $companyId): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $permissionName = $this->normalizePermissionName($permission);

        if (
            $permissionName !== null
            && $this->isMultiCompanyAdmin()
            && app(MultiCompanyAdminService::class)->deniesAbility($permissionName)
        ) {
            return false;
        }

        if ($this->hasPermissionToWithoutMultiCompanyRestrictions($permission)) {
            return true;
        }

        if ($permissionName === null || ! app(TenantRoleResolver::class)->supported()) {
            return false;
        }

        return app(TenantRoleResolver::class)->holdsAbility($this, $permissionName, $companyId);
    }

    public function hasPermissionTo($permission, ?string $guardName = null): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $permissionName = $this->normalizePermissionName($permission);

        if (
            $permissionName !== null
            && $this->isMultiCompanyAdmin()
            && app(MultiCompanyAdminService::class)->deniesAbility($permissionName)
        ) {
            return false;
        }

        /*
         * Two sources, unioned on purpose. Spatie answers for the global store -
         * platform roles, direct permissions, and any tenant role the migration
         * deliberately left global because the user has no membership row.
         * `company_membership_roles` answers for tenant roles granted per
         * company. Taking the union means this can only ever add to what the
         * previous implementation returned, so the migration cannot lock a user
         * out of something they held a moment before.
         *
         * With no company in scope this is the navigation question, so it
         * considers the companies active in the session, falling back to all
         * memberships outside a web request.
         */
        if ($this->hasPermissionToWithoutMultiCompanyRestrictions($permission, $guardName)) {
            return true;
        }

        if ($permissionName === null || ! app(TenantRoleResolver::class)->supported()) {
            return false;
        }

        return app(TenantRoleResolver::class)->holdsAbility($this, $permissionName);
    }

    /**
     * Every role name that applies to this user, from both stores.
     *
     * The Users list and infolist show roles, and after the migration a tenant
     * role is no longer in `model_has_roles`, so `roles.name` alone would show
     * an empty column for every tenant user.
     *
     * @return array<int, string>
     */
    public function effectiveRoleNames(): array
    {
        $global = $this->getRoleNames()->all();

        if (! app(TenantRoleResolver::class)->supported()) {
            return array_values($global);
        }

        $tenant = app(TenantRoleResolver::class)->roleNamesFor($this);

        return collect(array_merge($global, $tenant))
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    protected function normalizePermissionName($permission): ?string
    {
        return match (true) {
            $permission instanceof \BackedEnum                 => (string) $permission->value,
            is_object($permission) && isset($permission->name) => (string) $permission->name,
            is_string($permission)                             => $permission,
            default                                            => null,
        };
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function getAvatarUrlAttribute()
    {
        return $this->partner?->avatar_url;
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'user_team', 'user_id', 'team_id');
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class, 'user_id');
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class, 'manager_id');
    }

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'partner_id')
            ->withoutGlobalScope(CompanyScope::class);
    }

    public function allowedCompanies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'user_allowed_companies', 'user_id', 'company_id');
    }

    public function defaultCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'default_company_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->creator_id ??= Auth::id();
        });

        static::updating(function (self $user): void {
            // Changing the password satisfies the requirement, so the flag
            // clears itself and nobody has to remember to untick it. Skipped
            // when the flag is also being set in the same save: an
            // administrator who sets a password *and* ticks the box means the
            // person must still replace it.
            if ($user->isDirty('password') && ! $user->isDirty('must_change_password')) {
                $user->must_change_password = false;
            }
        });

        static::saved(function ($user) {
            if (! $user->partner_id) {
                $user->handlePartnerCreation($user);
            } else {
                $user->handlePartnerUpdation($user);
            }
        });

        static::updated(function (self $user): void {
            if (! $user->wasChanged('is_active')) {
                return;
            }

            if (! $user->is_active) {
                $user->tokens()->delete();
            }

            app(SecurityAuditLogger::class)->record(
                $user->is_active ? 'security.user.activated' : 'security.user.deactivated',
                $user,
                companyId: $user->default_company_id,
                before: ['is_active' => (bool) $user->getOriginal('is_active')],
                after: ['is_active' => (bool) $user->is_active],
            );
        });

        static::deleted(function (self $user): void {
            app(SecurityAuditLogger::class)->record(
                $user->isForceDeleting() ? 'security.user.force_deleted' : 'security.user.deleted',
                $user,
                companyId: $user->default_company_id,
            );
        });

        static::restored(function (self $user): void {
            app(SecurityAuditLogger::class)->record(
                'security.user.restored',
                $user,
                companyId: $user->default_company_id,
            );
        });
    }

    private function handlePartnerCreation(self $user)
    {
        $partnerAttributes = Arr::only($user->toArray(), app(Partner::class)->getFillable());

        $partner = $user->partner()->create([
            ...$partnerAttributes,
            'creator_id' => Auth::user()->id ?? $user->id,
            'company_id' => $user->default_company_id,
            'user_id'    => $user->id,
            'sub_type'   => 'partner',
        ]);

        $user->partner_id = $partner->id;
        $user->save();
    }

    private function handlePartnerUpdation(self $user)
    {
        $partnerAttributes = Arr::only($user->toArray(), app(Partner::class)->getFillable());

        $partner = Partner::withoutGlobalScopes()->updateOrCreate(
            ['id' => $user->partner_id],
            [
                ...$partnerAttributes,
                'creator_id' => Auth::user()->id ?? $user->id,
                'company_id' => $user->default_company_id,
                'user_id'    => $user->id,
                'sub_type'   => 'partner',
            ]
        );

        if ($user->partner_id !== $partner->id) {
            $user->partner_id = $partner->id;
            $user->save();
        }
    }
}
