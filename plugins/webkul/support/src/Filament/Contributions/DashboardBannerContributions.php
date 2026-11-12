<?php

namespace Webkul\Support\Filament\Contributions;

use Filament\Facades\Filament;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;

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

        $scopes = array_values(array_filter(
            $panel->getPages(),
            fn (string $page): bool => is_subclass_of($page, Dashboard::class),
        ));

        if ($scopes === []) {
            return;
        }

        FilamentView::registerRenderHook(
            PanelsRenderHook::PAGE_START,
            function (): string {
                if (! Filament::auth()->check()) {
                    return '';
                }

                return view('support::dashboard-banner')->render();
            },
            scopes: $scopes,
        );
    }
}
