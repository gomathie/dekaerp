<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Webkul\Project\Models\Timesheet;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\PluginManager\Support\PluginEntitlement;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('timesheets');
    PluginAccess::flush();
});

it('enforces the timesheets entitlement through a registered resource policy', function (): void {
    $companyA = Company::factory()->create();
    $companyB = Company::factory()->create();
    $user = CompanyHelper::actingAsCompanyUser($companyA, ['view_any_timesheet_timesheet']);
    $plugin = Plugin::query()->where('name', 'timesheets')->firstOrFail();

    expect($plugin->is_installed)->toBeTrue()
        ->and(PluginEntitlement::enabledForCompany('timesheets', (int) $companyA->getKey()))->toBeTrue()
        ->and(Gate::forUser($user)->allows('viewAny', Timesheet::class))->toBeTrue();

    DB::table('company_plugin')
        ->where('company_id', $companyA->getKey())
        ->where('plugin_id', $plugin->getKey())
        ->delete();
    PluginAccess::flush();

    expect(Gate::forUser($user)->allows('viewAny', Timesheet::class))->toBeFalse()
        ->and(PluginEntitlement::enabledForCompany('timesheets', (int) $companyB->getKey()))->toBeTrue();
});