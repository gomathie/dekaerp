<?php

namespace Webkul\PlatformBilling\Filament\Clusters\Billing\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\PlatformBilling\Filament\Clusters\Billing;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\PlanResource\Pages\CreatePlan;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\PlanResource\Pages\ListPlans;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\PlanResource\Pages\ViewPlan;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\PlanResource\Schemas\PlanForm;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\PlanResource\Schemas\PlanInfolist;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\PlanResource\Tables\PlansTable;
use Webkul\PlatformBilling\Models\Plan;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static ?string $cluster = Billing::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return __('platform-billing::filament/billing.plans.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('platform-billing::filament/billing.plans.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('platform-billing::filament/billing.plans.navigation');
    }

    public static function form(Schema $schema): Schema
    {
        return PlanForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PlanInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PlansTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withCount('prices');
    }

    /**
     * Deactivate, do not edit or delete. A sold subscription keeps its copy,
     * and the policy refuses delete even for a Super Admin.
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
            'index' => ListPlans::route('/'),
            'create' => CreatePlan::route('/create'),
            'view' => ViewPlan::route('/{record}'),
        ];
    }
}
