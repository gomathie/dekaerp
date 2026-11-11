<?php

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\DB;
use Webkul\Invoice\Filament\Overview\OverviewRegistry;
use Webkul\Invoice\Filament\Pages\Invoices;
use Webkul\Invoice\Filament\Pages\Overview;
use Webkul\Invoice\Filament\Widgets\OverviewHeadlineWidget;
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

afterEach(fn () => OverviewRegistry::forget('test-section'));

/**
 * Sales, Purchases and Invoices on one Overview page (user, 2026-11-11).
 * Pinned here: each section follows its own dashboard's access rule (module
 * switch plus page permission), the page needs no permission of its own,
 * and choosing a section limits the widgets to that section.
 */
it('shows the Invoices section only with the invoice dashboard permission and module', function (): void {
    $company = Company::factory()->create();

    CompanyHelper::actingAsCompanyUser($company, []);

    expect(array_key_exists('invoices', OverviewRegistry::visible()))->toBeFalse();

    CompanyHelper::actingAsCompanyUser($company, ['page_invoice_invoices']);

    expect(array_key_exists('invoices', OverviewRegistry::visible()))->toBeTrue()
        ->and(Overview::canAccess())->toBeTrue();

    $plugin = Plugin::query()->where('name', 'invoices')->firstOrFail();
    DB::table('company_plugin')->where('company_id', $company->getKey())->where('plugin_id', $plugin->getKey())->delete();
    PluginAccess::flush();

    expect(array_key_exists('invoices', OverviewRegistry::visible()))->toBeFalse();
});

it('limits the widgets to the chosen section and shows every visible section by default', function (): void {
    $company = Company::factory()->create();
    CompanyHelper::actingAsCompanyUser($company, ['page_invoice_invoices']);

    OverviewRegistry::register('test-section', fn (): string => 'Test', ['TestOnlyWidget'], fn (): array => [], fn (): bool => true, sort: 99);

    $page = new Overview;

    $page->filters = [];
    expect($page->getWidgets())->toContain('TestOnlyWidget')
        ->and($page->getWidgets())->toContain(...Invoices::sectionWidgets());

    // Changed 2026-11-11 (user): "At a glance" comes first, then widgets are
    // grouped by kind across sections (charts, stats, lists).
    $page->filters = ['sections' => ['invoices']];
    expect($page->getWidgets()[0])->toBe(OverviewHeadlineWidget::class)
        ->and($page->getWidgets())->toContain(...Invoices::sectionWidgets())
        ->and($page->getWidgets())->not->toContain('TestOnlyWidget');

    $page->filters = ['sections' => ['test-section']];
    expect($page->getWidgets())->toBe([OverviewHeadlineWidget::class, 'TestOnlyWidget']);
});

/**
 * Relevant first (user, 2026-11-11): charts before detailed stat cards,
 * stat cards before lists, across all sections.
 */
it('orders the widgets: at a glance, charts, stats, then lists', function (): void {
    CompanyHelper::actingAsCompanyUser(Company::factory()->create(), ['page_invoice_invoices']);

    $page = new Overview;
    $page->filters = ['sections' => ['invoices']];

    $kinds = array_map(fn (string $widget): string => match (true) {
        $widget === OverviewHeadlineWidget::class                                 => 'glance',
        is_subclass_of($widget, TableWidget::class)                               => 'list',
        is_subclass_of($widget, StatsOverviewWidget::class)                       => 'stats',
        default                                                                   => 'chart',
    }, $page->getWidgets());

    $order = ['glance' => 0, 'chart' => 1, 'stats' => 2, 'list' => 3];
    $ranks = array_map(fn (string $kind): int => $order[$kind], $kinds);

    expect($ranks)->toBe(collect($ranks)->sort()->values()->all());
});

/**
 * All filters show by default (user, 2026-11-11). One Vendor filter must
 * filter both Bills (vendor_id) and Purchases (partner_id), and clearing it
 * must clear both, not be restored from the stale copy.
 */
it('copies a shared filter to every section name and clears all of them together', function (): void {
    $page = new Overview;
    $page->filters = ['vendor_id' => [5], 'partner_id' => null];

    $page->updatedFilters([5], 'vendor_id');
    expect($page->filters['partner_id'])->toBe([5]);

    $page->filters['vendor_id'] = [];
    $page->updatedFilters([], 'vendor_id');
    expect($page->filters['partner_id'])->toBe([]);

    expect(Overview::mirrorAliases(['countries' => [2]])['country_id'])->toBe([2]);
});

it('needs no permission of its own, so existing roles keep their dashboards', function (): void {
    FilamentHelper::bootAdminPanel();

    $generated = collect(FilamentShield::getPages())
        ->flatMap(fn (array $page): array => array_keys($page['permissions']))
        ->all();

    expect($generated)->not->toContain('page_invoice_overview')
        // The section still answers to the old dashboard's permission.
        ->and($generated)->toContain('page_invoice_invoices')
        ->and(Invoices::shouldRegisterNavigation())->toBeFalse();
});

/**
 * Sentry DEKAERP-11 (2026-11-11): Filament stats and chart widgets refresh
 * every 5 seconds by default, so each open Overview re-ran every query 12
 * times a minute. None of them may poll; changing a filter reloads them.
 */
it('does not poll any widget on the overview', function (): void {
    CompanyHelper::actingAsCompanyUser(Company::factory()->create(), ['page_invoice_invoices']);

    $page = new Overview;
    $page->filters = [];

    $widgets = array_merge($page->getWidgets(), array_filter(array_column(OverviewRegistry::visible(), 'headline')));

    foreach ($widgets as $widget) {
        $defaults = (new ReflectionClass($widget))->getDefaultProperties();

        if (array_key_exists('pollingInterval', $defaults)) {
            expect($defaults['pollingInterval'])->toBeNull($widget);
        }
    }
});

/**
 * Layout (user, 2026-11-11): filters closed by default, four headline cards
 * across, three charts across on wide screens, and every headline card has an
 * icon so the row reads at a glance.
 */
it('starts with filters closed, four cards across and an icon on every card', function (): void {
    CompanyHelper::actingAsCompanyUser(Company::factory()->create(), ['page_invoice_invoices']);

    expect(Webkul\Support\Filament\Forms\Components\DashboardFilters::make([], [])->isCollapsed())->toBeTrue()
        ->and((new Overview)->getColumns())->toBe(['md' => 2, 'xl' => 3])
        ->and((fn () => $this->getColumns())->call(new OverviewHeadlineWidget))->toBe(['default' => 2, 'lg' => 4]);

    $stats = app(Webkul\Invoice\Filament\Widgets\InvoiceHeadlineWidget::class)->headlineStats([]);

    foreach ($stats as $stat) {
        expect($stat->getIcon())->not->toBeNull();
    }
});

it('refuses the overview to a user who may see no section', function (): void {
    CompanyHelper::actingAsCompanyUser(Company::factory()->create(), []);

    expect(Overview::canAccess())->toBeFalse();
});
