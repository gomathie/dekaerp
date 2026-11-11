<?php

namespace Webkul\Accounting\Filament\Clusters\Vendors\Resources\VendorResource\Pages;

use Webkul\Accounting\Filament\Clusters\Vendors\Resources\VendorResource;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\VendorResource\Pages\ManagePurchases as BaseManagePurchases;

/** The purchase orders tab on the Accounting vendor screen (unified contacts, 2026-11-11). */
class ManagePurchases extends BaseManagePurchases
{
    protected static string $resource = VendorResource::class;
}
