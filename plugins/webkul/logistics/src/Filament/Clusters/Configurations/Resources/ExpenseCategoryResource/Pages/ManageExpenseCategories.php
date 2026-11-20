<?php

namespace Webkul\Logistics\Filament\Clusters\Configurations\Resources\ExpenseCategoryResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\ExpenseCategoryResource;

class ManageExpenseCategories extends ManageRecords
{
    protected static string $resource = ExpenseCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
