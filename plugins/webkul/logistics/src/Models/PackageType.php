<?php

namespace Webkul\Logistics\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Logistics\Database\Factories\PackageTypeFactory;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;

/**
 * Shared configuration (see ServiceType). Not Inventory's PackageType, which
 * describes warehouse packaging.
 */
class PackageType extends Model
{
    use BelongsToCompany;
    use HasFactory;

    protected $table = 'logistics_package_types';

    protected $fillable = [
        'name',
        'code',
        'is_active',
        'sort',
        'company_id',
        'creator_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort'      => 'integer',
    ];

    public static function autoAssignsCompany(): bool
    {
        return false;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function newFactory(): PackageTypeFactory
    {
        return PackageTypeFactory::new();
    }

    protected static function booted(): void
    {
        static::creating(function (self $record): void {
            $record->creator_id ??= Auth::id();
        });
    }
}
