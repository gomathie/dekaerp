<?php

namespace Webkul\PlatformBilling\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Support\Models\Company;

/**
 * Sold terms are copied from the plan and price at creation. Catalog edits do
 * not alter existing subscriptions; a later explicit change must audit them.
 */
class Subscription extends Model
{
    protected $table = 'platform_billing_subscriptions';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'included_plugins' => 'array',
            'interval_months' => 'integer',
            'amount_minor' => 'integer',
            'anchor_at' => 'immutable_datetime',
            'period_start_at' => 'immutable_datetime',
            'period_end_at' => 'immutable_datetime',
            'next_invoice_at' => 'immutable_datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function price(): BelongsTo
    {
        return $this->belongsTo(Price::class);
    }
}
