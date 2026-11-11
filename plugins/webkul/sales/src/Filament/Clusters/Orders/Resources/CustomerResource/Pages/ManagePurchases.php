<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource\Pages;

use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\VendorResource\Pages\ManagePurchases as BaseManagePurchases;
use Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource;

/** The purchase orders tab on the Sales customer screen (unified contacts, 2026-11-11). */
class ManagePurchases extends BaseManagePurchases
{
    protected static string $resource = CustomerResource::class;
}
