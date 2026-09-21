<?php

namespace Webkul\Logistics\Filament\Clusters\Operations\Resources;

use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Webkul\Logistics\Filament\Clusters\Operations;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\TripResource\Pages\CreateTrip;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\TripResource\Pages\EditTrip;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\TripResource\Pages\ListTrips;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\TripResource\Pages\ViewTrip;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\TripResource\RelationManagers\ShipmentsRelationManager;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\TripResource\Schemas\TripForm;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\TripResource\Schemas\TripInfolist;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\TripResource\Tables\TripsTable;
use Webkul\Logistics\Models\Trip;

class TripResource extends Resource
{
    protected static ?string $model = Trip::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map';

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = Operations::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('logistics::models/trip.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('logistics::filament/clusters/operations/resources/trip.navigation.title');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'vehicle.registration_no', 'driver.name'];
    }

    public static function form(Schema $schema): Schema
    {
        return TripForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TripInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TripsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        // Eager loaded because the list and the dispatch board both render the
        // vehicle, driver and dispatcher for every row.
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class])
            ->with(['vehicle:id,registration_no,name', 'driver:id,name', 'dispatcher:id,name', 'company:id,name']);
    }

    public static function getRelations(): array
    {
        return [
            ShipmentsRelationManager::class,
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewTrip::class,
            EditTrip::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTrips::route('/'),
            'create' => CreateTrip::route('/create'),
            'view'   => ViewTrip::route('/{record}'),
            'edit'   => EditTrip::route('/{record}/edit'),
        ];
    }
}
