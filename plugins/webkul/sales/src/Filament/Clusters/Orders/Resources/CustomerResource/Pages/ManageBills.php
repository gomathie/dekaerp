<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource\Pages;

use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\VendorResource\Pages\ManageBills as BaseManageBills;
use Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource;

/** The vendor bills tab on the Sales customer screen (unified contacts, 2026-11-11). */
class ManageBills extends BaseManageBills
{
    protected static string $resource = CustomerResource::class;
}
