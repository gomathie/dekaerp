<?php

namespace Webkul\Logistics\Filament\Clusters\Finance\Resources\ExpenseResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Webkul\Logistics\Filament\Clusters\Finance\Resources\ExpenseResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class EditExpense extends EditRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = ExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
