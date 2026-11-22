<?php

use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Models\Move;
use Webkul\Contact\Filament\Resources\PartnerResource as ContactPartnerResource;
use Webkul\Contact\Filament\Resources\PartnerResource\Pages\ManageBills as ContactManageBills;
use Webkul\Contact\Filament\Resources\PartnerResource\Pages\ManageInvoices as ContactManageInvoices;
use Webkul\Contact\Filament\Resources\PartnerResource\Pages\ManagePurchases as ContactManagePurchases;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\VendorResource;
use Webkul\Purchase\Models\Order as PurchaseOrder;
use Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource as SaleCustomerResource;
use Webkul\Sale\Models\Partner as SalePartner;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('purchases');
    TestBootstrapHelper::ensurePluginInstalled('sales');
    TestBootstrapHelper::ensurePluginInstalled('contacts');
});

/**
 * Unified contacts, phase 3 (user, 2026-11-11): every contact screen shows
 * the Purchases and Bills tabs when Purchases is installed, and the Purchases
 * vendor screen gains bank accounts.
 */
it('adds purchases and bills to every contact screen and bank accounts to the purchases vendor', function (): void {
    expect(array_keys(ContactPartnerResource::getPages()))->toContain('purchases', 'bills')
        ->and(array_keys(SaleCustomerResource::getPages()))->toContain('purchases', 'bills')
        ->and(array_keys(VendorResource::getPages()))->toContain('bank-accounts', 'purchases', 'bills');
});

/**
 * The relation names are deliberately new. The Sales contact model already
 * has "orders" for sales orders, so reusing that name would have listed sales
 * orders under a Purchases tab on the customer screen.
 */
it('reads purchase orders and bills on a sales customer, not its sales orders', function (): void {
    $partner = new SalePartner;

    expect($partner->purchaseOrders())->toBeInstanceOf(HasMany::class)
        ->and($partner->purchaseOrders()->getRelated())->toBeInstanceOf(PurchaseOrder::class)
        ->and($partner->vendorBills()->getRelated())->toBeInstanceOf(Move::class);
});

/**
 * A user who may open a contact but not Purchases must not read that
 * contact's purchase orders or bills through the new tabs.
 */
/**
 * UF-028 (upstream dd55acdc3, 2026-11-22): the Bills tab lists bills and
 * vendor refunds only. It used to list every journal document of the
 * contact, customer invoices included.
 */
it('lists only bills and vendor refunds under the bills tab', function (): void {
    $partner = SalePartner::factory()->create();

    foreach ([MoveType::IN_INVOICE, MoveType::IN_REFUND, MoveType::OUT_INVOICE, MoveType::OUT_REFUND, MoveType::ENTRY] as $type) {
        Move::factory()->create(['partner_id' => $partner->getKey(), 'move_type' => $type]);
    }

    expect($partner->vendorBills()->pluck('move_type')->map(fn ($type) => $type instanceof MoveType ? $type->value : $type)->sort()->values()->all())
        ->toBe([MoveType::IN_INVOICE->value, MoveType::IN_REFUND->value]);
});

/**
 * The customer Invoices tab (user, 2026-11-22): on the contact and customer
 * screens, customer invoices and credit notes only, and hidden from a user
 * who may not list invoices.
 */
it('adds an invoices tab with customer invoices and credit notes only', function (): void {
    expect(array_keys(ContactPartnerResource::getPages()))->toContain('invoices')
        ->and(array_keys(SaleCustomerResource::getPages()))->toContain('invoices');

    $partner = SalePartner::factory()->create();

    foreach ([MoveType::IN_INVOICE, MoveType::IN_REFUND, MoveType::OUT_INVOICE, MoveType::OUT_REFUND, MoveType::ENTRY] as $type) {
        Move::factory()->create(['partner_id' => $partner->getKey(), 'move_type' => $type]);
    }

    expect($partner->customerInvoices()->pluck('move_type')->map(fn ($type) => $type instanceof MoveType ? $type->value : $type)->sort()->values()->all())
        ->toBe([MoveType::OUT_INVOICE->value, MoveType::OUT_REFUND->value]);

    FilamentHelper::actingAs([]);

    expect(ContactManageInvoices::canAccess())->toBeFalse();
});

it('hides the tabs from a user without purchases permissions', function (): void {
    FilamentHelper::actingAs([]);

    expect(ContactManagePurchases::canAccess())->toBeFalse()
        ->and(ContactManageBills::canAccess())->toBeFalse();
});
