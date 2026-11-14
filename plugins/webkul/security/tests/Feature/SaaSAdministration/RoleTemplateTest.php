<?php

use Illuminate\Support\Facades\DB;
use Webkul\Security\Models\Permission;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Security\Services\MultiCompanyAdminService;
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

it('gives the administrators business role every template permission, adding only', function () {
    // CR-3 production finding (2026-11-14): a narrower business role hid every
    // template from Company Admins, who may assign only what they hold.
    $provisioner = app(RoleTemplateProvisioner::class);
    $provisioner->provision();

    $business = Role::query()->create(['name' => 'Business Test '.uniqid(), 'guard_name' => 'web']);
    // A business permission the role already had; it must survive. Not a
    // security one, or the "nothing security was added" check below would
    // flag the fixture instead of the code.
    $own = Permission::query()
        ->where('name', 'like', 'view\_%')
        ->where('name', 'not like', '%\_security\_%')
        ->firstOrFail();
    $business->permissions()->sync([$own->getKey()]);

    $added = $provisioner->coverBusinessRole((int) $business->getKey());
    $held = $business->fresh()->permissions()->pluck('id');

    $templateIds = DB::table('role_has_permissions')
        ->whereIn('role_id', Role::query()->whereIn('name', array_keys(RoleTemplateProvisioner::TEMPLATES))->pluck('id'))
        ->distinct()->pluck('permission_id');

    expect($added)->toBeGreaterThan(0)
        ->and($templateIds->diff($held)->all())->toBe([])
        ->and($held)->toContain($own->getKey())
        ->and($business->fresh()->permissions()->pluck('name')->filter(fn (string $n): bool => str_contains($n, '_security_') || in_array($n, User::PLATFORM_ONLY_ABILITIES, true))->all())->toBe([])
        // A second run adds nothing, and an unknown role is ignored.
        ->and($provisioner->coverBusinessRole((int) $business->getKey()))->toBe(0)
        ->and($provisioner->coverBusinessRole(null))->toBe(0);
});

it('keeps every template free of what a Company Admin may not hand out', function () {
    // CR-3 production check (2026-11-14): "full" templates carried
    // force_delete_*, so five of the eight never reached a Company Admin's
    // role list. Existing templates are cleaned on the next sync.
    $provisioner = app(RoleTemplateProvisioner::class);
    $provisioner->provision();

    $finance = Role::query()->where('name', 'Finance Manager')->firstOrFail();
    $forceDelete = Permission::query()->where('name', 'like', 'force\_delete\_%')->first();

    if ($forceDelete) {
        $finance->permissions()->attach($forceDelete->getKey());
    }

    $provisioner->provision();

    $denied = Role::query()->whereIn('name', array_keys(RoleTemplateProvisioner::TEMPLATES))->get()
        ->flatMap(fn (Role $role) => $role->permissions()->pluck('name'))
        ->filter(fn (string $name): bool => app(MultiCompanyAdminService::class)->deniesAbility($name))
        ->values()
        ->all();

    expect($denied)->toBe([])
        ->and($provisioner->grants(['account' => RoleTemplateProvisioner::FULL], 'force_delete_any_account_journal'))->toBeFalse()
        ->and($provisioner->grants(['account' => RoleTemplateProvisioner::FULL], 'delete_account_journal'))->toBeTrue();
});

it('is created by the deploy permission sync', function () {
    Role::query()->whereIn('name', array_keys(RoleTemplateProvisioner::TEMPLATES))->get()->each->delete();

    $this->artisan('erp:sync-permissions')->assertSuccessful();

    expect(Role::query()->where('name', 'Finance Manager')->exists())->toBeTrue()
        ->and(Permission::query()->count())->toBeGreaterThan(0);
});
