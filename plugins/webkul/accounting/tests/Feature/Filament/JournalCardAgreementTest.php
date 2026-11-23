<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Move;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\InvoiceResource\Pages\ListInvoices;
use Webkul\Accounting\Filament\Widgets\JournalChartWidget;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../accounts/tests/Helpers/AccountHelper.php';

/**
 * Dashboard plan DB-2 (2026-11-23): on the Accounting dashboard, each
 * journal card's "Late" and "To Pay" figures agree with its due-date chart
 * and with the list "Late" opens. Before this, the chart added a credit note
 * as if it were owed (abs() per document) while "To Pay" deducted it, and
 * "Late" and its list left out partly paid invoices that the chart's Overdue
 * bar included.
 */
beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounting');

    DB::table('plugins')->whereIn('name', ['accounts', 'accounting'])->update([
        'is_installed' => true,
        'is_active'    => true,
        'updated_at'   => now(),
    ]);

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    AccountHelper::actingAsAdmin();
});

/**
 * A posted document with stored amounts, signed as real ones are: a
 * customer invoice and a vendor refund positive, a credit note and a vendor
 * bill negative.
 */
function journalCardMove(Journal $journal, MoveType $type, float $total, float $residual, PaymentState $paymentState, int $dueInDays): Move
{
    $move = AccountHelper::invoice($type, null, $journal);

    $sign = in_array($type, [MoveType::OUT_REFUND, MoveType::IN_INVOICE]) ? -1 : 1;

    Move::withoutGlobalScopes()->whereKey($move->getKey())->update([
        'state'                  => MoveState::POSTED,
        'payment_state'          => $paymentState,
        'invoice_date_due'       => now()->addDays($dueInDays)->toDateString(),
        'amount_total'           => $total,
        'amount_residual'        => $residual,
        'amount_total_signed'    => $sign * $total,
        'amount_residual_signed' => $sign * $residual,
    ]);

    return $move;
}

/**
 * @return array{stats: array<string, array<string, mixed>>, overdue: float, chart: float}
 */
function journalCard(Journal $journal): array
{
    $widget = new JournalChartWidget;
    $widget->journal = $journal;

    $datasets = $widget->getChartData()['datasets'];

    return [
        'stats'   => $widget->getDashboardData()['stats'],
        'overdue' => (float) array_sum($datasets[0]['data']),
        'chart'   => (float) array_sum($datasets[0]['data']) + (float) array_sum($datasets[1]['data']),
    ];
}

it('agrees on what customers owe in the sales journal card, its chart and its late list', function () {
    $journal = AccountHelper::saleJournal();

    journalCardMove($journal, MoveType::OUT_INVOICE, 1000, 1000, PaymentState::NOT_PAID, -10);
    $partlyPaid = journalCardMove($journal, MoveType::OUT_INVOICE, 600, 400, PaymentState::PARTIAL, -5);
    journalCardMove($journal, MoveType::OUT_REFUND, 200, 200, PaymentState::NOT_PAID, -3);
    journalCardMove($journal, MoveType::OUT_INVOICE, 500, 500, PaymentState::NOT_PAID, 20);
    journalCardMove($journal, MoveType::OUT_INVOICE, 300, 0, PaymentState::PAID, -8);

    $card = journalCard($journal);

    // Owed: 1,000 + 400 + 500 - 200; late: 1,000 + 400 - 200.
    expect((float) $card['stats']['to_pay']['amount'])->toBe(1700.0)
        ->and($card['stats']['to_pay']['value'])->toBe(4)
        ->and($card['chart'])->toBe(1700.0)
        ->and((float) $card['stats']['late']['amount'])->toBe(1200.0)
        ->and($card['stats']['late']['value'])->toBe(3)
        ->and($card['overdue'])->toBe(1200.0);

    // The list "Late" opens shows the same documents, the partly paid one too.
    $late = (new ListInvoices)->getPresetTableViews()['overdue']
        ->modifyQuery(Move::query()->where('journal_id', $journal->getKey()))
        ->pluck('id');

    expect($late)->toHaveCount($card['stats']['late']['value'])
        ->and($late)->toContain($partlyPaid->getKey());
});

it('agrees on what the company owes in the purchase journal card and its chart', function () {
    $journal = AccountHelper::purchaseJournal();

    journalCardMove($journal, MoveType::IN_INVOICE, 800, 800, PaymentState::NOT_PAID, -10);
    journalCardMove($journal, MoveType::IN_REFUND, 100, 100, PaymentState::NOT_PAID, -2);
    journalCardMove($journal, MoveType::IN_INVOICE, 300, 300, PaymentState::NOT_PAID, 20);

    $card = journalCard($journal);

    // Owed: 800 - 100 + 300; late: 800 - 100. Shown positive, as owed.
    expect((float) $card['stats']['to_pay']['amount'])->toBe(1000.0)
        ->and($card['chart'])->toBe(1000.0)
        ->and((float) $card['stats']['late']['amount'])->toBe(700.0)
        ->and($card['overdue'])->toBe(700.0);
});
