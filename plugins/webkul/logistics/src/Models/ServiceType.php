<?php

namespace Webkul\Logistics\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Webkul\Logistics\Database\Factories\ServiceTypeFactory;
use Webkul\Logistics\Enums\TransportMode;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;

/**
 * Configuration shared by all companies (company_id null) unless a company adds
 * its own. Declared shared in the company-scoping invariant test.
 */
class ServiceType extends Model
{
    use BelongsToCompany;
    use HasFactory, SoftDeletes;

    protected $table = 'logistics_service_types';

    protected $fillable = [
        'name',
        'code',
        'transport_mode',
        'product_reference',
        'is_active',
        'sort',
        'company_id',
        'creator_id',
    ];

    protected $casts = [
        'transport_mode' => TransportMode::class,
        'is_active'      => 'boolean',
        'sort'           => 'integer',
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

    protected static function newFactory(): ServiceTypeFactory
    {
        return ServiceTypeFactory::new();
    }

    protected static function booted(): void
    {
        static::creating(function (self $record): void {
            $record->creator_id ??= Auth::id();
        });
    }
}
