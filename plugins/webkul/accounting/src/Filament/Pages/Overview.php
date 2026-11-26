<?php

namespace Webkul\Accounting\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Webkul\Accounting\Filament\Widgets\JournalChartsWidget;
use Webkul\PluginManager\Support\PluginEntitlement;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Support\Filament\Concerns\ChecksPagePermission;
use Webkul\Support\Filament\Contracts\ShowsDashboardBanner;
use Webkul\Support\Filament\DashboardAccess;

class Overview extends Page implements ShowsDashboardBanner
{
    use ChecksPagePermission;
    use HasPageShield;

    protected static ?string $slug = 'accounting/overview';

    protected string $view = 'accounting::filament.pages.overview';

    protected static function getPagePermission(): ?string
    {
        return 'page_accounting_overview';
    }

    public static function getNavigationLabel(): string
    {
        return __('accounting::filament/pages/overview.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Accounting;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            JournalChartsWidget::class,
        ];
    }

    public static function canAccess(): bool
    {
        return PluginEntitlement::enabledForCurrent('accounting')
            && static::hasPagePermission()
            // The same two switches as the other money dashboards (user,
            // 2026-11-25). Off hides this page, its widgets and the
            // Accounting section of the combined Overview.
            && DashboardAccess::mayViewMoney();
    }
}
