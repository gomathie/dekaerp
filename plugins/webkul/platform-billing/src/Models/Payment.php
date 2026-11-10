<?php

namespace Webkul\PlatformBilling\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;

/**
 * One posted money-in row (D-SAAS-10). The unallocated part is derived from
 * allocations and reversals; this row does not store a running balance.
 */
class Payment extends Model
{
    use BelongsToCompany;

    protected $table = 'platform_billing_payments';

    protected $guarded = [];

    /**
     * The caller names the company. A session default must not file the
     * payment under whoever is signed in.
     */
    public static function autoAssignsCompany(): bool
    {
        return false;
    }

    protected function casts(): array
    {
        return [
            'amount_minor' => 'integer',
            'received_at'  => 'immutable_datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function poster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(Allocation::class);
    }

    public function reversals(): HasMany
    {
        return $this->hasMany(Reversal::class);
    }
}
