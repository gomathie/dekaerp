<?php

namespace Webkul\Logistics\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Logistics\Database\Factories\StopFactory;
use Webkul\Logistics\Enums\StopState;
use Webkul\Logistics\Enums\StopType;
use Webkul\Logistics\Models\Concerns\InheritsParentCompany;
use Webkul\Partner\Models\Partner;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;

/**
 * A pickup, delivery or intermediate stop of a shipment, optionally scheduled on
 * a trip.
 */
class Stop extends Model
{
    use BelongsToCompany;
    use HasFactory, InheritsParentCompany;

    protected static string $parentCompanyModel = Shipment::class;

    protected static string $parentCompanyKey = 'shipment_id';

    protected $table = 'logistics_stops';

    protected $fillable = [
        'sequence',
        'type',
        'state',
        'contact_name',
        'contact_phone',
        'instructions',
        'planned_arrival_at',
        'actual_arrival_at',
        'planned_departure_at',
        'actual_departure_at',
        'latitude',
        'longitude',
        'shipment_id',
        'trip_id',
        'address_id',
        'company_id',
    ];

    protected $casts = [
        'sequence'             => 'integer',
        'type'                 => StopType::class,
        'state'                => StopState::class,
        'planned_arrival_at'   => 'datetime',
        'actual_arrival_at'    => 'datetime',
        'planned_departure_at' => 'datetime',
        'actual_departure_at'  => 'datetime',
        'latitude'             => 'decimal:7',
        'longitude'            => 'decimal:7',
    ];

    /**
     * Minutes spent at the stop, when both actual times are known.
     */
    public function dwellMinutes(): ?int
    {
        if (! $this->actual_arrival_at || ! $this->actual_departure_at) {
            return null;
        }

        return (int) $this->actual_arrival_at->diffInMinutes($this->actual_departure_at);
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'address_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(ShipmentEvent::class);
    }

    public function deliveryProofs(): HasMany
    {
        return $this->hasMany(DeliveryProof::class);
    }

    public function links(): HasMany
    {
        return $this->hasMany(StopLink::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    protected static function newFactory(): StopFactory
    {
        return StopFactory::new();
    }
}
