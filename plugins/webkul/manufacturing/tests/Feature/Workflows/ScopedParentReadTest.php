<?php

use Webkul\Inventory\Enums\ManufactureStep;
use Webkul\Manufacturing\Models\Move;
use Webkul\Manufacturing\Models\Order;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/ManufacturingHelper.php';

/**
 * `Manufacturing\Models\Move` reads its parent order and its operation type
 * through relations inside model hooks, and both targets are globally scoped:
 * `Manufacturing\Models\Order` carries `OwnershipScope` as well as `CompanyScope`,
 * and `Inventory\Models\OperationType` carries `CompanyScope`.
 *
 * These tests exist to establish whether that is actually reachable, rather than
 * infer it from reading the code. See docs/company-admin-role-plan.md section 4i.
 */
beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    TestBootstrapHelper::ensurePluginInstalled('manufacturing');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function scopedParentActor($company, PermissionType $permission): User
{
    $actor = CompanyHelper::actingAsCompanyUser($company);

    $actor->forceFill(['resource_permission' => $permission])->saveQuietly();

    return $actor;
}

it('derives a component move from an order the actor does not own', function () {
    $company = ManufacturingHelper::company();
    $warehouse = ManufacturingHelper::multiStepWarehouse(ManufactureStep::ONE_STEP);
    $product = ManufacturingHelper::product();
    $component = ManufacturingHelper::product();

    // Built by somebody else, so the order's creator is not the actor below.
    $owner = SecurityHelper::authenticateWithPermissions([]);
    $order = ManufacturingHelper::order($warehouse, $product, null, 5);
    $order->forceFill(['creator_id' => $owner->getKey()])->saveQuietly();

    $actor = scopedParentActor($company, PermissionType::INDIVIDUAL);

    // Proof the branch under test is really taken: the actor cannot see the order,
    // so `$move->rawMaterialOrder ?? $move->order` resolves to null inside the
    // creating hook. Without this the test could pass for the wrong reason.
    expect($actor->getKey())->not->toBe($owner->getKey())
        ->and(Order::query()->whereKey($order->getKey())->exists())->toBeFalse();

    $move = ManufacturingHelper::addComponent($order->refresh(), $component, 2);

    // The creating hook returns early when the order is unreadable, skipping the
    // name, origin, procurement group, locations, schedule and deadline it exists
    // to derive. A component move with no name is the visible symptom.
    expect($move->name)->not->toBeNull();
});

it('does not null the warehouse of a move on a plain re-save', function () {
    $warehouse = ManufacturingHelper::multiStepWarehouse(ManufactureStep::ONE_STEP);
    $product = ManufacturingHelper::product();

    $order = ManufacturingHelper::order($warehouse, $product, null, 5);

    $move = Move::query()->where('order_id', $order->id)->firstOrFail();

    expect($move->warehouse_id)->not->toBeNull();

    $before = $move->warehouse_id;

    // `saving` does `$move->warehouse_id = $move->operationType?->warehouse_id`
    // with `=`, not `??=`. Eloquent fires `saving` on every save(), dirty or not,
    // so an unreadable operation type does not merely fail to populate - it
    // overwrites whatever was there with null. A plain re-save must be a no-op.
    $move->save();

    expect($move->refresh()->warehouse_id)->toBe($before);
});

it('keeps the warehouse when the operation type is outside the active companies', function () {
    $warehouse = ManufacturingHelper::multiStepWarehouse(ManufactureStep::ONE_STEP);
    $product = ManufacturingHelper::product();

    $order = ManufacturingHelper::order($warehouse, $product, null, 5);

    $move = Move::query()->where('order_id', $order->id)->firstOrFail();

    $before = $move->warehouse_id;

    expect($before)->not->toBeNull();

    // A different company becomes the active one, so the operation type the saving
    // hook reads is now hidden by CompanyScope. The move still belongs to its own
    // warehouse; which warehouse that is does not depend on who is looking.
    $other = CompanyHelper::company();
    scopedParentActor($other, PermissionType::GLOBAL);

    $move->save();

    expect($move->refresh()->warehouse_id)->toBe($before);
});
