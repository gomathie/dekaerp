<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\ExpenseResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ExpenseResource;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ExpenseResource\Actions\DeleteExpenseAction;

class ViewExpense extends ViewRecord
{
    protected static string $resource = ExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteExpenseAction::make(),
        ];
    }
}
