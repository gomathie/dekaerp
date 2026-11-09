<?php

namespace Webkul\PlatformBilling\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;

/** Immutable terms accepted by a company; later code edits cannot rewrite them. */
class DiscountRedemption extends Model
{
    use BelongsToCompany;

    protected $table = 'platform_billing_discount_redemptions';

    protected $guarded = [];

    public static function autoAssignsCompany(): bool
    {
        return false;
    }

    protected function casts(): array
    {
        return [
            'discount_value' => 'integer',
            'duration_months' => 'integer',
            'redeemed_at' => 'immutable_datetime',
            'duration_ends_at' => 'immutable_datetime',
        ];
    }

    public function code(): BelongsTo
    {
        return $this->belongsTo(DiscountCode::class, 'discount_code_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function redeemer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'redeemed_by');
    }
}
