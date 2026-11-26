<?php

namespace Webkul\Invoice\Filament\Clusters\Customers\Resources\RecurringInvoiceTemplateResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\RecurringInvoiceTemplateResource;
use Webkul\Invoice\Models\RecurringInvoiceTemplate;

class CreateRecurringInvoiceTemplate extends CreateRecord
{
    protected static string $resource = RecurringInvoiceTemplateResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = current_company_id();
        $data['state'] = 'active';
        $data['invoice_user_id'] ??= auth()->id();

        return $data;
    }

    protected function handleRecordCreation(array $data): RecurringInvoiceTemplate
    {
        return RecurringInvoiceTemplate::create($data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }
}
