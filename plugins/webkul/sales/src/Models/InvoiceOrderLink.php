<?php

namespace Webkul\Sale\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Account\Models\Move;

/**
 * A customer invoice that asked for a linked sales order, and that order.
 *
 * Not company-scoped on purpose: it is only ever read by invoice id from the
 * invoice's own listener or page, which already decided access, and a scoped
 * read in a listener running as another company's user would silently miss
 * the row (see the project rule on listeners and scoped reads).
 */
class InvoiceOrderLink extends Model
{
    protected $table = 'sales_invoice_order_links';

    protected $fillable = [
        'move_id',
        'order_id',
        'company_id',
        'creator_id',
        'deliver_now',
    ];

    protected $casts = [
        'deliver_now' => 'boolean',
    ];

    public function move(): BelongsTo
    {
        return $this->belongsTo(Move::class, 'move_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
