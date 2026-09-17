<?php

namespace Webkul\Logistics\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Webkul\Employee\Models\Employee;
use Webkul\Logistics\Database\Factories\DriverFactory;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;

/**
 * A driver profile over an employee or, for contractors, a contact (D6).
 */
class Driver extends Model
{
    use BelongsToCompany;
    use HasFactory, SoftDeletes;

    protected $table = 'logistics_drivers';

    protected $fillable = [
        'name',
        'phone',
        'license_number',
        'license_class',
        'license_expires_at',
        'is_active',
        'employee_id',
        'partner_id',
        'company_id',
        'creator_id',
    ];

    protected $casts = [
        'license_expires_at' => 'date',
        'is_active'          => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function isLicenseExpired(?Carbon $on = null): bool
    {
        return $this->license_expires_at !== null
            && $this->license_expires_at->lt(($on ?? now())->startOfDay());
    }

    public function licenseExpiresWithin(int $days, ?Carbon $from = null): bool
    {
        if ($this->license_expires_at === null || $this->isLicenseExpired($from)) {
            return false;
        }

        return $this->license_expires_at->lte(($from ?? now())->startOfDay()->addDays($days));
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function newFactory(): DriverFactory
    {
        return DriverFactory::new();
    }

    protected static function booted(): void
    {
        static::creating(function (self $driver): void {
            $driver->creator_id ??= Auth::id();
        });
    }
}
