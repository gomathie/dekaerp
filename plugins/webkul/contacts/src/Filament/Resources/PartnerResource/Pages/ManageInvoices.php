<?php

namespace Webkul\Contact\Filament\Resources\PartnerResource\Pages;

use Webkul\Contact\Filament\Resources\PartnerResource;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\CustomerResource\Pages\ManageInvoices as BaseManageInvoices;

/** The customer invoices tab on the Contacts screen (user, 2026-11-22). */
class ManageInvoices extends BaseManageInvoices
{
    protected static string $resource = PartnerResource::class;
}
