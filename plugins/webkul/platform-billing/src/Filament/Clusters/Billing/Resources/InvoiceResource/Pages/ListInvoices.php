<?php

namespace Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\InvoiceResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\InvoiceResource;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;
}
