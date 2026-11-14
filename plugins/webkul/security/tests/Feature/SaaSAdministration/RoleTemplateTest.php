<?php

use Illuminate\Support\Facades\DB;
use Webkul\Security\Models\Permission;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Security\Services\RoleTemplateProvisioner;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensureERPInstalled();
});

/**
 * Role templates (CR-5, 2026-11-13, option A: installation-wide roles).
 * Pinned: the access levels mean what they say, a template never carries a
 * security or platform permission, and an edited template is never reset.
 */
it('maps access levels to permission names without crossing plugins', function () {
    $provisioner = app(RoleTemplateProvisioner::class);
    $standard = ['account' => RoleTemplateProvisioner::STANDARD];
    $view = ['invoice' => RoleTemplateProvisioner::VIEW];

    expect($provisioner->grants($standard, 'update_account_journal'))->toBeTrue()
        ->and($provisioner->grants($standard, 'delete_account_journal'))->toBeFalse()
        ->and($provisioner->grants($standard, 'force_delete_any_account_journal'))->toBeFalse()
        // "account" must not reach the separate accounting plugin.
        ->and($provisioner->grants($standard, 'view_any_accounting_journal::entry'))->toBeFalse()
        ->and($provisioner->grants($view, 'view_any_invoice_invoice'))->toBeTrue()
        ->and($provisioner->grants($view, 'page_invoice_overview'))->toBeTrue()
        ->and($provisioner->grants($view, 'create_invoice_invoice'))->toBeFalse();
});

it('never gives a template a security, role, plugin or bypass permission', function () {
    $everything = ['security' => RoleTemplateProvisioner::FULL, 'company' => RoleTemplateProvisioner::FULL];
    $provisioner = app(RoleTemplateProvisioner::class);

    foreach (['view_any_security_user', 'bypass_company_scope', 'tenant.impersonate', 'create_role', 'view_any_plugin_manager_plugin'] as $name) {
        expect($provisioner->grants($everything, $name))->toBeFalse();
    }

    app(RoleTemplateProvisioner::class)->provision();

    $templateIds = Role::query()->whereIn('name', array_keys(RoleTemplateProvisioner::TEMPLATES))->pluck('id');
    $granted = DB::table('role_has_permissions')
        ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
        ->whereIn('role_has_permissions.role_id', $templateIds)
        ->pluck('permissions.name');

    expect($granted->filter(fn (string $name): bool => in_array($name, User::PLATFORM_ONLY_ABILITIES, true)
        || str_contains($name, '_security_') || str_contains($name, 'impersonate'))->all())->toBe([]);
});

it('creates each template once and never resets an edited one', function () {
    app(RoleTemplateProvisioner::class)->provision();

    $accountant = Role::query()->where('name', 'Accountant')->firstOrFail();
    $accountant->permissions()->sync(Permission::query()->where('name', 'like', 'view\_%')->limit(3)->pluck('id'));

    // Contacts are a core plugin, so this template has rows in every suite.
    expect(Role::query()->where('name', 'Logistics Manager')->firstOrFail()->permissions()->count())->toBeGreaterThan(0)
        ->and($accountant->permissions()->pluck('name')->filter(fn (string $n): bool => str_starts_with($n, 'delete_'))->all())->toBe([]);

    // Staff trim the role on the Roles screen; a later deploy must keep that.
    $accountant->permissions()->sync([]);

    $created = app(RoleTemplateProvisioner::class)->provision();

    expect($created)->toBe([])
        ->and($accountant->fresh()->permissions()->count())->toBe(0)
        ->and(Role::query()->whereIn('name', array_keys(RoleTemplateProvisioner::TEMPLATES))->count())
        ->toBe(count(RoleTemplateProvisioner::TEMPLATES));
});

it('is created by the deploy permission sync', function () {
    Role::query()->whereIn('name', array_keys(RoleTemplateProvisioner::TEMPLATES))->get()->each->delete();

    $this->artisan('erp:sync-permissions')->assertSuccessful();

    expect(Role::query()->where('name', 'Finance Manager')->exists())->toBeTrue()
        ->and(Permission::query()->count())->toBeGreaterThan(0);
});
