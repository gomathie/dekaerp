<?php

namespace Webkul\Logistics\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Models\Tax;
use Webkul\Logistics\Database\Factories\ShipmentChargeFactory;
use Webkul\Logistics\Models\Concerns\InheritsParentCompany;
use Webkul\Product\Models\Product;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\UOM;
use Webkul\Support\Traits\BelongsToCompany;

/**
 * A customer-facing amount on a shipment; becomes an invoice line (WP-7).
 */
class ShipmentCharge extends Model
{
    use BelongsToCompany;
    use HasFactory, InheritsParentCompany;

    protected static string $parentCompanyModel = Shipment::class;

    protected static string $parentCompanyKey = 'shipment_id';

    protected $table = 'logistics_shipment_charges';

    protected $fillable = [
        'sort',
        'description',
        'quantity',
        'price_unit',
        'discount',
        'subtotal',
        'total',
        'is_billable',
        'shipment_id',
        'product_id',
        'uom_id',
        'currency_id',
        'move_line_id',
        'company_id',
        'creator_id',
    ];

    protected $casts = [
        'sort'        => 'integer',
        'quantity'    => 'decimal:4',
        'price_unit'  => 'decimal:4',
        'discount'    => 'decimal:4',
        'subtotal'    => 'decimal:4',
        'total'       => 'decimal:4',
        'is_billable' => 'boolean',
    ];

    public function scopeUninvoiced(Builder $query): Builder
    {
        return $query->where('is_billable', true)->whereNull('move_line_id');
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function uom(): BelongsTo
    {
        return $this->belongsTo(UOM::class, 'uom_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function moveLine(): BelongsTo
    {
        return $this->belongsTo(MoveLine::class, 'move_line_id');
    }

    public function taxes(): BelongsToMany
    {
        return $this->belongsToMany(Tax::class, 'logistics_shipment_charge_taxes', 'charge_id', 'tax_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function newFactory(): ShipmentChargeFactory
    {
        return ShipmentChargeFactory::new();
    }

    protected static function booted(): void
    {
        static::creating(function (self $charge): void {
            $charge->creator_id ??= Auth::id();
        });
    }
}
