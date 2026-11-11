<?php

namespace Webkul\Invoice\Filament\Clusters\Customers\Resources\CustomerResource\Pages;

use Webkul\Invoice\Filament\Clusters\Customers\Resources\CustomerResource;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\VendorResource\Pages\ManageBills as BaseManageBills;

/** The vendor bills tab on the Invoicing customer screen (unified contacts, 2026-11-11). */
class ManageBills extends BaseManageBills
{
    protected static string $resource = CustomerResource::class;
}
