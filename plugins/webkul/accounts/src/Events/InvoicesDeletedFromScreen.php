<?php

namespace Webkul\Account\Events;

use Illuminate\Foundation\Events\Dispatchable;

/**
 * Fired by the customer invoice delete actions after the delete, with what
 * the person answered in the confirmation (fields contributed through
 * InvoiceSchemaRegistry slot "delete"). Accounts does not know what the
 * answers mean; the plugin that asked the question acts on them.
 */
class InvoicesDeletedFromScreen
{
    use Dispatchable;

    /**
     * @param  list<int>  $moveIds
     * @param  array<string, mixed>  $data
     */
    public function __construct(
        public array $moveIds,
        public array $data,
    ) {}
}
