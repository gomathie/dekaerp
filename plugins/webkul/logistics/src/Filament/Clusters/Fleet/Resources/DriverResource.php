<?php

namespace Webkul\Logistics\Filament\Clusters\Fleet\Resources;

use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Webkul\Logistics\Filament\Clusters\Fleet;
use Webkul\Logistics\Filament\Clusters\Fleet\Resources\DriverResource\Pages\CreateDriver;
use Webkul\Logistics\Filament\Clusters\Fleet\Resources\DriverResource\Pages\EditDriver;
use Webkul\Logistics\Filament\Clusters\Fleet\Resources\DriverResource\Pages\ListDrivers;
use Webkul\Logistics\Filament\Clusters\Fleet\Resources\DriverResource\Pages\ViewDriver;
use Webkul\Logistics\Filament\Clusters\Fleet\Resources\DriverResource\Schemas\DriverForm;
use Webkul\Logistics\Filament\Clusters\Fleet\Resources\DriverResource\Schemas\DriverInfolist;
use Webkul\Logistics\Filament\Clusters\Fleet\Resources\DriverResource\Tables\DriversTable;
use Webkul\Logistics\Models\Driver;

class DriverResource extends Resource
{
    protected static ?string $model = Driver::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-identification';

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = Fleet::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('logistics::models/driver.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('logistics::filament/clusters/fleet/resources/driver.navigation.title');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'phone', 'employee.name', 'partner.name'];
    }

    public static function form(Schema $schema): Schema
    {
        return DriverForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DriverInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DriversTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class])
            ->with(['employee:id,name', 'partner:id,name', 'company:id,name']);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewDriver::class,
            EditDriver::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListDrivers::route('/'),
            'create' => CreateDriver::route('/create'),
            'view'   => ViewDriver::route('/{record}'),
            'edit'   => EditDriver::route('/{record}/edit'),
        ];
    }
}
