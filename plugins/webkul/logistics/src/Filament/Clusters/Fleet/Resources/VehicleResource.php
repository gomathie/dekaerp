<?php

namespace Webkul\Logistics\Filament\Clusters\Fleet\Resources;

use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Webkul\Logistics\Filament\Clusters\Fleet;
use Webkul\Logistics\Filament\Clusters\Fleet\Resources\VehicleResource\Pages\CreateVehicle;
use Webkul\Logistics\Filament\Clusters\Fleet\Resources\VehicleResource\Pages\EditVehicle;
use Webkul\Logistics\Filament\Clusters\Fleet\Resources\VehicleResource\Pages\ListVehicles;
use Webkul\Logistics\Filament\Clusters\Fleet\Resources\VehicleResource\Pages\ViewVehicle;
use Webkul\Logistics\Filament\Clusters\Fleet\Resources\VehicleResource\Schemas\VehicleForm;
use Webkul\Logistics\Filament\Clusters\Fleet\Resources\VehicleResource\Schemas\VehicleInfolist;
use Webkul\Logistics\Filament\Clusters\Fleet\Resources\VehicleResource\Tables\VehiclesTable;
use Webkul\Logistics\Models\Vehicle;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-truck';

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = Fleet::class;

    protected static ?string $recordTitleAttribute = 'registration_no';

    public static function getModelLabel(): string
    {
        return __('logistics::models/vehicle.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('logistics::filament/clusters/fleet/resources/vehicle.navigation.title');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['registration_no', 'name', 'telematics_device_ref'];
    }

    public static function form(Schema $schema): Schema
    {
        return VehicleForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return VehicleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VehiclesTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class])
            ->with(['vehicleType:id,name', 'carrier:id,name', 'defaultDriver:id,name', 'company:id,name']);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewVehicle::class,
            EditVehicle::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListVehicles::route('/'),
            'create' => CreateVehicle::route('/create'),
            'view'   => ViewVehicle::route('/{record}'),
            'edit'   => EditVehicle::route('/{record}/edit'),
        ];
    }
}
