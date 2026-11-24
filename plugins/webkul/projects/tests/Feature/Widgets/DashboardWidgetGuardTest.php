<?php

use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Project\Filament\Pages\Dashboard;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/DashboardWidgetGuardHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('projects');
    PluginAccess::flush();
});

/**
 * Dashboard plan DB-3 (2026-11-23): every widget the Projects dashboard
 * lists is absent without its permission or once Projects is off for the
 * company, and present with both. A widget added without its guard fails here.
 */
it('shows every projects dashboard widget only with its permission and the module', function (): void {
    FilamentHelper::bootAdminPanel();

    $company = Company::factory()->create();

    expect(DashboardWidgetGuardHelper::visibility(Dashboard::sectionWidgets(), 'projects', $company))
        ->toBe(DashboardWidgetGuardHelper::guarded(Dashboard::sectionWidgets()));
});
