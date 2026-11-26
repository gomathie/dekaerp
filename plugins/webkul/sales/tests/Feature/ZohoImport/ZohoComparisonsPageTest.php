<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Sale\Filament\Pages\ZohoComparisons;
use Webkul\Sale\Models\ZohoImportRun;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Support\Services\CompanyContext;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../Helpers/ZohoImportHelper.php';

/**
 * The company's own read-only page of Zoho comparisons (user, 2026-11-26):
 * an active Super Admin, or a Company Admin who is a member of the company,
 * and only that company's results.
 */
beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('sales');

    DB::table('plugins')->updateOrInsert(['name' => 'sales'], ['is_installed' => true, 'is_active' => true, 'updated_at' => now()]);
    Package::$plugins = Plugin::all()->keyBy('name');
    PluginAccess::flush();
    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    $this->company = zohoTestCompany();
    $this->other = zohoTestCompany();

    $result = fn (string $label) => [
        'rows'      => [['section' => 'Bank and cash balance', 'label' => $label, 'zoho' => 10.0, 'deka' => 10.0, 'difference' => 0.0, 'status' => 'OK']],
        'unmatched' => [['side' => 'zoho', 'kind' => 'invoice', 'number' => 'INV-1', 'date' => '2026-12-03', 'partner' => '<b>x</b>', 'amount' => 1200.0, 'text' => 'In Zoho, not in DEKA: invoice INV-1, 1,200.00 on 2026-12-03 (<b>x</b>)']],
    ];

    $this->mine = ZohoImportRun::query()->create(['company_id' => $this->company->id, 'mode' => ZohoImportRun::MODE_COMPARE, 'status' => ZohoImportRun::FINISHED, 'comparison' => $result('Mine bank'), 'finished_at' => now()]);
    $this->theirs = ZohoImportRun::query()->create(['company_id' => $this->other->id, 'mode' => ZohoImportRun::MODE_COMPARE, 'status' => ZohoImportRun::FINISHED, 'comparison' => $result('Other company bank'), 'finished_at' => now()]);
});

function zohoComparisonUser(array $companyIds, bool $companyAdmin = true): User
{
    $user = User::withoutEvents(fn (): User => User::factory()->create([
        'is_active'          => true,
        'is_default'         => false,
        'default_company_id' => $companyIds[0] ?? null,
    ]));

    if ($companyAdmin) {
        $role = Role::query()->whereRaw('LOWER(name) = ?', [mb_strtolower(Role::COMPANY_ADMIN)])->first()
            ?? Role::create(['name' => Role::COMPANY_ADMIN, 'guard_name' => 'web']);
        $user->syncRoles([$role->getKey()]);
    }

    $user->allowedCompanies()->sync($companyIds);

    Auth::shouldUse('web');
    Auth::guard('web')->login($user->fresh());
    session([CompanyContext::SESSION_KEY => $companyIds]);
    app()->forgetInstance(CompanyContext::class);
    FilamentHelper::bootAdminPanel();

    return $user;
}

it('shows a member Company Admin their own company\'s comparison, escaped', function () {
    zohoComparisonUser([$this->company->id]);

    expect(ZohoComparisons::canAccess())->toBeTrue();

    Livewire::test(ZohoComparisons::class)
        ->assertOk()
        ->assertSee('Mine bank')
        ->assertSee('&lt;b&gt;x&lt;/b&gt;', escape: false)
        ->assertDontSee('Other company bank');
});

it('never shows another company\'s run, whatever id is asked for', function () {
    zohoComparisonUser([$this->company->id]);

    Livewire::test(ZohoComparisons::class)
        ->call('selectRun', $this->theirs->id)
        ->assertSee('Mine bank')
        ->assertDontSee('Other company bank');
});

it('refuses users who are not a Company Admin, and a Company Admin of another company', function () {
    zohoComparisonUser([$this->company->id], companyAdmin: false);

    expect(ZohoComparisons::canAccess())->toBeFalse();
    Livewire::test(ZohoComparisons::class)->assertForbidden();

    zohoComparisonUser([$this->other->id]);

    // Even asking the session for the first company: the context keeps an
    // outsider in their own company, so only its results are shown.
    session([CompanyContext::SESSION_KEY => [$this->company->id]]);
    app()->forgetInstance(CompanyContext::class);

    Livewire::test(ZohoComparisons::class)
        ->assertOk()
        ->assertSee('Other company bank')
        ->assertDontSee('Mine bank');
});
