<?php

namespace Webkul\Contact\Filament\Resources\PartnerResource\Pages;

use Webkul\Contact\Filament\Resources\PartnerResource;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\VendorResource\Pages\ManagePurchases as BaseManagePurchases;

/** The purchase orders tab on the Contacts screen (unified contacts, 2026-11-11). */
class ManagePurchases extends BaseManagePurchases
{
    protected static string $resource = PartnerResource::class;
}
