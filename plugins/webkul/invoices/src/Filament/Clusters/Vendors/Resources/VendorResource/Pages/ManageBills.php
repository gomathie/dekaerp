<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource\Pages;

use Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\VendorResource\Pages\ManageBills as BaseManageBills;

/** The vendor bills tab on the Invoicing vendor screen (unified contacts, 2026-11-11). */
class ManageBills extends BaseManageBills
{
    protected static string $resource = VendorResource::class;
}
