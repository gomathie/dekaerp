<?php

namespace Webkul\PlatformBilling\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Security\Models\User;
use Webkul\Support\Traits\BelongsToCompany;
use Webkul\Support\Traits\InheritsParentCompany;

/** A signed amount of a payment applied to one invoice. The company is the payment's. */
class Allocation extends Model
{
    use BelongsToCompany;
    use InheritsParentCompany;

    protected $table = 'platform_billing_allocations';

    protected $guarded = [];

    protected static string $parentCompanyModel = Payment::class;

    protected static string $parentCompanyKey = 'payment_id';

    public static function autoAssignsCompany(): bool
    {
        return false;
    }

    protected function casts(): array
    {
        return [
            'amount_minor' => 'integer',
            'posted_at' => 'immutable_datetime',
        ];
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function poster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }
}
