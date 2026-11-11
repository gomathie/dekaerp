<?php

use Illuminate\Support\Facades\Event;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Models\Move;
use Webkul\Inventory\Models\Delivery;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Models\OperationType;
use Webkul\Inventory\Models\Product;
use Webkul\Inventory\Models\Receipt;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsInventoryReceiptApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function receiptRoute(string $action, mixed $receipt = null): string
{
    $name = "admin.api.v1.inventories.receipts.{$action}";

    return $receipt ? route($name, $receipt) : route($name);
}

function receiptPayload(array $overrides = []): array
{
    $product = Product::factory()->create();
    $operationType = OperationType::factory()->receipt()->create();

    return array_replace_recursive([
        'operation_type_id' => $operationType->id,
        'moves'             => [[
            'product_id'      => $product->id,
            'product_uom_qty' => 1,
            'uom_id'          => $product->uom_id,
        ]],
    ], $overrides);
}

function createReceiptRecord(array $overrides = []): Receipt
{
    $operation = Operation::factory()->receipt()->create($overrides);

    return Receipt::query()->findOrFail($operation->id);
}

it('requires authentication to list receipts', function () {
    $this->getJson(receiptRoute('index'))
        ->assertUnauthorized();
});

it('requires authentication to create a receipt', function () {
    $this->postJson(receiptRoute('store'), [])
        ->assertUnauthorized();
});

it('requires authentication to show a receipt', function () {
    $receipt = createReceiptRecord();

    $this->getJson(receiptRoute('show', $receipt->id))
        ->assertUnauthorized();
});

it('forbids creating a receipt without permission', function () {
    actingAsInventoryReceiptApiUser();

    $this->postJson(receiptRoute('store'), receiptPayload())
        ->assertForbidden();
});

it('forbids showing a receipt without permission', function () {
    actingAsInventoryReceiptApiUser();

    $receipt = createReceiptRecord();

    $this->getJson(receiptRoute('show', $receipt->id))
        ->assertForbidden();
});

it('forbids listing receipts without permission', function () {
    actingAsInventoryReceiptApiUser();

    $this->getJson(receiptRoute('index'))
        ->assertForbidden();
});

it('lists receipts for authorized users', function () {
    actingAsInventoryReceiptApiUser(['view_any_inventory_receipt']);

    $receipt = createReceiptRecord();

    $response = $this->getJson(receiptRoute('index'))
        ->assertOk();

    expect(collect($response->json('data'))->pluck('id'))->toContain($receipt->id);
});

it('creates a receipt with valid payload', function () {
    actingAsInventoryReceiptApiUser(['create_inventory_receipt']);

    $response = $this->postJson(receiptRoute('store'), receiptPayload())
        ->assertCreated()
        ->assertJsonPath('message', 'Receipt created successfully.');

    expect(Receipt::query()->whereKey($response->json('data.id'))->exists())->toBeTrue();
});

/**
 * Removed moves used to go by bulk query, which fires no model events: the
 * read-only guard never saw a payload that only removed moves, and Move's
 * own deleting hook (which removes its lines) did not run (2026-11-10).
 */
it('removes a dropped move through the model so its delete events run', function () {
    actingAsInventoryReceiptApiUser(['create_inventory_receipt', 'update_inventory_receipt', 'view_inventory_receipt']);

    $payload = receiptPayload();
    $second = Product::factory()->create();
    $payload['moves'][] = ['product_id' => $second->id, 'product_uom_qty' => 1, 'uom_id' => $second->uom_id];

    $created = $this->postJson(receiptRoute('store'), $payload)->assertCreated();
    $receiptId = $created->json('data.id');
    $moves = Operation::query()->findOrFail($receiptId)->moves()->orderBy('id')->get();
    expect($moves)->toHaveCount(2);

    $deletingEvents = 0;
    Event::listen('eloquent.deleting: '.Move::class, function () use (&$deletingEvents): void {
        $deletingEvents++;
    });

    $kept = $moves->first();

    $this->putJson(receiptRoute('update', $receiptId), [
        'moves' => [[
            'id'              => $kept->id,
            'product_id'      => $kept->product_id,
            'product_uom_qty' => 1,
            'uom_id'          => $kept->uom_id,
        ]],
    ])->assertOk();

    expect($deletingEvents)->toBe(1)
        ->and(Move::query()->whereKey($moves->last()->id)->exists())->toBeFalse()
        ->and(Move::query()->whereKey($kept->id)->exists())->toBeTrue();
});

it('shows a receipt for authorized users', function () {
    actingAsInventoryReceiptApiUser(['view_inventory_receipt']);

    $receipt = createReceiptRecord();

    $this->getJson(receiptRoute('show', $receipt->id))
        ->assertOk()
        ->assertJsonPath('data.id', $receipt->id);
});

it('returns 404 for a non-existent receipt', function () {
    actingAsInventoryReceiptApiUser(['view_inventory_receipt']);

    $this->getJson(receiptRoute('show', 999999))
        ->assertNotFound();
});

it('validates required move product_id when creating a receipt', function () {
    actingAsInventoryReceiptApiUser(['create_inventory_receipt']);

    $payload = receiptPayload([
        'moves' => [[
            'product_id'      => null,
            'product_uom_qty' => 1,
        ]],
    ]);

    $this->postJson(receiptRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['moves.0.product_id']);
});

it('returns 404 when showing a non-receipt operation id', function () {
    actingAsInventoryReceiptApiUser(['view_inventory_receipt']);

    $delivery = Delivery::query()->findOrFail(Operation::factory()->delivery()->create()->id);

    $this->getJson(receiptRoute('show', $delivery->id))
        ->assertNotFound();
});

it('rejects check availability when receipt is not confirmed or assigned', function () {
    actingAsInventoryReceiptApiUser(['update_inventory_receipt']);

    $receipt = createReceiptRecord();

    $this->postJson(receiptRoute('check-availability', $receipt->id))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only waiting, confirmed or assigned operations can check availability.');
});

it('forbids check availability without update permission', function () {
    actingAsInventoryReceiptApiUser();

    $receipt = createReceiptRecord();

    $this->postJson(receiptRoute('check-availability', $receipt->id))
        ->assertForbidden();
});

it('returns todo validation error when receipt has no moves', function () {
    actingAsInventoryReceiptApiUser(['update_inventory_receipt']);

    $receipt = createReceiptRecord();

    $this->postJson(receiptRoute('todo', $receipt->id))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Cannot set operation to todo without moves.');
});

it('returns validate validation error for done receipt', function () {
    actingAsInventoryReceiptApiUser(['update_inventory_receipt']);

    $receipt = createReceiptRecord(['state' => OperationState::DONE]);

    $this->postJson(receiptRoute('validate', $receipt->id))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only non-done and non-canceled operations can be validated.');
});

it('returns cancel validation error for canceled receipt', function () {
    actingAsInventoryReceiptApiUser(['update_inventory_receipt']);

    $receipt = createReceiptRecord(['state' => OperationState::CANCELED]);

    $this->postJson(receiptRoute('cancel', $receipt->id))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only non-done and non-canceled operations can be canceled.');
});

it('returns return validation error when receipt is not done', function () {
    actingAsInventoryReceiptApiUser(['update_inventory_receipt']);

    $receipt = createReceiptRecord(['state' => OperationState::DRAFT]);

    $this->postJson(receiptRoute('return', $receipt->id))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only done operations can be returned.');
});
