<?php

namespace Webkul\Account\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Webkul\Account\Models\Move;

/**
 * Fired once an entry is deleted, from every path (screens, API, imports).
 * Sales uses it to recompute an order whose invoice was deleted.
 */
class MoveDeleted
{
    use Dispatchable;

    public function __construct(
        public Move $move
    ) {}
}
