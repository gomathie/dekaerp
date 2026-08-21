<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\DirectSaleResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Webkul\Sale\Filament\Clusters\Orders\Resources\DirectSaleResource;

class ListDirectSales extends ListRecords
{
    protected static string $resource = DirectSaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('New Direct Sale'))
                ->icon('heroicon-o-plus'),
        ];
    }
}
