<?php

use Illuminate\Support\Facades\DB;
use Webkul\Partner\Models\Partner;
use Webkul\Sale\Models\Order;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensureERPInstalled();
    TestBootstrapHelper::ensurePluginInstalled('sales');
});

function runAssignSharedContactsMigration(): void
{
    (require __DIR__.'/../../../database/migrations/2026_11_10_000001_assign_shared_contacts_to_a_company.php')->up();
}

function makeSharedContact(string $name): Partner
{
    $partner = Partner::factory()->create(['name' => $name]);
    DB::table('partners_partners')->where('id', $partner->getKey())->update(['company_id' => null]);

    return $partner;
}

/**
 * Shared contacts appeared in every company (user, 2026-11-10). One that a
 * company's documents use must go to that company, or its own order would
 * lose its customer; an unused one goes to the default company.
 */
it('moves a shared contact to the one company that uses it and the rest to the default company', function () {
    $defaultCompanyId = (int) DB::table('companies')->whereNull('deleted_at')->min('id');
    $other = Company::factory()->create();

    $used = makeSharedContact('Used By Other Co');
    $unused = makeSharedContact('Unused Demo Contact');

    $order = Order::factory()->create(['company_id' => $other->getKey()]);
    DB::table('sales_orders')->where('id', $order->getKey())->update(['partner_id' => $used->getKey()]);

    runAssignSharedContactsMigration();

    expect(DB::table('partners_partners')->where('id', $used->getKey())->value('company_id'))->toBe($other->getKey())
        ->and(DB::table('partners_partners')->where('id', $unused->getKey())->value('company_id'))->toBe($defaultCompanyId);
});

it('leaves a contact shared when two companies use it', function () {
    [$first, $second] = Company::factory()->count(2)->create();
    $contested = makeSharedContact('Contested Contact');

    foreach ([$first, $second] as $company) {
        $order = Order::factory()->create(['company_id' => $company->getKey()]);
        DB::table('sales_orders')->where('id', $order->getKey())->update(['partner_id' => $contested->getKey()]);
    }

    runAssignSharedContactsMigration();

    expect(DB::table('partners_partners')->where('id', $contested->getKey())->value('company_id'))->toBeNull();
});
