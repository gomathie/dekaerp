<?php

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Illuminate\Support\Facades\DB;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Models\Move;
use Webkul\Invoice\Filament\Pages\Invoices;
use Webkul\Invoice\Filament\Widgets\InvoiceHeadlineWidget;
use Webkul\Invoice\Filament\Widgets\InvoiceStatsWidget;
use Webkul\Invoice\Filament\Widgets\RevenueOverTimeWidget;
use Webkul\Invoice\Models\Invoice;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/DashboardWidgetGuardHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('invoices');
    PluginAccess::flush();
});

function revokeInvoicesFor(Company $company): void
{
    $plugin = Plugin::query()->where('name', 'invoices')->firstOrFail();

    DB::table('company_plugin')
        ->where('company_id', $company->getKey())
        ->where('plugin_id', $plugin->getKey())
        ->delete();

    PluginAccess::flush();
}

/**
 * A posted customer invoice with fixed totals. The amounts are written
 * straight to the row: MoveLine hooks recompute totals from lines, and these
 * tests are about what the dashboard reads, not how a move is costed.
 */
function postedInvoice(Company $company, float $amount, string $date, string $paymentState = 'paid'): Move
{
    $move = Move::factory()->create(['company_id' => $company->getKey()]);

    Move::withoutGlobalScopes()->whereKey($move->getKey())->update([
        'company_id'          => $company->getKey(),
        'move_type'           => MoveType::OUT_INVOICE,
        'state'               => MoveState::POSTED,
        'payment_state'       => $paymentState,
        'invoice_date'        => $date,
        'amount_total'        => $amount,
        'amount_total_signed' => $amount,
        'creator_id'          => auth()->id(),
    ]);

    return $move;
}

/**
 * @param  array<string, mixed>  $filters
 */
function invoiceWidget(string $class, array $filters): object
{
    $widget = new $class;

    $widget->pageFilters = $filters;

    return $widget;
}

it('generates the permission name the invoice dashboard checks', function (): void {
    // Every other test grants the name by hand, so a mismatch with what Shield
    // generates would pass here and leave the page unopenable in production.
    FilamentHelper::bootAdminPanel();

    $generated = collect(FilamentShield::getPages())
        ->flatMap(fn (array $page): array => array_keys($page['permissions']))
        ->all();

    expect($generated)->toContain('page_invoice_invoices');
});

it('opens the invoice dashboard only with the permission and the Invoices module', function (): void {
    $company = Company::factory()->create();

    CompanyHelper::actingAsCompanyUser($company, []);

    expect(Invoices::canAccess())->toBeFalse();

    CompanyHelper::actingAsCompanyUser($company, ['page_invoice_invoices']);

    expect(Invoices::canAccess())->toBeTrue();

    revokeInvoicesFor($company);

    expect(Invoices::canAccess())->toBeFalse();
});

it('hides every invoice widget once Invoices is revoked for the company', function (): void {
    FilamentHelper::bootAdminPanel();

    $company = Company::factory()->create();

    $widgetPermission = array_key_first(FilamentShield::getWidgets()[InvoiceStatsWidget::class]['permissions'] ?? []);

    expect($widgetPermission)->not->toBeNull();

    CompanyHelper::actingAsCompanyUser($company, [$widgetPermission]);

    expect(InvoiceStatsWidget::canView())->toBeTrue();

    revokeInvoicesFor($company);

    expect(InvoiceStatsWidget::canView())->toBeFalse();
});

it('counts and sums only the current company’s invoices', function (): void {
    $company = Company::factory()->create();
    $otherCompany = Company::factory()->create();

    CompanyHelper::actingAsCompanyUser($company, []);

    postedInvoice($company, 100, '2026-03-02');
    postedInvoice($company, 50, '2026-03-03', 'not_paid');
    // Another client's invoice is set up with nobody acting: the cross-company
    // write guard (UF-025) refuses this user creating it, as it should.
    CompanyHelper::asNobody(fn () => postedInvoice($otherCompany, 900, '2026-03-02'));

    $widget = invoiceWidget(InvoiceStatsWidget::class, ['start_date' => '2026-03-01', 'end_date' => '2026-03-31']);

    $query = (fn () => $this->customerInvoices())->call($widget);

    expect((float) $query->sum('amount_total_signed'))->toBe(150.0)
        ->and(Invoice::query()->whereKey($query->pluck('id'))->count())->toBe(2);
});

/**
 * Dashboard plan DB-2 (2026-11-22): the invoiced total is invoices less
 * credit notes (DB-8's revenue rule), and "Unpaid" includes partly paid
 * invoices, which used to fall in neither paid nor unpaid.
 */
