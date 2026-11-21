<?php

namespace Webkul\Logistics\Filament\Clusters\Configurations\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Logistics\Filament\Clusters\Configurations;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\Concerns\ConfiguresCompanyScope;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\VehicleTypeResource\Pages\ManageVehicleTypes;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\VehicleTypeResource\Schemas\VehicleTypeForm;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\VehicleTypeResource\Tables\VehicleTypesTable;
use Webkul\Logistics\Models\VehicleType;

class VehicleTypeResource extends Resource
{
    use ConfiguresCompanyScope;

    protected static ?string $model = VehicleType::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static string $lang = 'logistics::filament/clusters/configurations/resources/vehicle-type';

    public static function getModelLabel(): string
    {
        return __('logistics::models/vehicle-type.title');
    }

    public static function getNavigationLabel(): string
    {
        return __(static::$lang.'.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return VehicleTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VehicleTypesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageVehicleTypes::route('/'),
        ];
    }
}
