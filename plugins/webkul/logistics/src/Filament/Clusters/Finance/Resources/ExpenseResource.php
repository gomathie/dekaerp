<?php

namespace Webkul\Logistics\Filament\Clusters\Finance\Resources;

use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Webkul\Logistics\Filament\Clusters\Finance;
use Webkul\Logistics\Filament\Clusters\Finance\Resources\ExpenseResource\Pages\CreateExpense;
use Webkul\Logistics\Filament\Clusters\Finance\Resources\ExpenseResource\Pages\EditExpense;
use Webkul\Logistics\Filament\Clusters\Finance\Resources\ExpenseResource\Pages\ListExpenses;
use Webkul\Logistics\Filament\Clusters\Finance\Resources\ExpenseResource\Pages\ViewExpense;
use Webkul\Logistics\Filament\Clusters\Finance\Resources\ExpenseResource\Schemas\ExpenseForm;
use Webkul\Logistics\Filament\Clusters\Finance\Resources\ExpenseResource\Schemas\ExpenseInfolist;
use Webkul\Logistics\Filament\Clusters\Finance\Resources\ExpenseResource\Tables\ExpensesTable;
use Webkul\Logistics\Models\Expense;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = Finance::class;

    protected static ?string $recordTitleAttribute = 'description';

    public static function getModelLabel(): string
    {
        return __('logistics::models/expense.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('logistics::filament/clusters/finance/resources/expense.navigation.title');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['description', 'vendor_reference', 'category.name'];
    }

    public static function form(Schema $schema): Schema
    {
        return ExpenseForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ExpenseInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExpensesTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class])
            ->with(['category:id,name', 'shipment:id,name', 'trip:id,name', 'vehicle:id,registration_no', 'company:id,name']);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewExpense::class,
            EditExpense::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListExpenses::route('/'),
            'create' => CreateExpense::route('/create'),
            'view'   => ViewExpense::route('/{record}'),
            'edit'   => EditExpense::route('/{record}/edit'),
        ];
    }
}
