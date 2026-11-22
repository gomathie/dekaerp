<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Models\Move;
use Webkul\Accounting\Filament\Widgets\JournalChartWidget;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../accounts/tests/Helpers/AccountHelper.php';

/**
 * Dashboard plan DB-7 / D-DB-7 (2026-11-22): the journal cards add amounts
 * in the company's own currency. They used to add each document's own
 * currency, so a USD invoice of 100 and a GHS invoice of 50 showed "150".
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

/** Stored figures written directly: this pins which column is summed. */
function journalChartInvoice($journal, float $ownCurrency, float $companyCurrency, array $state): Move
{
    $move = AccountHelper::invoice(MoveType::OUT_INVOICE, null, $journal);

    Move::withoutGlobalScopes()->whereKey($move->getKey())->update(array_merge([
        'amount_total'           => $ownCurrency,
        'amount_residual'        => $ownCurrency,
        'amount_total_signed'    => $companyCurrency,
        'amount_residual_signed' => $companyCurrency,
    ], $state));

    return $move;
}

it('adds draft and unpaid amounts in the company currency, not each document\'s', function () {
    $journal = AccountHelper::saleJournal();

    // 100 USD at 15.5 = 1,550 in the company's currency, plus 50 local.
    journalChartInvoice($journal, 100, 1550, ['state' => MoveState::DRAFT]);
    journalChartInvoice($journal, 50, 50, ['state' => MoveState::DRAFT]);

    $overdue = ['state' => MoveState::POSTED, 'payment_state' => PaymentState::NOT_PAID, 'invoice_date_due' => now()->subDays(3)->toDateString()];
    journalChartInvoice($journal, 100, 1550, $overdue);
    journalChartInvoice($journal, 50, 50, $overdue);

    $widget = new JournalChartWidget;
    $widget->journal = $journal;

    $stats = $widget->getDashboardData()['stats'];

    expect((float) $stats['to_validate']['amount'])->toBe(1600.0)
        ->and((float) $stats['late']['amount'])->toBe(1600.0)
        ->and((float) $stats['to_pay']['amount'])->toBe(1600.0)
        ->and(array_sum($widget->getChartData()['datasets'][0]['data']))->toBe(1600.0);
});
