<?php

namespace Webkul\PlatformBilling\Filament\Clusters\Billing\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\PlatformBilling\Filament\Clusters\Billing;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\SubscriptionResource\Pages\CreateSubscription;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\SubscriptionResource\Pages\ListSubscriptions;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\SubscriptionResource\Pages\ViewSubscription;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\SubscriptionResource\Schemas\SubscriptionForm;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\SubscriptionResource\Schemas\SubscriptionInfolist;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\SubscriptionResource\Tables\SubscriptionsTable;
use Webkul\PlatformBilling\Models\Subscription;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $cluster = Billing::class;

    protected static ?string $recordTitleAttribute = 'plan_name';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return __('platform-billing::filament/billing.subscriptions.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('platform-billing::filament/billing.subscriptions.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('platform-billing::filament/billing.subscriptions.navigation');
    }

    public static function form(Schema $schema): Schema
    {
        return SubscriptionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SubscriptionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SubscriptionsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['company:id,name', 'plan:id,name']);
    }

    /**
     * Sold terms change through the lifecycle actions. There is no edit form
     * and no delete: a draft is left as a draft, and an active subscription
     * is cancelled at period end.
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
            'index' => ListSubscriptions::route('/'),
            'create' => CreateSubscription::route('/create'),
            'view' => ViewSubscription::route('/{record}'),
        ];
    }
}
