<?php

namespace Webkul\TimeOff\Filament\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Dashboard as BaseDashboard;
use Webkul\TimeOff\Filament\Clusters\MyTime;
use Webkul\TimeOff\Filament\Widgets\CalendarWidget;
use Webkul\TimeOff\Filament\Widgets\MyTimeOffWidget;

class Dashboard extends BaseDashboard
{
    use HasPageShield;

    protected static string $routePath = 'time-off';

    /*
     * getRouteName() is deliberately NOT overridden.
     *
     * It used to return the hardcoded 'filament.admin.pages.time-off-dashboard'.
     * This page lives in the MyTime cluster, so Filament registers it as
     * 'filament.admin.time-off.dashboard...' - the hardcoded name was not in
     * the route table at all, and Page::getUrl() calls route() on whatever
     * getRouteName() returns, so building the navigation item threw
     * RouteNotFoundException and the page 500'd. Letting Filament answer means
     * the name can never drift from the one it registered.
     */

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $cluster = MyTime::class;

    protected static ?int $navigationSort = 1;

    protected static function getPagePermission(): ?string
    {
        return 'page_time_off_dashboard';
    }

    public static function getNavigationLabel(): string
    {
        return __('time-off::filament/pages/dashboard.navigation.title');
    }

    public function getWidgets(): array
    {
        return [
            CalendarWidget::class,
        ];
    }

    public function getHeaderWidgets(): array
    {
        return [
            MyTimeOffWidget::make(),
        ];
    }
}
