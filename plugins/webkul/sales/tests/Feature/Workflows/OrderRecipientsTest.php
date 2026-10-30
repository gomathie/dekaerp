<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Webkul\Partner\Models\Partner;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Services\OrderMailer;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

/**
 * A sales order is never emailed to another company's contact (user decision,
 * 2026-10-30; §4k read audit). OrderMailer reads its recipients through
 * Partner::recipientsForCompany(), whose own cases are pinned in the partners
 * plugin's RecipientsForCompanyTest. This proves the mailer uses it: refused
 * before anything is sent.
 */
beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('sales');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('refuses to email a sales order to another company\'s contact and sends nothing', function () {
    Mail::fake();

    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    CompanyHelper::actingAsCompanyUser($companyB);
    $foreign = Partner::factory()->create(['email' => 'buyer@other-client.test']);

    // A user who sees both companies, so the old scoped read would have found
    // the contact and sent the order to it.
    CompanyHelper::actingAsCompanyUser([$companyA, $companyB], activeIds: [$companyA->id, $companyB->id]);

    // The refusal comes before the order is used, so an unsaved order that
    // carries its company is enough, and avoids a full sales-order setup.
    $order = (new Order)->forceFill(['company_id' => $companyA->id, 'name' => 'SO-RECIPIENTS']);

    expect(fn () => app(OrderMailer::class)->sendCancellation($order, [
        'partners'    => [$foreign->id],
        'subject'     => 'Cancelled',
        'description' => 'Cancelled',
    ]))->toThrow(
        ValidationException::class,
        "These recipients do not belong to this company and cannot be emailed: {$foreign->id}.",
    );

    Mail::assertNothingSent();
});
