<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Webkul\Account\Enums\PaymentType;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\PaymentResource\Pages\ListPayments;
use Webkul\Invoice\Filament\Widgets\OverviewCashFlowChart;
use Webkul\Invoice\Filament\Widgets\PaymentStatsWidget;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('invoices');
    PluginAccess::flush();
});

/**
 * A payment written straight to the table, as the Overview chart's test
 * does: these tests are about what the cards read, not how a payment posts.
 */
function listCardPayment(Company $company, string $type, float $amount, string $date, string $state = 'paid'): void
{
    DB::table('accounts_account_payments')->insert([
        'company_id'                     => $company->getKey(),
        'payment_type'                   => $type,
        'state'                          => $state,
        'date'                           => $date,
        'amount'                         => $amount,
        'amount_company_currency_signed' => $type === 'inbound' ? $amount : -$amount,
        'created_at'                     => now(),
        'updated_at'                     => now(),
    ]);
}

/**
 * Plan #10 R-9 (2026-11-24): this month's money received above the
 * payments received list, read with the Overview chart's own rule, so on
 * the last day of March "Received" is the chart's March "Money in": paid
 * and in process count; a draft, February, money paid out and the other
 * client's payment never do.
 */
it('shows this month\'s money received above the payments list, the same as the dashboard', function (): void {
    $this->travelTo(Carbon::parse('2026-03-31 12:00'));

    $own = Company::factory()->create();
    $other = CompanyHelper::asNobody(fn () => Company::factory()->create());

    listCardPayment($own, 'inbound', 150, '2026-03-10');
    listCardPayment($own, 'inbound', 50, '2026-03-20', 'in_process');
    listCardPayment($own, 'inbound', 999, '2026-03-12', 'draft');
    listCardPayment($own, 'inbound', 70, '2026-02-15');
    listCardPayment($own, 'outbound', 40, '2026-03-11');
    listCardPayment($other, 'inbound', 9000, '2026-03-10');

    CompanyHelper::actingAsCompanyUser($own, []);

    $widgets = (new ListPayments)->getHeaderWidgets();

    expect($widgets)->toHaveCount(1)
        ->and($widgets[0]->widget)->toBe(PaymentStatsWidget::class)
        ->and($widgets[0]->getProperties()['pageFilters'])->toBe(['start_date' => '2026-03-01', 'end_date' => '2026-03-31']);

    $cards = new PaymentStatsWidget;

    foreach ($widgets[0]->getProperties() as $property => $value) {
        $cards->{$property} = $value;
    }

    $figures = (fn (): array => [
        'received' => $this->aggregate(OverviewCashFlowChart::recordedPayments(PaymentType::RECEIVE, ...$this->periodRange()), true),
        'count'    => $this->aggregate(OverviewCashFlowChart::recordedPayments(PaymentType::RECEIVE, ...$this->periodRange()), false),
    ])->call($cards);

    $stats = (fn (): array => $this->getStats())->call($cards);

    $chart = new OverviewCashFlowChart;
    $chart->pageFilters = $widgets[0]->getProperties()['pageFilters'];

    $moneyIn = (fn (): float => array_sum($this->dailyPayments(PaymentType::RECEIVE, ...$this->periodRange())))->call($chart);

    expect($figures)->toBe(['received' => 200.0, 'count' => 2.0])
        ->and($moneyIn)->toBe($figures['received'])
        ->and($stats)->toHaveCount(2)
        ->and($stats[0]->getExtraAttributes()['title'])->toBe(money(200.0, current_company()?->currency?->name))
        ->and($stats[1]->getValue())->toBe('2')
        ->and($cards->getHeading())->toBe(__('invoices::filament/widgets/invoice-dashboard.invoice-stats.this-month'))
        ->and((new PaymentStatsWidget)->getHeading())->toBe(__('invoices::filament/widgets/invoice-dashboard.payment-stats.heading'));
});

/**
 * The cards show payment amounts, so only to someone who may open the list
 * (the payments permission, with Invoices on for the company) and has the
 * money-figures switch on. The widget is reachable through Livewire alone.
 */
it('shows the payment cards only to people who may see the payments and money figures', function (): void {
    FilamentHelper::bootAdminPanel();

    $company = Company::factory()->create();

    CompanyHelper::actingAsCompanyUser($company, []);

    expect(PaymentStatsWidget::canView())->toBeFalse();

    $user = CompanyHelper::actingAsCompanyUser($company, ['view_any_invoice_payment']);

    expect(PaymentStatsWidget::canView())->toBeTrue();

    $user->forceFill(['can_see_money_figures' => false])->save();

    expect(PaymentStatsWidget::canView())->toBeFalse();

    $user->forceFill(['can_see_money_figures' => true])->save();

    DB::table('company_plugin')
        ->where('company_id', $company->getKey())
        ->where('plugin_id', Plugin::query()->where('name', 'invoices')->value('id'))
        ->delete();

    PluginAccess::flush();

    expect(PaymentStatsWidget::canView())->toBeFalse();
});
