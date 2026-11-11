<?php

namespace Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\DiscountCodeResource\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Throwable;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\DiscountCodeResource;
use Webkul\PlatformBilling\Filament\Support\ReportsServiceFailure;
use Webkul\PlatformBilling\Models\DiscountCode;
use Webkul\PlatformBilling\Services\DiscountCodeService;

class ViewDiscountCode extends ViewRecord
{
    protected static string $resource = DiscountCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('deactivate')
                ->label(__('platform-billing::filament/billing.discounts.actions.deactivate'))
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn (DiscountCode $record): bool => (bool) $record->is_active)
                ->action(function (DiscountCode $record, Action $action): void {
                    try {
                        app(DiscountCodeService::class)->setActive($record, ReportsServiceFailure::actor(), false);
                    } catch (Throwable $exception) {
                        ReportsServiceFailure::onAction($action, $exception);
                    }
                }),
        ];
    }
}
