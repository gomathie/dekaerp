<?php

namespace Webkul\Accounting\Filament\Clusters\Customers\Resources\CustomerResource\Pages;

use Webkul\Accounting\Filament\Clusters\Customers\Resources\CustomerResource;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\CustomerResource\Pages\ManageInvoices as BaseManageInvoices;

/** The customer invoices tab on the Accounting customer screen (user, 2026-11-22). */
class ManageInvoices extends BaseManageInvoices
{
    protected static string $resource = CustomerResource::class;
}
