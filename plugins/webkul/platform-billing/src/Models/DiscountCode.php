<?php

namespace Webkul\PlatformBilling\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class DiscountCode extends Model
{
    protected $table = 'platform_billing_discount_codes';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'discount_value'     => 'integer',
            'duration_months'    => 'integer',
            'max_redemptions'    => 'integer',
            'redemption_count'   => 'integer',
            'redemption_ends_at' => 'immutable_datetime',
            'is_active'          => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function eligibleCompanies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'platform_billing_discount_code_companies');
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(DiscountRedemption::class, 'discount_code_id');
    }
}
