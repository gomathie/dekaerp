<?php

namespace Webkul\Logistics\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Models\Move;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Logistics\Database\Factories\ShipmentFactory;
use Webkul\Logistics\Enums\ShipmentPriority;
use Webkul\Logistics\Enums\ShipmentState;
use Webkul\Logistics\Enums\TransportMode;
use Webkul\Logistics\Support\LogisticsSequences;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Traits\BelongsToCompany;

/**
 * The customer's logistics job. State changes go through ShipmentWorkflow (WP-2),
 * never by assigning `state` directly.
 */
class Shipment extends Model
{
    use BelongsToCompany;
    use HasChatter, HasCustomFields, HasFactory, HasLogActivity, SoftDeletes;

    protected $table = 'logistics_shipments';

    protected $fillable = [
        'name',
        'customer_reference',
        'transport_mode',
        'priority',
        'state',
        'origin_label',
        'destination_label',
        'planned_pickup_at',
        'actual_pickup_at',
        'expected_delivery_at',
        'actual_delivery_at',
        'declared_value',
        'is_fragile',
        'is_hazardous',
        'instructions',
        'carrier_reference',
        'waybill_no',
        'is_opening',
        'total_packages',
        'total_weight_kg',
        'total_volume_m3',
        'total_charges',
        'total_costs',
        'sale_order_id',
        'customer_id',
        'pickup_address_id',
        'delivery_address_id',
        'carrier_id',
        'service_type_id',
        'currency_id',
        'dispatcher_id',
        'company_id',
        'creator_id',
    ];

    protected $casts = [
        'transport_mode'       => TransportMode::class,
        'priority'             => ShipmentPriority::class,
        'state'                => ShipmentState::class,
        'planned_pickup_at'    => 'datetime',
        'actual_pickup_at'     => 'datetime',
        'expected_delivery_at' => 'datetime',
        'actual_delivery_at'   => 'datetime',
        'declared_value'       => 'decimal:4',
        'is_fragile'           => 'boolean',
        'is_hazardous'         => 'boolean',
        'is_opening'           => 'boolean',
        'total_packages'       => 'integer',
        'total_weight_kg'      => 'decimal:3',
        'total_volume_m3'      => 'decimal:3',
        'total_charges'        => 'decimal:4',
        'total_costs'          => 'decimal:4',
    ];

    public string $recordTitleAttribute = 'name';

    public function getModelTitle(): string
    {
        return __('logistics::models/shipment.title');
    }

    protected function getLogAttributeLabels(): array
    {
        return [
            'state'                => __('logistics::models/shipment.log-attributes.state'),
            'customer.name'        => __('logistics::models/shipment.log-attributes.customer'),
            'customer_reference'   => __('logistics::models/shipment.log-attributes.customer-reference'),
            'planned_pickup_at'    => __('logistics::models/shipment.log-attributes.planned-pickup-at'),
            'expected_delivery_at' => __('logistics::models/shipment.log-attributes.expected-delivery-at'),
            'dispatcher.name'      => __('logistics::models/shipment.log-attributes.dispatcher'),
            'carrier.name'         => __('logistics::models/shipment.log-attributes.carrier'),
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'customer_id');
    }

    public function pickupAddress(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'pickup_address_id');
    }

    public function deliveryAddress(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'delivery_address_id');
    }

    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'carrier_id');
    }

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function dispatcher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dispatcher_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(ShipmentLine::class)->orderBy('sort');
    }

    public function stops(): HasMany
    {
        return $this->hasMany(Stop::class)->orderBy('sequence');
    }

    public function events(): HasMany
    {
        return $this->hasMany(ShipmentEvent::class)->orderBy('occurred_at');
    }

    public function deliveryProofs(): HasMany
    {
        return $this->hasMany(DeliveryProof::class);
    }

    public function charges(): HasMany
    {
        return $this->hasMany(ShipmentCharge::class)->orderBy('sort');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function trips(): BelongsToMany
    {
        return $this->belongsToMany(Trip::class, 'logistics_trip_shipments', 'shipment_id', 'trip_id')
            ->withPivot('leg_sequence');
    }

    public function invoices(): BelongsToMany
    {
        return $this->belongsToMany(Move::class, 'logistics_shipment_invoices', 'shipment_id', 'move_id');
    }

    protected static function newFactory(): ShipmentFactory
    {
        return ShipmentFactory::new();
    }

    protected static function booted(): void
    {
        // Runs after BelongsToCompany has set company_id (traits boot first).
        static::creating(function (self $shipment): void {
            $shipment->creator_id ??= Auth::id();

            if (blank($shipment->name) && $shipment->company_id) {
                $shipment->name = LogisticsSequences::next(LogisticsSequences::SHIPMENT, (int) $shipment->company_id);
            }
        });
    }
}
