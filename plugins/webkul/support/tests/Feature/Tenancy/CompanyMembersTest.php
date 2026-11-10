<?php

use Webkul\Security\Models\User;
use Webkul\Support\Filament\CompanyMembers;
use Webkul\Support\Models\Company;
use Webkul\Support\Services\CompanyContext;

require_once __DIR__.'/../../Helpers/SecurityHelper.php';
require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensureERPInstalled();
});

/**
 * Pickers listed every user on the installation, other tenants' staff
 * included (the Project dashboard's Assignees filter, 2026-11-10). They list
 * members of the company the viewer is working in.
 */
it('lists only members of the company the viewer is working in', function () {
    [$mine, $theirs] = Company::factory()->count(2)->create();

    $colleague = User::factory()->create(['name' => 'Same Company Colleague']);
    $colleague->allowedCompanies()->sync([$mine->getKey()]);

    $stranger = User::factory()->create(['name' => 'Other Tenant Stranger']);
    $stranger->allowedCompanies()->sync([$theirs->getKey()]);

    $viewer = SecurityHelper::authenticateWithPermissions([]);
    $viewer->allowedCompanies()->sync([$mine->getKey()]);
    $viewer->forceFill(['default_company_id' => $mine->getKey()])->saveQuietly();
    session([CompanyContext::SESSION_KEY => [$mine->getKey()]]);
    app()->forgetInstance(CompanyContext::class);

    $options = CompanyMembers::options();

    expect($options)->toContain('Same Company Colleague')
        ->and($options)->not->toContain('Other Tenant Stranger');
});
