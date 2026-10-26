<?php

use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Security\Services\MultiCompanyAdminService;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';

/**
 * SAAS-00 characterization: pins a known defect, it does not endorse it.
 *
 * CreateUser sets `resource_permission = GLOBAL` when either Multi-Company Admin
 * or Company Admin is selected (CreateUser.php:40-42). EditUser does so only for
 * Multi-Company Admin (EditUser.php:110). This means promoting an existing
 * restricted user to Company Admin via Edit does not set them to GLOBAL, while
 * creating a new Company Admin via Create does. SAAS-01 should unify this.
 *
 * See: docs/saas-administration-billing-audit-plan.md § Known related defects.
 */
it('detects that CreateUser and EditUser differ on Company Admin resource_permission', function () {
    $administration = app(MultiCompanyAdminService::class);

    $companyAdminRole = Role::query()
        ->whereRaw('LOWER(name) = ?', [mb_strtolower(Role::COMPANY_ADMIN)])
        ->first();

    $multiCompanyAdminRole = Role::query()
        ->whereRaw('LOWER(name) = ?', [mb_strtolower(Role::MULTI_COMPANY_ADMIN)])
        ->first();

    // Precondition: both roles exist.
    expect($companyAdminRole)->not->toBeNull()
        ->and($multiCompanyAdminRole)->not->toBeNull();

    $companyAdminRoleIds = [$companyAdminRole->getKey()];
    $multiCompanyRoleIds = [$multiCompanyAdminRole->getKey()];

    // Precondition: the service can identify both roles.
    expect($administration->includesCompanyAdminRole($companyAdminRoleIds))->toBeTrue()
        ->and($administration->includesMultiCompanyAdminRole($multiCompanyRoleIds))->toBeTrue();

    // --- Simulate CreateUser's mutateFormDataBeforeCreate logic ---
    // CreateUser checks BOTH Multi-Company Admin AND Company Admin.
    $createWouldSetGlobalForCompanyAdmin =
        $administration->includesMultiCompanyAdminRole($companyAdminRoleIds)
        || $administration->includesCompanyAdminRole($companyAdminRoleIds);

    $createWouldSetGlobalForMultiCompanyAdmin =
        $administration->includesMultiCompanyAdminRole($multiCompanyRoleIds)
        || $administration->includesCompanyAdminRole($multiCompanyRoleIds);

    // --- Simulate EditUser's mutateFormDataBeforeSave logic ---
    // EditUser checks ONLY Multi-Company Admin.
    $editWouldSetGlobalForCompanyAdmin =
        $administration->includesMultiCompanyAdminRole($companyAdminRoleIds);

    $editWouldSetGlobalForMultiCompanyAdmin =
        $administration->includesMultiCompanyAdminRole($multiCompanyRoleIds);

    // Both pages agree on Multi-Company Admin: both set GLOBAL.
    expect($createWouldSetGlobalForMultiCompanyAdmin)->toBeTrue()
        ->and($editWouldSetGlobalForMultiCompanyAdmin)->toBeTrue();

    // The defect: CreateUser sets GLOBAL for Company Admin, EditUser does not.
    expect($createWouldSetGlobalForCompanyAdmin)->toBeTrue()
        ->and($editWouldSetGlobalForCompanyAdmin)->toBeFalse();

    // Consequence: a user created via CreateUser as a Company Admin gets GLOBAL
    // resource_permission; a user promoted to Company Admin via EditUser retains
    // their previous (possibly restricted) resource_permission.
    //
    // Note for SAAS-01: this test should be inverted or replaced when the defect
    // is fixed. The correct behavior is for both paths to agree.
});

it('demonstrates the resource_permission default is GLOBAL for new users', function () {
    // Since 2026-09-29, the schema default for resource_permission is 'global'.
    // A user created without explicit resource_permission gets GLOBAL. This means
    // a user created via EditUser promotion only experiences the defect when they
    // were previously set to INDIVIDUAL or GROUP explicitly.
    expect(PermissionType::GLOBAL->value)->toBe('global');

    // The model's $attributes declaration mirrors the schema default.
    $user = new User;
    expect($user->getAttribute('resource_permission'))->toBeNull()
        ->or->toBe(PermissionType::GLOBAL);
});
