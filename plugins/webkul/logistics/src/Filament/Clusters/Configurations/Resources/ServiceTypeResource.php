<?php

namespace Webkul\Logistics\Filament\Clusters\Configurations\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Webkul\Logistics\Enums\TransportMode;
use Webkul\Logistics\Filament\Clusters\Configurations;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\Concerns\ConfiguresCompanyScope;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\ServiceTypeResource\Pages\ManageServiceTypes;
use Webkul\Logistics\Models\ServiceType;

class ServiceTypeResource extends Resource
{
    use ConfiguresCompanyScope;

    protected static ?string $model = ServiceType::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-truck';

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string $lang = 'logistics::filament/clusters/configurations/resources/service-type';

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
        return $schema
            ->components([
                static::nameField(),
                static::codeField(),
                Select::make('transport_mode')
                    ->label(__(static::$lang.'.form.fields.transport-mode'))
                    ->options(TransportMode::class)
                    ->default(TransportMode::ROAD)
                    ->required()
                    ->native(false),
                TextInput::make('product_reference')
                    ->label(__(static::$lang.'.form.fields.product-reference'))
                    ->helperText(__(static::$lang.'.form.fields.product-reference-helper'))
                    ->maxLength(64),
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
                TextColumn::make('transport_mode')
                    ->label(__(static::$lang.'.table.columns.transport-mode'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('product_reference')
                    ->label(__(static::$lang.'.table.columns.product-reference'))
                    ->placeholder('—')
                    ->visibleFrom('lg'),
                static::companyColumn(),
                static::activeColumn(),
            ])
            ->filters([
                static::activeFilter(),
                static::companyFilter(),
                TrashedFilter::make(),
            ])
            ->reorderable('sort')
            ->defaultSort('sort')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
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
