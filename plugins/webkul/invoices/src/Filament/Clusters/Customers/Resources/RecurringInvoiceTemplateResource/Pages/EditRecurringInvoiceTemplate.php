<?php

namespace Webkul\Invoice\Filament\Clusters\Customers\Resources\RecurringInvoiceTemplateResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\RecurringInvoiceTemplateResource;

class EditRecurringInvoiceTemplate extends EditRecord
{
    protected static string $resource = RecurringInvoiceTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }
}
