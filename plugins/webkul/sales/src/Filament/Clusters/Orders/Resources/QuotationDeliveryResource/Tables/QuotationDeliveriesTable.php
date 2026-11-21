<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationDeliveryResource\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\OperationResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationDeliveryResource;

class QuotationDeliveriesTable
{
    public static function configure(Table $table): Table
    {
        return OperationResource::table($table)
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->url(fn ($record): string => QuotationDeliveryResource::getUrl('view', ['record' => $record], shouldGuessMissingParameters: true)),
                    EditAction::make()
                        ->url(fn ($record): string => QuotationDeliveryResource::getUrl('edit', ['record' => $record], shouldGuessMissingParameters: true)),
                ]),
            ]);
    }
}
