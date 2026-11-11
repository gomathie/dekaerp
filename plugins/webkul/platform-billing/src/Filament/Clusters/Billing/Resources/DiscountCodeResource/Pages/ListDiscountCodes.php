<?php

namespace Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\DiscountCodeResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\DiscountCodeResource;

class ListDiscountCodes extends ListRecords
{
    protected static string $resource = DiscountCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
