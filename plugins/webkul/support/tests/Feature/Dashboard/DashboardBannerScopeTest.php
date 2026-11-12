<?php

use Filament\Pages\Dashboard;
use Filament\Pages\Page;
use Webkul\Accounting\Filament\Pages\Overview;
use Webkul\Support\Filament\Contracts\ShowsDashboardBanner;
use Webkul\Support\Filament\Contributions\DashboardBannerContributions;

require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/CompanyHelper.php';
require_once __DIR__.'/../../Helpers/FilamentHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensureERPInstalled();
});

/**
 * The welcome banner is not tied to any plugin (user, 2026-11-12). It shows
 * on every Dashboard subclass and on a plain-Page module overview that opts in
 * with ShowsDashboardBanner, decided from the page being rendered, and on no
 * other page.
 */
it('shows the banner on dashboards and opted-in overviews only', function (): void {
    $dashboard = new class extends Dashboard {};
    $overview = new class extends Page implements ShowsDashboardBanner {};
    $ordinary = new class extends Page {};

    expect(DashboardBannerContributions::isDashboardPage([$dashboard::class]))->toBeTrue()
        ->and(DashboardBannerContributions::isDashboardPage([$overview::class]))->toBeTrue()
        ->and(DashboardBannerContributions::isDashboardPage([$ordinary::class]))->toBeFalse()
        ->and(DashboardBannerContributions::isDashboardPage(['NotAClass', 42]))->toBeFalse()
        // The real module overviews that are plain Pages opt in.
        ->and(is_subclass_of(Overview::class, ShowsDashboardBanner::class))->toBeTrue()
        ->and(is_subclass_of(Webkul\Inventory\Filament\Pages\Overview::class, ShowsDashboardBanner::class))->toBeTrue();
});

/**
 * Refined 2026-11-12: the greeting follows the viewer's own clock (rendered in
 * the browser, server text as fallback), the company appears once, and the
 * work location only when it differs from the company. It also runs where
 * Attendance and Employees are not installed: the attendance contribution once
 * queried employees_employees there and broke every dashboard (2026-11-12).
 */
it('greets from the viewer clock and names the company once', function (): void {
    FilamentHelper::bootAdminPanel();

    // State it explicitly: neither plugin installed, whatever an earlier run
    // left in this database. Package caches the plugin list per process.
    Webkul\PluginManager\Models\Plugin::query()
        ->whereIn('name', ['attendances', 'employees'])
        ->update(['is_installed' => false]);
    Webkul\PluginManager\Package::$plugins = Webkul\PluginManager\Models\Plugin::all()->keyBy('name');

    $company = CompanyHelper::company(['name' => 'TRACE365']);
    $user = CompanyHelper::actingAsCompanyUser($company);

    $html = view('support::dashboard-banner')->render();

    expect($html)->toContain('x-text="greeting"')
        ->and($html)->toContain('x-text="today"')
        ->and($html)->toContain('deka-welcome__avatar')
        ->and(substr_count($html, 'TRACE365'))->toBe(1)
        ->and($html)->not->toContain('@js(');
});
