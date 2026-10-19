<?php

use Webkul\Account\Enums\MoveType;
use Webkul\Account\Models\Move;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/AccountHelper.php';

/**
 * Ownership on the money documents.
 *
 * `Account\Models\Move` is the highest-stakes model carrying a global
 * `OwnershipScope`, and it had no ownership coverage at all - only the company
 * scope was proven. `users.resource_permission` defaults to `individual`, so what
 * these tests describe is what an ordinary user sees in production today:
 * invoices they raised, and invoices assigned to them as the salesperson.
 *
 * Whether `individual` should be the default is a separate, open product question
 * (docs/company-admin-role-plan.md §4g). These tests set the permission
 * explicitly, so they hold either way.
 */
beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function accountOwnershipActor($company, PermissionType $permission): User
{
    $actor = CompanyHelper::actingAsCompanyUser($company);

    $actor->forceFill(['resource_permission' => $permission])->saveQuietly();

    return $actor;
}

it('hides a colleagues invoice from an individual user', function () {
    $company = AccountHelper::company();

    $colleague = SecurityHelper::authenticateWithPermissions([]);

    $theirs = AccountHelper::invoice(MoveType::OUT_INVOICE, null, null, [
        'company_id' => $company->id,
        'creator_id' => $colleague->getKey(),
    ]);

    $actor = accountOwnershipActor($company, PermissionType::INDIVIDUAL);

    $mine = AccountHelper::invoice(MoveType::OUT_INVOICE, null, null, [
        'company_id' => $company->id,
        'creator_id' => $actor->getKey(),
    ]);

    // Same company for both, so this is ownership doing the work and not
    // CompanyScope. This is the money: an individual user cannot see what a
    // colleague invoiced.
    expect($theirs->company_id)->toBe($mine->company_id);

    $visible = Move::query()->pluck('id');

    expect($visible)->toContain($mine->id)
        ->not->toContain($theirs->id);
});

it('shows an individual user an invoice they are the salesperson on', function () {
    $company = AccountHelper::company();

    $colleague = SecurityHelper::authenticateWithPermissions([]);

    $actor = accountOwnershipActor($company, PermissionType::INDIVIDUAL);

    // invoice_user_id is Move's second ownership source and nothing exercised it.
    // It is the salesperson, so an invoice raised by the bookkeeper and assigned
    // to them has to be visible or they cannot chase their own sale.
    $assigned = AccountHelper::invoice(MoveType::OUT_INVOICE, null, null, [
        'company_id'      => $company->id,
        'creator_id'      => $colleague->getKey(),
        'invoice_user_id' => $actor->getKey(),
    ]);

    $unassigned = AccountHelper::invoice(MoveType::OUT_INVOICE, null, null, [
        'company_id' => $company->id,
        'creator_id' => $colleague->getKey(),
    ]);

    $visible = Move::query()->pluck('id');

    expect($visible)->toContain($assigned->id)
        ->not->toContain($unassigned->id);
});

it('lets a colleague see the company invoices by default', function () {
    $company = AccountHelper::company();

    $colleague = SecurityHelper::authenticateWithPermissions([]);

    $theirs = AccountHelper::invoice(MoveType::OUT_INVOICE, null, null, [
        'company_id' => $company->id,
        'creator_id' => $colleague->getKey(),
    ]);

    // No resource_permission set, so this takes the column default. Since
    // 2026-09-28 that is `global`: people in the same company see the company's
    // records, and CompanyScope is the boundary that matters. Before that the
    // default was `individual` and this assertion would have failed - which is
    // exactly the complaint that prompted the change.
    $actor = CompanyHelper::actingAsCompanyUser($company);

    expect($actor->refresh()->resource_permission)->toBe(PermissionType::GLOBAL)
        ->and(Move::query()->pluck('id'))->toContain($theirs->id);
});

it('shows a global user every invoice in the company', function () {
    $company = AccountHelper::company();

    $colleague = SecurityHelper::authenticateWithPermissions([]);

    $theirs = AccountHelper::invoice(MoveType::OUT_INVOICE, null, null, [
        'company_id' => $company->id,
        'creator_id' => $colleague->getKey(),
    ]);

    accountOwnershipActor($company, PermissionType::GLOBAL);

    expect(Move::query()->pluck('id'))->toContain($theirs->id);
});
