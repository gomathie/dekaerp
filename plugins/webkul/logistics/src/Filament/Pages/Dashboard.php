<?php

namespace Webkul\Logistics\Filament\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Logistics\Filament\Widgets\FleetStatsWidget;
use Webkul\Logistics\Filament\Widgets\ShipmentStatsWidget;
use Webkul\Logistics\Filament\Widgets\UnbilledRevenueWidget;
use Webkul\Logistics\Support\LogisticsAccess;
use Webkul\Support\Enums\NavigationGroup;

/**
 * The Logistics dashboard.
 *
 * Its own page rather than the panel's main dashboard: the widgets are
 * discovered by the plugin, so they also reach the main dashboard, but a
 * company that runs logistics wants them together and in one place.
 *
 * Access needs the page permission *and* the per-company switch. Each widget
 * repeats its own guard, so the page never has to decide which of them a given
 * user may see - the finance widget hides itself from anyone without
 * view_financials, here as everywhere else.
 */
class Dashboard extends BaseDashboard
{
    use HasPageShield;

    protected static string $routePath = 'logistics';

    protected static ?int $navigationSort = 10;

    protected static string $lang = 'logistics::filament/pages/dashboard';

    protected static function getPagePermission(): ?string
    {
        return 'page_logistics_dashboard';
    }

    /**
     * The page permission is checked here explicitly, not by delegating to
     * HasPageShield.
     *
     * A canAccess() defined on the class wins over the one the trait provides,
     * so `parent::canAccess()` here reaches Filament's Page::canAccess(), which
     * returns true - the Shield check would be skipped and the page would open
     * for anyone. Same shape as the UnbilledCharges page for the same reason.
     */
    public static function canAccess(): bool
    {
        return (auth()->user()?->can('page_logistics_dashboard') ?? false)
            && LogisticsAccess::enabledForCurrent();
    }

    public static function getNavigationLabel(): string
    {
        return __(static::$lang.'.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Dashboard;
    }

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return 'heroicon-o-truck';
    }

    public function getTitle(): string
    {
        return __(static::$lang.'.title');
    }

    public function getWidgets(): array
    {
        return [
            ShipmentStatsWidget::class,
            FleetStatsWidget::class,
            UnbilledRevenueWidget::class,
        ];
    }
}