it('deducts credit notes from the invoiced total and counts partly paid as unpaid', function (): void {
    $company = Company::factory()->create();
    CompanyHelper::actingAsCompanyUser($company, []);

    postedInvoice($company, 100, '2026-03-02');
    postedInvoice($company, 60, '2026-03-03', 'partial');
    postedInvoice($company, 40, '2026-03-04', 'not_paid');

    $creditNote = postedInvoice($company, 30, '2026-03-05');
    Move::withoutGlobalScopes()->whereKey($creditNote->getKey())->update([
        'move_type'           => MoveType::OUT_REFUND,
        'amount_total_signed' => -30,
    ]);

    $widget = invoiceWidget(InvoiceStatsWidget::class, ['start_date' => '2026-03-01', 'end_date' => '2026-03-31']);

    $net = (fn () => $this->customerInvoicesNet())->call($widget);
    $unpaid = (fn () => $this->unpaidOnly($this->customerInvoices()))->call($widget);

    expect((float) $net->sum('amount_total_signed'))->toBe(170.0)
        ->and($unpaid->count())->toBe(2);
});

/**
 * DB-7 / D-DB-5 (2026-11-22): money to collect in more than one currency is
 * split by currency under the total, and a debt row shows its own currency.
 */
it('splits money to collect by currency and shows a foreign debt in its own currency', function (): void {
    $company = Company::factory()->create();
    CompanyHelper::actingAsCompanyUser($company, []);

    $local = Currency::query()->updateOrCreate(['name' => 'GHS'], ['active' => true]);
    $usd = Currency::query()->updateOrCreate(['name' => 'USD'], ['active' => true]);
    $company->forceFill(['currency_id' => $local->getKey()])->save();

    foreach ([[$usd, 100, 1550], [$local, 500, 500]] as [$currency, $own, $base]) {
        $invoice = postedInvoice($company, $base, now()->toDateString(), 'not_paid');
        Move::withoutGlobalScopes()->whereKey($invoice->getKey())->update([
            'currency_id'            => $currency->getKey(),
            'amount_residual'        => $own,
            'amount_residual_signed' => $base,
            'invoice_date_due'       => now()->addDays(3)->toDateString(),
        ]);
    }

    $widget = invoiceWidget(InvoiceHeadlineWidget::class, []);

    $stats = (fn () => $this->getStats())->call($widget);
    $description = (string) $stats[0]->getDescription();

    $rows = $widget->panel([])['list']['rows']['open'];

    // ICU writes symbols ("$100", "GH₵500"), so check the split itself: the
    // overdue text plus one part per currency.
    expect(substr_count($description, ' · '))->toBe(2)
        ->and($description)->toContain('$')
        ->and(collect($rows)->pluck('amount')->filter(fn (string $amount): bool => str_contains($amount, '100'))->first())->toContain('$');
});

it('draws revenue per day from one grouped sum of paid invoices', function (): void {
    $company = Company::factory()->create();

    CompanyHelper::actingAsCompanyUser($company, []);

    postedInvoice($company, 100, '2026-03-01');
    postedInvoice($company, 40.5, '2026-03-01');
    postedInvoice($company, 60, '2026-03-03');
    postedInvoice($company, 999, '2026-03-02', 'not_paid');

    $widget = invoiceWidget(RevenueOverTimeWidget::class, ['start_date' => '2026-03-01', 'end_date' => '2026-03-03']);

    DB::flushQueryLog();
    DB::enableQueryLog();

    $data = (fn (): array => $this->getData())->call($widget);

    $moveQueries = collect(DB::getQueryLog())
        // Data queries only: the log also holds one-off column lookups on the
        // table (pg_attribute), which are not part of the per-day N+1.
        ->filter(fn (array $entry): bool => str_contains($entry['query'], 'from "accounts_account_moves"'))
        ->count();

    DB::disableQueryLog();

    expect($data['datasets'][0]['data'])->toBe([140.5, 0.0, 60.0])
        ->and($data['labels'])->toHaveCount(3)
        ->and($moveQueries)->toBe(1);
});

it('lists only the current company’s members as salespersons', function (): void {
    $company = Company::factory()->create();
    $otherCompany = Company::factory()->create();

    $member = CompanyHelper::actingAsCompanyUser($company, []);

    $outsider = User::factory()->create();
    $outsider->allowedCompanies()->sync([$otherCompany->getKey()]);

    $options = (fn (): array => static::companyMemberOptions())->bindTo(null, Invoices::class)();

    expect($options)->toHaveKey($member->getKey())
        ->not->toHaveKey($outsider->getKey());
});

/**
 * Dashboard plan DB-3 (2026-11-23): every widget this dashboard lists is
 * absent without its permission or once the module is off, and present with
 * both. A widget added without its guard fails here.
 */
it('shows every invoice dashboard widget only with its permission and the module', function (): void {
    FilamentHelper::bootAdminPanel();

    $company = Company::factory()->create();

    // The cash-flow chart checks the revenue-over-time widget's permission
    // (OverviewCashFlowChart::getWidgetPermission()), which is not listed.
    $revenuePermission = array_key_first(FilamentShield::getWidgets()[RevenueOverTimeWidget::class]['permissions']);

    expect(DashboardWidgetGuardHelper::visibility(Invoices::sectionWidgets(), 'invoices', $company, [$revenuePermission]))
        ->toBe(DashboardWidgetGuardHelper::guarded(Invoices::sectionWidgets()));
});
