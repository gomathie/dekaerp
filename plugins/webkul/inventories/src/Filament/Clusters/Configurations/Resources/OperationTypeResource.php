<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Inventory\Enums;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Filament\Clusters\Configurations;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\OperationTypeResource\Pages\CreateOperationType;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\OperationTypeResource\Pages\EditOperationType;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\OperationTypeResource\Pages\ListOperationTypes;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\OperationTypeResource\Pages\ViewOperationType;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\OperationTypeResource\Schemas\OperationTypeForm;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\OperationTypeResource\Schemas\OperationTypeInfolist;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\OperationTypeResource\Tables\OperationTypesTable;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\OperationType;
use Webkul\Inventory\Models\Warehouse;

class OperationTypeResource extends Resource
{
    protected static ?string $model = OperationType::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-queue-list';

    protected static ?int $navigationSort = 3;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $isGloballySearchable = false;

    public static function getNavigationGroup(): string
    {
        return __('inventories::filament/clusters/configurations/resources/operation-type.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/configurations/resources/operation-type.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return OperationTypeForm::configure($schema);
    }

    public static function getDefaultWarehouseId($companyId): ?int
    {
        return Warehouse::where(owned_by_company($companyId))->first()?->id;
    }

    protected static function normalizeOperationType($type): ?Enums\OperationType
    {
        return $type instanceof Enums\OperationType
            ? $type
            : Enums\OperationType::tryFrom((string) $type);
    }

    public static function getDefaultSourceLocationId($type, $warehouseId, $companyId): ?int
    {
        return match (static::normalizeOperationType($type)) {
            Enums\OperationType::INCOMING => static::virtualLocationId(LocationType::SUPPLIER, $companyId),
            Enums\OperationType::OUTGOING,
            Enums\OperationType::INTERNAL => static::replenishLocationId($warehouseId, $companyId),
            default                       => null,
        };
    }

    public static function getDefaultDestinationLocationId($type, $warehouseId, $companyId): ?int
    {
        return match (static::normalizeOperationType($type)) {
            Enums\OperationType::OUTGOING => static::virtualLocationId(LocationType::CUSTOMER, $companyId),
            Enums\OperationType::INCOMING,
            Enums\OperationType::INTERNAL => static::replenishLocationId($warehouseId, $companyId),
            default                       => null,
        };
    }

    protected static function virtualLocationId(LocationType $type, $companyId): ?int
    {
        return Location::where('type', $type)
            ->where(owned_by_company($companyId))
            ->first()?->id;
    }

    protected static function replenishLocationId($warehouseId, $companyId): ?int
    {
        return Location::where('is_replenish', true)
            ->where(owned_by_company($companyId))
            ->when($warehouseId, fn ($query) => $query->where('warehouse_id', $warehouseId))
            ->first()?->id;
    }

    public static function table(Table $table): Table
    {
        return OperationTypesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OperationTypeInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListOperationTypes::route('/'),
            'create' => CreateOperationType::route('/create'),
            'view'   => ViewOperationType::route('/{record}'),
            'edit'   => EditOperationType::route('/{record}/edit'),
        ];
    }
}
