<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Models\MoveLine;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/AccountHelper.php';

/**
 * Accounting plan #7, package A1: known figures, end to end.
 *
 * The brief's Ghana example (VAT Act 2025, Act 1151: VAT 15%, NHIL 2.5% and
 * GETFund 2.5%, all on the same taxable value): GHS 1,000 excluding tax is
 * invoiced at GHS 1,200, paid 500 then 700. Each figure is asserted on the
 * ledger itself, so a report built from posted lines starts from proven
 * numbers. The tax rates here are test data, not a statement of compliance.
 */
beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');

    DB::table('plugins')->updateOrInsert(
        ['name' => 'accounts'],
        ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
    );

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    AccountHelper::actingAsAdmin();

    $this->income = AccountHelper::account('income');
    $this->partner = AccountHelper::partner();
});

/**
 * Debit and credit of every posted line in the company, i.e. the trial
 * balance totals.
 *
 * @return array{debit: float, credit: float}
 */
function knownFigurePostedTotals(): array
{
    $lines = MoveLine::query()
        ->withoutGlobalScopes()
        ->where('company_id', AccountHelper::company()->id)
        ->where('parent_state', MoveState::POSTED)
        ->get(['debit', 'credit']);

    return [
        'debit'  => round((float) $lines->sum('debit'), 2),
        'credit' => round((float) $lines->sum('credit'), 2),
    ];
}

function knownFigureGhanaTaxes(TypeTaxUse $use = TypeTaxUse::SALE): array
{
    return [
        'vat'     => AccountHelper::taxWithAccounts(15, type: $use),
        'nhil'    => AccountHelper::taxWithAccounts(2.5, type: $use),
        'getfund' => AccountHelper::taxWithAccounts(2.5, type: $use),
    ];
}

it('invoices GHS 1,000 with VAT, NHIL and GETFund at GHS 1,200 with each tax on its own account', function () {
    $taxes = knownFigureGhanaTaxes();
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($invoice, $this->income, qty: 1, priceUnit: 1000, taxes: array_values($taxes));

    AccountHelper::post($invoice);
    $invoice->refresh();

    $taxLines = $invoice->lines->where('display_type', DisplayType::TAX);
    $byAccount = $taxLines->groupBy('account_id')->map(fn ($lines) => round((float) $lines->sum('credit'), 2))->sort()->values()->all();
    $receivable = $invoice->lines->firstWhere('display_type', DisplayType::PAYMENT_TERM);
    $revenue = $invoice->lines->firstWhere('display_type', DisplayType::PRODUCT);

    expect((float) $invoice->amount_untaxed)->toBe(1000.0)
        ->and((float) $invoice->amount_tax)->toBe(200.0)
        ->and((float) $invoice->amount_total)->toBe(1200.0)
        ->and($byAccount)->toBe([25.0, 25.0, 150.0])
        ->and((float) $revenue->credit)->toBe(1000.0)
        ->and($receivable->account->account_type)->toBe(AccountType::ASSET_RECEIVABLE)
        ->and((float) $receivable->debit)->toBe(1200.0);
});

it('settles the GHS 1,200 invoice in two payments and leaves the ledger balanced', function () {
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($invoice, $this->income, qty: 1, priceUnit: 1000, taxes: array_values(knownFigureGhanaTaxes()));
    AccountHelper::post($invoice);

    AccountHelper::pay($invoice, 500);
    $invoice->refresh();

    expect($invoice->payment_state)->toBe(PaymentState::PARTIAL)
        ->and((float) abs($invoice->amount_residual))->toBe(700.0);

    AccountHelper::pay($invoice, 700);
    $invoice->refresh();

    $receivable = $invoice->lines->firstWhere('display_type', DisplayType::PAYMENT_TERM);
    $partnerReceivable = MoveLine::query()->withoutGlobalScopes()
        ->where('partner_id', $this->partner->id)
        ->where('account_id', $receivable->account_id)
        ->where('parent_state', MoveState::POSTED)
        ->get();
    $totals = knownFigurePostedTotals();

    // Paid in full: nothing open on the invoice or on the customer's
    // receivable, which is what the partner ledger and aged receivable read.
    expect(in_array($invoice->payment_state, [PaymentState::PAID, PaymentState::IN_PAYMENT], true))->toBeTrue()
        ->and((float) $invoice->amount_residual)->toBe(0.0)
        ->and(round((float) $partnerReceivable->sum('debit') - (float) $partnerReceivable->sum('credit'), 2))->toBe(0.0)
        ->and($totals['debit'])->toBe($totals['credit']);
});

it('records a GHS 1,000 supplier bill with the same taxes as GHS 1,200 owed', function () {
    $expense = AccountHelper::account('expense');
    $bill = AccountHelper::invoice(MoveType::IN_INVOICE, $this->partner);
    AccountHelper::productLine($bill, $expense, qty: 1, priceUnit: 1000, taxes: array_values(knownFigureGhanaTaxes(TypeTaxUse::PURCHASE)));

    AccountHelper::post($bill);
    $bill->refresh();

    $payable = $bill->lines->firstWhere('display_type', DisplayType::PAYMENT_TERM);
    $totals = knownFigurePostedTotals();

    expect((float) $bill->amount_total)->toBe(1200.0)
        ->and($payable->account->account_type)->toBe(AccountType::LIABILITY_PAYABLE)
        ->and((float) $payable->credit)->toBe(1200.0)
        ->and(round((float) $bill->lines->where('display_type', DisplayType::TAX)->sum('debit'), 2))->toBe(200.0)
        ->and($totals['debit'])->toBe($totals['credit']);
});

it('reduces the open amount by a GHS 240 credit note against the GHS 1,200 invoice', function () {
    $taxes = array_values(knownFigureGhanaTaxes());
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($invoice, $this->income, qty: 1, priceUnit: 1000, taxes: $taxes);
    AccountHelper::post($invoice);

    $credit = AccountHelper::invoice(MoveType::OUT_REFUND, $this->partner);
    AccountHelper::productLine($credit, $this->income, qty: 1, priceUnit: 200, taxes: $taxes);
    AccountHelper::post($credit);
    AccountHelper::reconcile($invoice, $credit);

    expect((float) $credit->refresh()->amount_total)->toBe(240.0)
        ->and((float) abs($invoice->refresh()->amount_residual))->toBe(960.0)
        ->and(knownFigurePostedTotals()['debit'])->toBe(knownFigurePostedTotals()['credit']);
});
