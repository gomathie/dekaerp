<?php

namespace Webkul\Logistics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;
use Webkul\Logistics\Enums\ShipmentEventSource;
use Webkul\Logistics\Enums\ShipmentEventType;
use Webkul\Logistics\Models\Concerns\InheritsParentCompany;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;

/**
 * One entry of a shipment's tracking timeline. Append-only: rows are never
 * updated. Telematics adapters create these with source = telematics.
 */
class ShipmentEvent extends Model
{
    use BelongsToCompany;
    use InheritsParentCompany;

    protected static string $parentCompanyModel = Shipment::class;

    protected static string $parentCompanyKey = 'shipment_id';

    protected $table = 'logistics_shipment_events';

    protected $fillable = [
        'type',
        'source',
        'occurred_at',
        'location_label',
        'latitude',
        'longitude',
        'notes',
        'metadata',
        'shipment_id',
        'trip_id',
        'stop_id',
        'user_id',
        'company_id',
    ];

    protected $casts = [
        'type'        => ShipmentEventType::class,
        'source'      => ShipmentEventSource::class,
        'occurred_at' => 'datetime',
        'latitude'    => 'decimal:7',
        'longitude'   => 'decimal:7',
        'metadata'    => 'array',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function stop(): BelongsTo
    {
        return $this->belongsTo(Stop::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    protected static function booted(): void
    {
        static::creating(function (self $event): void {
            $event->occurred_at ??= now();
            $event->source ??= ShipmentEventSource::USER;
        });

        static::updating(function (): void {
            throw new LogicException('Shipment events are append-only.');
        });
    }
}
