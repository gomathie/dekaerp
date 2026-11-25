<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Support\RecordedPayments;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\PaymentResource\Pages\ListPayments;
use Webkul\Accounting\Filament\Widgets\PaymentStatsWidget;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('accounting');
    PluginAccess::flush();
});

/** A payment written straight to the table: these tests are about what the cards read. */
function accountingCardPayment(Company $company, string $type, float $amount, string $date, string $state = 'paid'): void
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
 * Plan #9 R-9 (2026-11-25): Accounting's own payments list shows this month's
 * money received, read with RecordedPayments, the rule the Invoices list and
 * the Overview chart use: paid and in process count; a draft, last month,
 * money paid out and another client's payment never do.
 */
it('shows this month\'s money received above the accounting payments list', function (): void {
    $this->travelTo(Carbon::parse('2026-03-31 12:00'));

    $own = Company::factory()->create();
    $other = CompanyHelper::asNobody(fn () => Company::factory()->create());

    accountingCardPayment($own, 'inbound', 150, '2026-03-10');
    accountingCardPayment($own, 'inbound', 50, '2026-03-20', 'in_process');
    accountingCardPayment($own, 'inbound', 999, '2026-03-12', 'draft');
    accountingCardPayment($own, 'inbound', 70, '2026-02-15');
    accountingCardPayment($own, 'outbound', 40, '2026-03-11');
    accountingCardPayment($other, 'inbound', 9000, '2026-03-10');

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
        'received' => $this->aggregate(RecordedPayments::between(PaymentType::RECEIVE, ...$this->periodRange()), true),
        'count'    => $this->aggregate(RecordedPayments::between(PaymentType::RECEIVE, ...$this->periodRange()), false),
    ])->call($cards);

    $stats = (fn (): array => $this->getStats())->call($cards);

    expect($figures)->toBe(['received' => 200.0, 'count' => 2.0])
        ->and($stats)->toHaveCount(2)
        ->and($stats[1]->getValue())->toBe('2')
        ->and($cards->getHeading())->toBe(__('accounting::filament/widgets/payment-stats.this-month'))
        ->and((new PaymentStatsWidget)->getHeading())->toBe(__('accounting::filament/widgets/payment-stats.heading'));
});

/**
 * Only for someone who may open Accounting's payments list, with Accounting
 * on for the company and the money-figures switch on.
 */
it('shows the accounting payment cards only to people who may see the payments and money figures', function (): void {
    FilamentHelper::bootAdminPanel();

    $company = Company::factory()->create();

    CompanyHelper::actingAsCompanyUser($company, []);

    expect(PaymentStatsWidget::canView())->toBeFalse();

    $user = CompanyHelper::actingAsCompanyUser($company, ['view_any_accounting_payment']);

    expect(PaymentStatsWidget::canView())->toBeTrue();

    $user->forceFill(['can_see_money_figures' => false])->save();

    expect(PaymentStatsWidget::canView())->toBeFalse();

    $user->forceFill(['can_see_money_figures' => true])->save();

    DB::table('company_plugin')
        ->where('company_id', $company->getKey())
        ->where('plugin_id', Plugin::query()->where('name', 'accounting')->value('id'))
        ->delete();

    PluginAccess::flush();

    expect(PaymentStatsWidget::canView())->toBeFalse();
});
