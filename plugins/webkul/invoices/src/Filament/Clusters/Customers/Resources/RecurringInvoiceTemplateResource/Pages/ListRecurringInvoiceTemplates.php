<?php

namespace Webkul\Invoice\Filament\Clusters\Customers\Resources\RecurringInvoiceTemplateResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\RecurringInvoiceTemplateResource;

class ListRecurringInvoiceTemplates extends ListRecords
{
    protected static string $resource = RecurringInvoiceTemplateResource::class;
}
