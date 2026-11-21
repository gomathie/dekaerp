<?php

namespace Webkul\Logistics\Filament\Clusters\Configurations\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Webkul\Logistics\Filament\Clusters\Configurations;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\Concerns\ConfiguresCompanyScope;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\ServiceTypeResource\Pages\ManageServiceTypes;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\ServiceTypeResource\Schemas\ServiceTypeForm;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\ServiceTypeResource\Tables\ServiceTypesTable;
use Webkul\Logistics\Models\ServiceType;

class ServiceTypeResource extends Resource
{
    use ConfiguresCompanyScope;

    protected static ?string $model = ServiceType::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-truck';

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static string $lang = 'logistics::filament/clusters/configurations/resources/service-type';

    public static function getModelLabel(): string
    {
        return __('logistics::models/service-type.title');
    }

    public static function getNavigationLabel(): string
    {
        return __(static::$lang.'.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return ServiceTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ServiceTypesTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageServiceTypes::route('/'),
        ];
    }
}
