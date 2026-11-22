<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource\Pages;

use Webkul\Invoice\Filament\Clusters\Customers\Resources\CustomerResource\Pages\ManageInvoices as BaseManageInvoices;
use Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource;

/** The customer invoices tab on the Sales customer screen (user, 2026-11-22). */
class ManageInvoices extends BaseManageInvoices
{
    protected static string $resource = CustomerResource::class;
}
