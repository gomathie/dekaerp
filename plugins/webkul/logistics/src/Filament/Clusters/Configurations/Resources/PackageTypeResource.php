<?php

namespace Webkul\Logistics\Filament\Clusters\Configurations\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Logistics\Filament\Clusters\Configurations;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\Concerns\ConfiguresCompanyScope;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\PackageTypeResource\Pages\ManagePackageTypes;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\PackageTypeResource\Schemas\PackageTypeForm;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\PackageTypeResource\Tables\PackageTypesTable;
use Webkul\Logistics\Models\PackageType;

class PackageTypeResource extends Resource
{
    use ConfiguresCompanyScope;

    protected static ?string $model = PackageType::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    protected static ?int $navigationSort = 3;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string $lang = 'logistics::filament/clusters/configurations/resources/package-type';

    public static function getModelLabel(): string
    {
        return __('logistics::models/package-type.title');
    }

    public static function getNavigationLabel(): string
    {
        return __(static::$lang.'.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return PackageTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PackageTypesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePackageTypes::route('/'),
        ];
    }
}
