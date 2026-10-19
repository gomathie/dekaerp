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

    /**
     * Mirrors the column defaults in the shipment charges migration. See Trip
     * for why.
     *
     * `is_billable` is the one that would bite: a charge created without it
     * reads back null, which is falsy, so code deciding what to invoice would
     * skip a charge the database considers billable.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'sort'        => 0,
        'quantity'    => 1,
        'price_unit'  => 0,
        'discount'    => 0,
        'subtotal'    => 0,
        'total'       => 0,
        'is_billable' => true,
    ];

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

        // Nothing computed this before, so the column sat at its default of 0
        // for every charge ever created - while three places read it as money:
        // the Unbilled charges page, the unbilled revenue widget on the
        // dashboard, and the shipment margin. All three showed zero, always.
        //
        // Pre-tax, like sales_order_lines.price_subtotal, and discount is a
        // percentage, as it is on the charge form and on the order lines
        // ShipmentFromOrder copies from. Tax belongs to accounting and is
        // computed when the charge becomes an invoice line, so `total` is
        // deliberately left alone rather than guessed at here.
        static::saving(function (self $charge): void {
            $quantity = (float) ($charge->quantity ?? 0);
            $priceUnit = (float) ($charge->price_unit ?? 0);
            $discount = (float) ($charge->discount ?? 0);

            $charge->subtotal = round($quantity * $priceUnit * (1 - $discount / 100), 4);
        });
    }
}
