<?php

namespace Webkul\PlatformBilling\Filament\Clusters\Billing\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\PlatformBilling\Filament\Clusters\Billing;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\PaymentResource\Pages\CreatePayment;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\PaymentResource\Pages\ListPayments;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\PaymentResource\Pages\ViewPayment;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\PaymentResource\Schemas\PaymentForm;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\PaymentResource\Schemas\PaymentInfolist;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\PaymentResource\Tables\PaymentsTable;
use Webkul\PlatformBilling\Models\Payment;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $cluster = Billing::class;

    protected static ?string $recordTitleAttribute = 'idempotency_key';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 4;

    public static function getModelLabel(): string
    {
        return __('platform-billing::filament/billing.payments.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('platform-billing::filament/billing.payments.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('platform-billing::filament/billing.payments.navigation');
    }

    public static function form(Schema $schema): Schema
    {
        return PaymentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PaymentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PaymentsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['company:id,name']);
    }

    /**
     * Posted payments, allocations and reversals are immutable. The create
     * page posts through PlatformPayments; it does not edit the row after.
     */
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
            'index'  => ListPayments::route('/'),
            'create' => CreatePayment::route('/create'),
            'view'   => ViewPayment::route('/{record}'),
        ];
    }
}
