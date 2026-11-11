<?php

namespace Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\SubscriptionResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\SubscriptionResource;

class ListSubscriptions extends ListRecords
{
    protected static string $resource = SubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
