<?php

namespace Webkul\PlatformBilling\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class DiscountCode extends Model
{
    protected $table = 'platform_billing_discount_codes';

    protected $guarded = [];

    public const LOCKED_TERMS_MESSAGE = 'This discount code has been used, so its terms can no longer change. Deactivate it and create a new code.';

    /**
     * The terms a company agreed to when it redeemed the code.
     *
     * @var list<string>
     */
    public const TERM_COLUMNS = [
        'code', 'discount_type', 'discount_value', 'duration_type', 'duration_months',
        'eligibility_type', 'max_redemptions', 'redemption_ends_at',
    ];

    /**
     * A code's terms are locked once any company has redeemed it (user,
     * 2026-11-11). Editing them afterwards would give later redeemers
     * different terms under the same code. Status (is_active) and the
     * redemption counter stay writable.
     */
    protected static function booted(): void
    {
        static::updating(function (self $discount): void {
            if ((int) $discount->getOriginal('redemption_count') > 0 && $discount->isDirty(self::TERM_COLUMNS)) {
                throw ValidationException::withMessages(['code' => self::LOCKED_TERMS_MESSAGE]);
            }
        });
    }

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
