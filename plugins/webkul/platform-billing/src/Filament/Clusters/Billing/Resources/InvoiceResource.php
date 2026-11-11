<?php

namespace Webkul\PlatformBilling\Filament\Clusters\Billing\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\PlatformBilling\Filament\Clusters\Billing;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\InvoiceResource\Pages\ListInvoices;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\InvoiceResource\Pages\ViewInvoice;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\InvoiceResource\Schemas\InvoiceForm;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\InvoiceResource\Schemas\InvoiceInfolist;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\InvoiceResource\Tables\InvoicesTable;
use Webkul\PlatformBilling\Models\Invoice;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $cluster = Billing::class;

    protected static ?string $recordTitleAttribute = 'number';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 3;

    public static function getModelLabel(): string
    {
        return __('platform-billing::filament/billing.invoices.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('platform-billing::filament/billing.invoices.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('platform-billing::filament/billing.invoices.navigation');
    }

    public static function form(Schema $schema): Schema
    {
        return InvoiceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return InvoiceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InvoicesTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['company:id,name']);
    }

    /**
     * A posted invoice is the snapshot taken at posting. There is no edit
     * screen; the database trigger is what still refuses a direct write.
     */
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInvoices::route('/'),
            'view'  => ViewInvoice::route('/{record}'),
        ];
    }
}
