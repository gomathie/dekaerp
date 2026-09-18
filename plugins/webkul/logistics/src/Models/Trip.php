<?php

namespace Webkul\Logistics\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Logistics\Database\Factories\TripFactory;
use Webkul\Logistics\Enums\TripState;
use Webkul\Logistics\Support\LogisticsSequences;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;

/**
 * An operational vehicle and driver movement. One trip can carry several
 * shipments. State changes go through DispatchService (WP-4).
 */
class Trip extends Model
{
    use BelongsToCompany;
    use HasChatter, HasFactory, HasLogActivity, SoftDeletes;

    protected $table = 'logistics_trips';

    protected $fillable = [
        'name',
        'state',
        'planned_start_at',
        'actual_start_at',
        'planned_end_at',
        'actual_end_at',
        'odometer_start',
        'odometer_end',
        'notes',
        'vehicle_id',
        'driver_id',
        'dispatcher_id',
        'company_id',
        'creator_id',
    ];

    protected $casts = [
        'state'            => TripState::class,
        'planned_start_at' => 'datetime',
        'actual_start_at'  => 'datetime',
        'planned_end_at'   => 'datetime',
        'actual_end_at'    => 'datetime',
        'odometer_start'   => 'decimal:1',
        'odometer_end'     => 'decimal:1',
    ];

    public string $recordTitleAttribute = 'name';

    public function getModelTitle(): string
    {
        return __('logistics::models/trip.title');
    }

    protected function getLogAttributeLabels(): array
    {
        return [
            'state'                   => __('logistics::models/trip.log-attributes.state'),
            'vehicle.registration_no' => __('logistics::models/trip.log-attributes.vehicle'),
            'driver.name'             => __('logistics::models/trip.log-attributes.driver'),
            'planned_start_at'        => __('logistics::models/trip.log-attributes.planned-start-at'),
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class)->withTrashed();
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class)->withTrashed();
    }

    public function dispatcher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dispatcher_id');
    }

    public function shipments(): BelongsToMany
    {
        return $this->belongsToMany(Shipment::class, 'logistics_trip_shipments', 'trip_id', 'shipment_id')
            ->withPivot('leg_sequence');
    }

    public function stops(): HasMany
    {
        return $this->hasMany(Stop::class)->orderBy('planned_arrival_at');
    }

    public function events(): HasMany
    {
        return $this->hasMany(ShipmentEvent::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function newFactory(): TripFactory
    {
        return TripFactory::new();
    }

    protected static function booted(): void
    {
        static::creating(function (self $trip): void {
            $trip->creator_id ??= Auth::id();

            if (blank($trip->name) && $trip->company_id) {
                $trip->name = LogisticsSequences::next(LogisticsSequences::TRIP, (int) $trip->company_id);
            }
        });
    }
}
