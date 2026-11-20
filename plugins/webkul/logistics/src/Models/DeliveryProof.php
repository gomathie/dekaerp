<?php

namespace Webkul\Logistics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Logistics\Enums\ProofCaptureChannel;
use Webkul\Logistics\Models\Concerns\InheritsParentCompany;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;

/**
 * Proof of delivery, per shipment and optionally per delivery stop (D8).
 * Location is supporting evidence only.
 */
class DeliveryProof extends Model
{
    use BelongsToCompany;
    use InheritsParentCompany;

    protected static string $parentCompanyModel = Shipment::class;

    protected static string $parentCompanyKey = 'shipment_id';

    protected $table = 'logistics_delivery_proofs';

    protected $fillable = [
        'recipient_name',
        'received_at',
        'reference',
        'notes',
        'photo_path',
        'signature_path',
        'captured_via',
        'latitude',
        'longitude',
        'accuracy_m',
        'shipment_id',
        'stop_id',
        'captured_by_id',
        'company_id',
    ];

    protected $casts = [
        'received_at'  => 'datetime',
        'captured_via' => ProofCaptureChannel::class,
        'latitude'     => 'decimal:7',
        'longitude'    => 'decimal:7',
        'accuracy_m'   => 'decimal:2',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function stop(): BelongsTo
    {
        return $this->belongsTo(Stop::class);
    }

    public function capturedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'captured_by_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
