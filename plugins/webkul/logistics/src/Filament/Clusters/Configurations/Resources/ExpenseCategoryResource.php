<?php

namespace Webkul\Logistics\Filament\Clusters\Configurations\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Webkul\Logistics\Filament\Clusters\Configurations;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\Concerns\ConfiguresCompanyScope;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\ExpenseCategoryResource\Pages\ManageExpenseCategories;
use Webkul\Logistics\Models\ExpenseCategory;

class ExpenseCategoryResource extends Resource
{
    use ConfiguresCompanyScope;

    protected static ?string $model = ExpenseCategory::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';

    protected static ?int $navigationSort = 4;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string $lang = 'logistics::filament/clusters/configurations/resources/expense-category';

    public static function getModelLabel(): string
    {
        return __('logistics::models/expense-category.title');
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
                Toggle::make('requires_receipt')
                    ->label(__(static::$lang.'.form.fields.requires-receipt')),
                Toggle::make('is_subcontracting')
                    ->label(__(static::$lang.'.form.fields.is-subcontracting'))
                    ->helperText(__(static::$lang.'.form.fields.is-subcontracting-helper')),
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
                IconColumn::make('requires_receipt')
                    ->label(__(static::$lang.'.table.columns.requires-receipt'))
                    ->boolean()
                    ->visibleFrom('sm'),
                IconColumn::make('is_subcontracting')
                    ->label(__(static::$lang.'.table.columns.is-subcontracting'))
                    ->boolean()
                    ->visibleFrom('md'),
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
            'index' => ManageExpenseCategories::route('/'),
        ];
    }
}
