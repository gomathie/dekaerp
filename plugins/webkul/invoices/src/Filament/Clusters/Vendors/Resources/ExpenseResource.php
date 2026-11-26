<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources;

use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Invoice\Filament\Clusters\Vendors;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ExpenseResource\Pages\CreateExpense;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ExpenseResource\Pages\EditExpense;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ExpenseResource\Pages\ListExpenses;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ExpenseResource\Pages\ViewExpense;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ExpenseResource\Schemas\ExpenseForm;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ExpenseResource\Schemas\ExpenseInfolist;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ExpenseResource\Tables\ExpensesTable;
use Webkul\Invoice\Models\Expense;

/**
 * Expenses (plan #10 Z1, mockup approved 2026-11-26): beside Bills in the
 * Vendors cluster, so it follows Bills under Purchase or Invoices
 * (Vendors::getNavigationGroup()). Every write goes through
 * Account\Services\ExpenseRecorder; the pages only collect the fields.
 */
class ExpenseResource extends Resource
{
    public const LANG = 'invoices::filament/clusters/vendors/resources/expense';

    protected static ?string $model = Expense::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';

    protected static bool $shouldRegisterNavigation = true;

    protected static bool $isGloballySearchable = false;

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = Vendors::class;

    protected static ?string $recordTitleAttribute = 'number';

    public static function getNavigationGroup(): ?string
    {
        return null;
    }

    public static function getModelLabel(): string
    {
        return __(self::LANG.'.title');
    }

    public static function getPluralModelLabel(): string
    {
        return __(self::LANG.'.navigation.title');
    }

    public static function getNavigationLabel(): string
    {
        return __(self::LANG.'.navigation.title');
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
        return parent::getEloquentQuery()->with(['expenseAccount', 'journal', 'partner', 'currency', 'company.currency']);
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
