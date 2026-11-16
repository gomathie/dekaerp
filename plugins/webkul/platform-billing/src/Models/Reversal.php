<?php

namespace Webkul\PlatformBilling\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Security\Models\User;
use Webkul\Support\Traits\BelongsToCompany;
use Webkul\Support\Traits\InheritsParentCompany;

/**
 * A refund of unapplied credit, or the closing row of a full reversal.
 * It references the payment and moves no money to a bank (D-SAAS-10).
 */
class Reversal extends Model
{
    use BelongsToCompany;
    use InheritsParentCompany;

    protected $table = 'platform_billing_reversals';

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
            'effective_at' => 'immutable_datetime',
        ];
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function poster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }
}
