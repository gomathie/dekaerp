<?php

namespace Webkul\Support\Filament\Contributions;

use Filament\Facades\Filament;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Webkul\Support\Filament\Contracts\ShowsDashboardBanner;

/**
 * Puts the welcome banner at the top of every module dashboard.
 *
 * A render hook scoped to the dashboard page classes, so no dashboard file is
 * edited and a dashboard added later is picked up automatically. The scope is
 * each page's own class because that is exactly what Filament passes to
 * PAGE_START (`BasePage::getRenderHookScopes()`).
 */
class DashboardBannerContributions
{
    public static function register(Panel $panel): void
    {
        $marker = 'support.dashboard_banner.'.$panel->getId();

        // A static "registered" flag survives a rebuilt test application while
        // Filament's view manager does not, leaving later applications without
        // the banner. The container marker is per application.
        if (app()->bound($marker)) {
            return;
        }

        app()->instance($marker, true);

        // Decided per request from the page being rendered (2026-11-12, user:
        // "not tied to any plugin"). Scoping to the Dashboard subclasses in
        // $panel->getPages() at boot missed module overviews built on a plain
        // Page (Accounting, Inventory), and depended on every plugin having
        // added its pages before Support booted. The hook receives the current
        // page's classes as $scopes: every Dashboard subclass shows the
        // banner, and a plain-Page overview opts in with ShowsDashboardBanner.
        FilamentView::registerRenderHook(
            PanelsRenderHook::PAGE_START,
            function (array $scopes = []): string {
                if (! Filament::auth()->check() || ! static::isDashboardPage($scopes)) {
                    return '';
                }

                return view('support::dashboard-banner')->render();
            },
        );
    }

    /**
     * @param  array<int, string>  $scopes
     */
    public static function isDashboardPage(array $scopes): bool
    {
        foreach ($scopes as $scope) {
            if (! is_string($scope) || ! class_exists($scope)) {
                continue;
            }

            if (is_subclass_of($scope, Dashboard::class) || is_subclass_of($scope, ShowsDashboardBanner::class)) {
                return true;
            }
        }

        return false;
    }
}
