<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationReceiptResource\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\OperationResource;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationReceiptResource;

class QuotationReceiptsTable
{
    public static function configure(Table $table): Table
    {
        return OperationResource::table($table)
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->url(fn ($record): string => QuotationReceiptResource::getUrl('view', ['record' => $record], shouldGuessMissingParameters: true)),
                    EditAction::make()
                        ->url(fn ($record): string => QuotationReceiptResource::getUrl('edit', ['record' => $record], shouldGuessMissingParameters: true)),
                ]),
            ]);
    }
}
