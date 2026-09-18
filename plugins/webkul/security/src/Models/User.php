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
        ]);

        $this->mergeCasts([
            'default_company_id'  => 'integer',
            'resource_permission' => PermissionType::class,
            'is_default'          => 'boolean',
            'is_active'           => 'boolean',
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

    public function hasPermissionTo($permission, ?string $guardName = null): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $permissionName = match (true) {
            $permission instanceof \BackedEnum => (string) $permission->value,
            is_object($permission) && isset($permission->name) => (string) $permission->name,
            is_string($permission) => $permission,
            default => null,
        };

        if (
            $permissionName !== null
            && $this->isMultiCompanyAdmin()
            && app(MultiCompanyAdminService::class)->deniesAbility($permissionName)
        ) {
            return false;
        }

        return $this->hasPermissionToWithoutMultiCompanyRestrictions($permission, $guardName);
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
