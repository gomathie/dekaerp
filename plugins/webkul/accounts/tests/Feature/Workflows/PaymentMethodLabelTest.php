<?php

use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Payment;
use Webkul\Account\Models\PaymentMethod;
use Webkul\Account\Models\PaymentMethodLine;

/**
 * TRACE365, 2026-11-20: every cash, bank and Mobile Money payment read
 * "Manual Payment" on invoices, journal items and the payments list. The
 * generic manual method now shows the journal's name; any other method
 * keeps its own.
 */
function paymentWithMethod(string $code, string $lineName, string $journalName): Payment
{
    $method = new PaymentMethod(['code' => $code, 'name' => $lineName]);
    $line = new PaymentMethodLine(['name' => $lineName]);
    $line->setRelation('paymentMethod', $method);

    $payment = new Payment;
    $payment->setRelation('paymentMethodLine', $line);
    $payment->setRelation('paymentMethod', $method);
    $payment->setRelation('journal', new Journal(['name' => $journalName]));

    return $payment;
}

it('names the journal instead of "Manual Payment"', function (): void {
    expect(paymentWithMethod('manual', 'Manual Payment', 'Mobile Money')->methodLabel())->toBe('Mobile Money');
});

it('keeps the name of any other payment method', function (): void {
    expect(paymentWithMethod('sepa_ct', 'SEPA Credit Transfer', 'Bank')->methodLabel())->toBe('SEPA Credit Transfer');
});
