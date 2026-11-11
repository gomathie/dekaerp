<?php

namespace Webkul\Accounting\Filament\Clusters\Customers\Resources\CustomerResource\Pages;

use Webkul\Accounting\Filament\Clusters\Customers\Resources\CustomerResource;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\VendorResource\Pages\ManagePurchases as BaseManagePurchases;

/** The purchase orders tab on the Accounting customer screen (unified contacts, 2026-11-11). */
class ManagePurchases extends BaseManagePurchases
{
    protected static string $resource = CustomerResource::class;
}
