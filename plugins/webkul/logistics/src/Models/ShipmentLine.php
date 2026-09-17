<?php

namespace Webkul\Logistics\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Logistics\Database\Factories\ShipmentLineFactory;
use Webkul\Logistics\Models\Concerns\InheritsParentCompany;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;

/**
 * A cargo line of a shipment.
 */
class ShipmentLine extends Model
{
    use BelongsToCompany;
    use HasFactory, InheritsParentCompany;

    protected static string $parentCompanyModel = Shipment::class;

    protected static string $parentCompanyKey = 'shipment_id';

    protected $table = 'logistics_shipment_lines';

    protected $fillable = [
        'sort',
        'description',
        'quantity',
        'weight_kg',
        'length_cm',
        'width_cm',
        'height_cm',
        'volume_m3',
        'declared_value',
        'handling_instructions',
        'shipment_id',
        'package_type_id',
        'company_id',
    ];

    protected $casts = [
        'sort'           => 'integer',
        'quantity'       => 'decimal:3',
        'weight_kg'      => 'decimal:3',
        'length_cm'      => 'decimal:2',
        'width_cm'       => 'decimal:2',
        'height_cm'      => 'decimal:2',
        'volume_m3'      => 'decimal:3',
        'declared_value' => 'decimal:4',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function packageType(): BelongsTo
    {
        return $this->belongsTo(PackageType::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    protected static function newFactory(): ShipmentLineFactory
    {
        return ShipmentLineFactory::new();
    }
}
