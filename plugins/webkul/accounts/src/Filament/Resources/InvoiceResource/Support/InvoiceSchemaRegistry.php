<?php

namespace Webkul\Account\Filament\Resources\InvoiceResource\Support;

use Webkul\Support\Filament\Contributions\AbstractSchemaRegistry;

/**
 * Lets plugins that depend on Accounts (Sales) add fields to the customer
 * invoice form and view without Accounts referencing them.
 */
class InvoiceSchemaRegistry extends AbstractSchemaRegistry
{
    protected static function scope(): string
    {
        return 'invoice';
    }
}
