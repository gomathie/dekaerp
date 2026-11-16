<?php

use Webkul\Account\Events\MovePaid;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Move;
use Webkul\Partner\Models\Partner;
use Webkul\Sale\Listeners\SendSMSNotificationListener;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Services\Msg91Service;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('sales');
});

it('copies a cross-company order partner into the address defaults during its model hook', function () {
    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    CompanyHelper::actingAsCompanyUser($companyA);

    $partner = Partner::factory()->create(['company_id' => $companyA->id]);

    CompanyHelper::actingAsCompanyUser($companyB);

    $order = Order::factory()->create([
        'company_id'           => $companyB->id,
        'partner_id'           => $partner->id,
        'partner_shipping_id'  => null,
        'partner_invoice_id'   => null,
    ]);

    expect($order->partner_shipping_id)->toBe($partner->id)
        ->and($order->partner_invoice_id)->toBe($partner->id);
});

it('notifies a cross-company order customer when a payment event has no company session', function () {
    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    CompanyHelper::actingAsCompanyUser($companyA);

    $partner = Partner::factory()->create([
        'company_id' => $companyA->id,
        'mobile'     => '+260970000000',
    ]);

    CompanyHelper::actingAsCompanyUser($companyB);

    $order = Order::factory()->create([
        'company_id'  => $companyB->id,
        'partner_id'  => $partner->id,
    ]);
    $journal = Journal::factory()->sale()->create([
        'company_id'  => $companyB->id,
        'currency_id' => $companyB->currency_id,
    ]);
    $move = Move::factory()->invoice()->posted()->create([
        'company_id'  => $companyB->id,
        'currency_id' => $companyB->currency_id,
        'journal_id'  => $journal->id,
    ]);
    $order->accountMoves()->attach($move->id);

    $sms = Mockery::mock(Msg91Service::class);
    $sms->shouldReceive('send')
        ->once()
        ->withArgs(fn (string $mobile, string $message): bool => $mobile === $partner->mobile && str_contains($message, $partner->name));

    app()->instance(Msg91Service::class, $sms);

    (new SendSMSNotificationListener($sms))->handle(new MovePaid($move));
});
