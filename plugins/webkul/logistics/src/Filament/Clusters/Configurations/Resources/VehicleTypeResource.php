<?php

namespace Webkul\Logistics\Filament\Clusters\Configurations\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Logistics\Filament\Clusters\Configurations;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\Concerns\ConfiguresCompanyScope;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\VehicleTypeResource\Pages\ManageVehicleTypes;
use Webkul\Logistics\Models\VehicleType;

class VehicleTypeResource extends Resource
{
    use ConfiguresCompanyScope;

    protected static ?string $model = VehicleType::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string $lang = 'logistics::filament/clusters/configurations/resources/vehicle-type';

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
        return $schema
            ->components([
                static::nameField(),
                static::codeField(),
                TextInput::make('capacity_kg')
                    ->label(__(static::$lang.'.form.fields.capacity-kg'))
                    ->numeric()
                    ->minValue(0)
                    ->suffix('kg'),
                TextInput::make('capacity_m3')
                    ->label(__(static::$lang.'.form.fields.capacity-m3'))
                    ->numeric()
                    ->minValue(0)
                    ->suffix('m³'),
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
                TextColumn::make('capacity_kg')
                    ->label(__(static::$lang.'.table.columns.capacity-kg'))
                    ->numeric()
                    ->placeholder('—')
                    ->visibleFrom('sm')
                    ->sortable(),
                TextColumn::make('capacity_m3')
                    ->label(__(static::$lang.'.table.columns.capacity-m3'))
                    ->numeric()
                    ->placeholder('—')
                    ->visibleFrom('md')
                    ->sortable(),
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
            'index' => ManageVehicleTypes::route('/'),
        ];
    }
}
