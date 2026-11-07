<?php

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Illuminate\Support\Facades\DB;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Recruitment\Filament\Widgets\ApplicantChartWidget;
use Webkul\Recruitment\Filament\Widgets\ApplicantsByStageChart;
use Webkul\Recruitment\Filament\Widgets\JobPositionStatsWidget;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('recruitments');
    PluginAccess::flush();
});

/**
 * Before 2026-11-07 ApplicantChartWidget had no check, so any user could load
 * the applicant figures. Every Recruitment dashboard widget now needs its own
 * permission and the Recruitments module (GuardsDashboardWidget).
 */
it('guards each recruitment widget by permission and by the Recruitments module', function (string $widget): void {
    FilamentHelper::bootAdminPanel();

    $company = Company::factory()->create();

    CompanyHelper::actingAsCompanyUser($company, []);

    expect($widget::canView())->toBeFalse();

    $permission = array_key_first(FilamentShield::getWidgets()[$widget]['permissions'] ?? []);

    expect($permission)->not->toBeNull();

    CompanyHelper::actingAsCompanyUser($company, [$permission]);

    expect($widget::canView())->toBeTrue();

    $plugin = Plugin::query()->where('name', 'recruitments')->firstOrFail();

    DB::table('company_plugin')
        ->where('company_id', $company->getKey())
        ->where('plugin_id', $plugin->getKey())
        ->delete();

    PluginAccess::flush();

    expect($widget::canView())->toBeFalse();
})->with([
    'applicant chart'     => ApplicantChartWidget::class,
    'applicants by stage' => ApplicantsByStageChart::class,
    'job position stats'  => JobPositionStatsWidget::class,
]);
