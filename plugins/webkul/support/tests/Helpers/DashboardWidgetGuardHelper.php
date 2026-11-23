<?php

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Illuminate\Support\Facades\DB;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Support\Models\Company;

require_once __DIR__.'/CompanyHelper.php';

/**
 * Dashboard plan DB-3 (2026-11-23): a dashboard widget is absent, never an
 * empty frame, when its module is off for the company or the user lacks its
 * permission. A module's own tests pin one widget; this checks every widget
 * a dashboard lists, so a widget added without its guard is caught.
 */
class DashboardWidgetGuardHelper
{
    /**
     * Each widget's canView() answer for a user with no permission, for a
     * user with every listed widget's Shield permission (plus $extra), and
     * for that same user once the company's module grant is removed.
     *
     * @param  list<class-string>  $widgets
     * @param  list<string>  $extra  permissions a widget checks besides its Shield one
     * @return array<class-string, array{none: bool, granted: bool, revoked: bool}>
     */
    public static function visibility(array $widgets, string $plugin, Company $company, array $extra = []): array
    {
        $shield = FilamentShield::getWidgets();

        $permissions = collect($widgets)
            ->flatMap(fn (string $widget): array => array_keys($shield[$widget]['permissions'] ?? []))
            ->merge($extra)
            ->unique()
            ->values()
            ->all();

        $answers = [];

        CompanyHelper::actingAsCompanyUser($company, []);

        foreach ($widgets as $widget) {
            $answers[$widget]['none'] = $widget::canView();
        }

        CompanyHelper::actingAsCompanyUser($company, $permissions);

        foreach ($widgets as $widget) {
            $answers[$widget]['granted'] = $widget::canView();
        }

        DB::table('company_plugin')
            ->where('company_id', $company->getKey())
            ->where('plugin_id', Plugin::query()->where('name', $plugin)->value('id'))
            ->delete();

        PluginAccess::flush();

        foreach ($widgets as $widget) {
            $answers[$widget]['revoked'] = $widget::canView();
        }

        return $answers;
    }

    /**
     * What visibility() must return: hidden, shown, hidden.
     *
     * @param  list<class-string>  $widgets
     * @return array<class-string, array{none: bool, granted: bool, revoked: bool}>
     */
    public static function guarded(array $widgets): array
    {
        return collect($widgets)
            ->mapWithKeys(fn (string $widget): array => [$widget => ['none' => false, 'granted' => true, 'revoked' => false]])
            ->all();
    }
}
