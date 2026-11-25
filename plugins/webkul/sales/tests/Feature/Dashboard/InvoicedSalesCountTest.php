<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Filament\Widgets\SaleHeadlineWidget;
use Webkul\Sale\Filament\Widgets\SaleStatsOverview;
use Webkul\Sale\Models\Order;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/SaleHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('sales');
    PluginAccess::flush();
});

/**
 * User decision, 2026-11-25 (TRACE365): the Sales tiles follow posted
 * invoices. Revenue was invoiced (DB-8) but the count beside it was
 * confirmed orders, so 12 orders with 10 invoiced read "12 sales" and the
 * average was invoiced revenue over 12. The count is now the invoiced sales
 * that make up the revenue, each sale once, and the average divides by it.
 *
 * Period 11 to 20 March 2026; the previous period is 1 to 10 March.
 *
 *   SO-A  confirmed, posted invoice 300            counts
 *   SO-B  confirmed, two posted invoices 100 + 100 counts once
 *   SO-C  confirmed, draft invoice 400             not invoiced: no
 *   SO-D  confirmed, no invoice                    not invoiced: no
 *   DIR   posted invoice without an order, 150     counts
 *   REF   posted credit note without an order, 50  lowers revenue, no count
 *   PREV  posted invoice without an order, 5 Mar   previous period: 1
 *
 * Revenue 600 (300 + 200 + 150 - 50) in 3 sales, average 200.
 */
function invoicedSalesFixture(): array
{
    $company = Company::factory()->create();
    $user = CompanyHelper::actingAsCompanyUser($company, []);
    $user->forceFill(['resource_permission' => 'global'])->saveQuietly();

    $order = function (string $date) use ($company, $user): Order {
        $order = SaleHelper::order(['company_id' => $company->getKey(), 'user_id' => $user->getKey()]);

        Order::withoutGlobalScopes()->whereKey($order->getKey())->update([
            'state'      => OrderState::SALE->value,
            'date_order' => $date.' 10:00:00',
        ]);

        return $order;
    };

    $move = fn (string $type, string $state, float $signed, string $date, ?Order $from = null): int => tap(DB::table('accounts_account_moves')->insertGetId([
        'company_id'          => $company->getKey(),
        'move_type'           => $type,
        'state'               => $state,
        'date'                => $date,
        'invoice_date'        => $date,
        'amount_total'        => abs($signed),
        'amount_total_signed' => $signed,
        // The test user's own rows: Move keeps its ownership scope.
        'creator_id'          => $user->getKey(),
        'created_at'          => now(),
        'updated_at'          => now(),
    ]), function (int $moveId) use ($from): void {
        if ($from !== null) {
            DB::table('sales_order_invoices')->insert(['order_id' => $from->getKey(), 'move_id' => $moveId]);
        }
    });

    $soA = $order('2026-03-11');
    $soB = $order('2026-03-12');
    $soC = $order('2026-03-13');
    $order('2026-03-14');

    $move('out_invoice', 'posted', 300, '2026-03-15', $soA);
    $move('out_invoice', 'posted', 100, '2026-03-16', $soB);
    $move('out_invoice', 'posted', 100, '2026-03-17', $soB);
    $move('out_invoice', 'draft', 400, '2026-03-18', $soC);
    $move('out_invoice', 'posted', 150, '2026-03-18');
    $move('out_refund', 'posted', -50, '2026-03-19');
    $move('out_invoice', 'posted', 80, '2026-03-05');

    return ['startDate' => '2026-03-11', 'endDate' => '2026-03-20'];
}

it('counts invoiced sales, each once, and averages revenue over them', function (): void {
    $this->travelTo(Carbon::parse('2026-03-20 12:00'));

    $filters = invoicedSalesFixture();

    $widget = new SaleStatsOverview;
    $widget->pageFilters = $filters;

    $figures = (fn (): array => [
        'revenue'  => $this->invoicedRevenue(),
        'sales'    => $this->salesCount(),
        'previous' => $this->salesCount(...$this->previousPeriodRange()),
        'orders'   => $this->saleOrders()->count(),
    ])->call($widget);

    // Four confirmed orders, three invoiced sales: the tiles follow invoices.
    expect($figures)->toBe(['revenue' => 600.0, 'sales' => 3, 'previous' => 1, 'orders' => 4]);

    $stats = (fn (): array => $this->getStats())->call($widget);

    expect((string) $stats[1]->getValue())->toBe('3')
        ->and((string) $stats[2]->getValue())->toBe(compact_money(600.0, current_company()?->currency?->name))
        ->and((string) $stats[3]->getValue())->toBe(compact_money(200.0, current_company()?->currency?->name));
});

it('pairs the sales headline and the overview sales panel with the invoiced count', function (): void {
    $this->travelTo(Carbon::parse('2026-03-20 12:00'));

    $filters = invoicedSalesFixture();

    $headline = (new SaleHeadlineWidget)->headlineStats($filters);
    $caption = html_entity_decode(strip_tags((string) $headline[0]->getDescription()));

    $panel = (new SaleHeadlineWidget)->panel($filters);

    expect($caption)->toContain(trans_choice('invoices::filament/pages/overview.headline.orders', 3, ['count' => 3]))
        ->and($panel['metrics'][1]['value'])->toBe('3');
});
