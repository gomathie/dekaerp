<?php

namespace Webkul\Account\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Webkul\Account\Models\Move;

/**
 * Fired just before an entry is deleted, while what links to it can still be
 * read: deleting it cascades away the links other plugins keep (Sales'
 * sales_order_invoices), so a listener that needs them reads them here.
 */
class MoveDeleting
{
    use Dispatchable;

    public function __construct(
        public Move $move
    ) {}
}
