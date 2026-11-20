<?php

namespace Webkul\Logistics\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Logistics\Database\Factories\VehicleFactory;
use Webkul\Logistics\Enums\VehicleOwnership;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;

class Vehicle extends Model
{
    use BelongsToCompany;
    use HasCustomFields, HasFactory, SoftDeletes;

    protected $table = 'logistics_vehicles';

    protected $fillable = [
        'registration_no',
        'name',
        'ownership',
        'capacity_kg',
        'capacity_m3',
        'equipment_id',
        'telematics_device_ref',
        'is_active',
        'vehicle_type_id',
        'carrier_id',
        'default_driver_id',
        'company_id',
        'creator_id',
    ];

    protected $casts = [
        'ownership'   => VehicleOwnership::class,
        'capacity_kg' => 'decimal:3',
        'capacity_m3' => 'decimal:3',
        'is_active'   => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'carrier_id');
    }

    public function defaultDriver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'default_driver_id');
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

    protected static function newFactory(): VehicleFactory
    {
        return VehicleFactory::new();
    }

    protected static function booted(): void
    {
        static::creating(function (self $vehicle): void {
            $vehicle->creator_id ??= Auth::id();
        });
    }
}
