<?php

namespace Webkul\Logistics\Filament\Clusters\Configurations\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Logistics\Filament\Clusters\Configurations;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\Concerns\ConfiguresCompanyScope;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\ExpenseCategoryResource\Pages\ManageExpenseCategories;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\ExpenseCategoryResource\Schemas\ExpenseCategoryForm;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\ExpenseCategoryResource\Tables\ExpenseCategoriesTable;
use Webkul\Logistics\Models\ExpenseCategory;

class ExpenseCategoryResource extends Resource
{
    use ConfiguresCompanyScope;

    protected static ?string $model = ExpenseCategory::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';

    protected static ?int $navigationSort = 4;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static string $lang = 'logistics::filament/clusters/configurations/resources/expense-category';

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
        return ExpenseCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExpenseCategoriesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageExpenseCategories::route('/'),
        ];
    }
}
