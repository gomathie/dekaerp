<?php

namespace Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource\Pages;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Table;
use Livewire\Livewire;
use Webkul\Inventory\Enums\OperationType as InventoryOperationType;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\DeliveryResource;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\DropshipResource;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\InternalResource;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\OperationResource;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ReceiptResource;
use Webkul\Inventory\Models\Operation;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManageTransfers extends ManageRelatedRecords
{
    use HasRecordNavigationTabs;

    protected static string $resource = ManufacturingOrderResource::class;

    protected static string $relationship = 'inventoryOperations';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';

    public static function getNavigationLabel(): string
    {
        return __('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/manage-transfers.title');
    }

    public static function getNavigationBadge($parameters = []): ?string
    {
        return (string) Livewire::current()->getRecord()->inventoryOperations()->count();
    }

    public function table(Table $table): Table
    {
        return OperationResource::table($table)
            ->recordActions([
                ViewAction::make()
                    ->url(fn (Operation $record): string => static::resolveOperationResourceClass($record)::getUrl('view', ['record' => $record]))
                    ->openUrlInNewTab(true),

                EditAction::make()
                    ->url(fn (Operation $record): string => static::resolveOperationResourceClass($record)::getUrl('edit', ['record' => $record]))
                    ->openUrlInNewTab(true),
            ])
            ->defaultSort('created_at', 'desc')
            ->toolbarActions([]);
    }

    protected static function resolveOperationResourceClass(Operation $operation): string
    {
        return match ($operation->operationType?->type) {
            InventoryOperationType::INCOMING => ReceiptResource::class,
            InventoryOperationType::INTERNAL => InternalResource::class,
            InventoryOperationType::OUTGOING => DeliveryResource::class,
            InventoryOperationType::DROPSHIP => DropshipResource::class,
            default                          => OperationResource::class,
        };
    }
}
