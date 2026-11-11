<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\VendorResource\Pages;

use Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource\Pages\ManageBankAccounts as BaseManageBankAccounts;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\VendorResource;

/**
 * The bank accounts tab on the Purchases vendor screen (unified contacts,
 * user 2026-11-11). Every other contact screen already had it; a buyer paying
 * a supplier needed to leave Purchases to see where to pay.
 */
class ManageBankAccounts extends BaseManageBankAccounts
{
    protected static string $resource = VendorResource::class;
}
