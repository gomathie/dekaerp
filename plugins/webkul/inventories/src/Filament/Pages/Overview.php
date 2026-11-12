<?php

namespace Webkul\Inventory\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Webkul\Inventory\Filament\Widgets\OperationTypeOverviewWidget;
use Webkul\PluginManager\Support\PluginEntitlement;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Support\Filament\Concerns\ChecksPagePermission;
use Webkul\Support\Filament\Contracts\ShowsDashboardBanner;

class Overview extends Page implements ShowsDashboardBanner
{
    use ChecksPagePermission;
    use HasPageShield;

    protected static ?string $slug = 'inventory/overview';

    protected string $view = 'inventories::filament.pages.overview';

    protected static ?int $navigationSort = 0;

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/pages/overview.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Inventory;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            OperationTypeOverviewWidget::class,
        ];
    }

    public static function canAccess(): bool
    {
        return PluginEntitlement::enabledForCurrent('inventories')
            && static::hasPagePermission();
    }
}
