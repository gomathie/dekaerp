<?php

namespace Webkul\PlatformBilling\Filament\Clusters\Billing\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\PlatformBilling\Filament\Clusters\Billing;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\DiscountCodeResource\Pages\CreateDiscountCode;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\DiscountCodeResource\Pages\ListDiscountCodes;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\DiscountCodeResource\Pages\ViewDiscountCode;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\DiscountCodeResource\Schemas\DiscountCodeForm;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\DiscountCodeResource\Schemas\DiscountCodeInfolist;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\DiscountCodeResource\Tables\DiscountCodesTable;
use Webkul\PlatformBilling\Models\DiscountCode;

class DiscountCodeResource extends Resource
{
    protected static ?string $model = DiscountCode::class;

    protected static ?string $cluster = Billing::class;

    protected static ?string $recordTitleAttribute = 'code';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static ?int $navigationSort = 5;

    public static function getModelLabel(): string
    {
        return __('platform-billing::filament/billing.discounts.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('platform-billing::filament/billing.discounts.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('platform-billing::filament/billing.discounts.navigation');
    }

    public static function form(Schema $schema): Schema
    {
        return DiscountCodeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DiscountCodeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DiscountCodesTable::configure($table);
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
            'index'  => ListDiscountCodes::route('/'),
            'create' => CreateDiscountCode::route('/create'),
            'view'   => ViewDiscountCode::route('/{record}'),
        ];
    }
}
