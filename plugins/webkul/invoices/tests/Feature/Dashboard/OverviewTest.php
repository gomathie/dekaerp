<?php

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Illuminate\Support\Facades\DB;
use Webkul\Invoice\Filament\Overview\OverviewRegistry;
use Webkul\Invoice\Filament\Pages\Invoices;
use Webkul\Invoice\Filament\Pages\Overview;
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

    $page->filters = ['sections' => ['invoices']];
    expect($page->getWidgets())->toBe(Invoices::sectionWidgets());

    $page->filters = ['sections' => ['test-section']];
    expect($page->getWidgets())->toBe(['TestOnlyWidget']);
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

it('refuses the overview to a user who may see no section', function (): void {
    CompanyHelper::actingAsCompanyUser(Company::factory()->create(), []);

    expect(Overview::canAccess())->toBeFalse();
});
