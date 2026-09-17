<?php

namespace Webkul\Logistics\Filament\Clusters\Configurations\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Logistics\Filament\Clusters\Configurations;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\Concerns\ConfiguresCompanyScope;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\PackageTypeResource\Pages\ManagePackageTypes;
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
        return $schema
            ->components([
                static::nameField(),
                static::codeField(),
                static::companyField(),
                static::activeField(),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                static::nameColumn(),
                static::codeColumn(),
                static::companyColumn(),
                static::activeColumn(),
            ])
            ->filters([
                static::activeFilter(),
                static::companyFilter(),
            ])
            ->reorderable('sort')
            ->defaultSort('sort')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePackageTypes::route('/'),
        ];
    }
}
