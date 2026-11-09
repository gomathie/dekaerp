<?php

namespace Webkul\PlatformBilling\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;

/**
 * A posted platform invoice. The database refuses updates and deletes, and the
 * amounts are the figures copied at posting, not a live calculation.
 */
class Invoice extends Model
{
    use BelongsToCompany;

    protected $table = 'platform_billing_invoices';

    protected $guarded = [];

    /**
     * The subscription names the company. A session default must not file the
     * invoice under whoever the queue happens to be.
     */
    public static function autoAssignsCompany(): bool
    {
        return false;
    }

    protected function casts(): array
    {
        return [
            'period_start_at' => 'immutable_datetime',
            'period_end_at' => 'immutable_datetime',
            'document_year' => 'integer',
            'sequence' => 'integer',
            'issued_at' => 'immutable_datetime',
            'due_at' => 'immutable_datetime',
            'grace_starts_at' => 'immutable_datetime',
            'tax_enabled' => 'boolean',
            'subtotal_minor' => 'integer',
            'discount_minor' => 'integer',
            'tax_minor' => 'integer',
            'total_minor' => 'integer',
            'discount_value' => 'integer',
            'free_month_applied' => 'boolean',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function redemption(): BelongsTo
    {
        return $this->belongsTo(DiscountRedemption::class, 'discount_redemption_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(InvoiceLine::class);
    }
}
