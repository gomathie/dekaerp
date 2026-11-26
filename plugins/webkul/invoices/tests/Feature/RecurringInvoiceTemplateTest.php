<?php

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Models\Move;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\RecurringInvoiceTemplateResource\Pages\CreateRecurringInvoiceTemplate;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\RecurringInvoiceTemplateResource\Pages\ListRecurringInvoiceTemplates;
use Webkul\Invoice\Models\RecurringInvoicePeriod;
use Webkul\Invoice\Models\RecurringInvoiceTemplate;
use Webkul\Invoice\Models\RecurringInvoiceTemplateLine;
use Webkul\Invoice\Services\RecurringInvoiceService;
use Webkul\PluginManager\Support\PluginAccess;

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../accounts/tests/Helpers/AccountHelper.php';

function recurringPermissions(): array
{
    return [
        'view_any_invoice_recurring::invoice::template',
        'view_invoice_recurring::invoice::template',
        'create_invoice_recurring::invoice::template',
        'update_invoice_recurring::invoice::template',
    ];
}

function recurringTemplate(array $overrides = []): RecurringInvoiceTemplate
{
    $template = RecurringInvoiceTemplate::create(array_merge([
        'company_id'        => test()->company->id,
        'partner_id'        => test()->partner->id,
        'journal_id'        => test()->journal->id,
        'currency_id'       => test()->company->currency_id,
        'invoice_user_id'   => auth()->id(),
        'name'              => 'Monthly support',
        'frequency'         => 'monthly',
        'next_invoice_date' => '2026-11-01',
        'state'             => 'active',
    ], $overrides));

    RecurringInvoiceTemplateLine::create([
        'recurring_template_id' => $template->id,
        'name'                  => 'Support',
        'quantity'              => 1,
        'price_unit'            => 100,
    ]);

    return $template;
}

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('invoices');
    PluginAccess::flush();

    $this->company = AccountHelper::company();
    $this->journal = AccountHelper::saleJournal();
    $this->partner = AccountHelper::partner();
});

it('registers only view, create and update permissions for recurring templates', function (): void {
    FilamentHelper::bootAdminPanel();

    $generated = collect(FilamentShield::getResources())
        ->flatMap(fn (array $resource): array => array_column($resource['permissions'] ?? [], 'key'))
        ->all();

    expect($generated)->toContain(...recurringPermissions())
        ->and($generated)->not->toContain('delete_invoice_recurring::invoice::template', 'delete_any_invoice_recurring::invoice::template');
});

it('forbids the recurring template list without permission', function (): void {
    FilamentHelper::actingAs([]);

    Livewire::test(ListRecurringInvoiceTemplates::class)->assertForbidden();
});

it('creates a template with its company, creator and one recurring line', function (): void {
    FilamentHelper::actingAs(recurringPermissions());

    Livewire::test(CreateRecurringInvoiceTemplate::class)
        ->fillForm([
            'name'              => 'Monthly support',
            'partner_id'        => $this->partner->id,
            'journal_id'        => $this->journal->id,
            'currency_id'       => $this->company->currency_id,
            'frequency'         => 'monthly',
            'next_invoice_date' => '2026-11-01',
            'lines'             => [['name' => 'Support', 'quantity' => 1, 'price_unit' => 100]],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $template = RecurringInvoiceTemplate::query()->where('name', 'Monthly support')->firstOrFail();

    expect((int) $template->company_id)->toBe((int) $this->company->id)
        ->and((int) $template->invoice_user_id)->toBe((int) auth()->id())
        ->and($template->lines)->toHaveCount(1)
        ->and($template->state)->toBe('active');
});

it('generates a balanced draft once and advances to the next scheduled period', function (): void {
    FilamentHelper::actingAs(recurringPermissions());
    $template = recurringTemplate();

    $invoice = app(RecurringInvoiceService::class)->generateDue((int) $template->id);

    expect($invoice)->toBeInstanceOf(Move::class)
        ->and($invoice->state)->toBe(MoveState::DRAFT)
        ->and($invoice->date->toDateString())->toBe('2026-11-01')
        ->and($invoice->lines()->where('display_type', DisplayType::PRODUCT)->count())->toBe(1)
        ->and((float) $invoice->amount_total)->toBe(100.0)
        ->and(RecurringInvoicePeriod::query()->where('recurring_template_id', $template->id)->count())->toBe(1)
        ->and($template->refresh()->next_invoice_date->toDateString())->toBe('2026-12-01');

});

it('uses the same invoice for a repeated period and creates the upcoming period on demand', function (): void {
    FilamentHelper::actingAs(recurringPermissions());
    $template = recurringTemplate();
    $service = app(RecurringInvoiceService::class);

    $first = $service->generateDue((int) $template->id);
    $duplicate = $service->generateDue((int) $template->id, scheduledDate: '2026-11-01');
    $upcoming = $service->generateNow($template, auth()->user());

    expect($duplicate->is($first))->toBeTrue()
        ->and($upcoming)->not->toBeNull()
        ->and($upcoming->invoice_date->toDateString())->toBe('2026-12-01')
        ->and(RecurringInvoicePeriod::query()->where('recurring_template_id', $template->id)->count())->toBe(2)
        ->and($template->refresh()->next_invoice_date->toDateString())->toBe('2027-01-01');
});

it('confirms and emails an automatic template from a scheduled run with nobody signed in', function (): void {
    // The scheduler has no signed-in user. The first build restored the
    // previous user with setUser(null), which throws, and named a partner
    // class that does not exist, so every automatic send crashed.
    Mail::fake();
    FilamentHelper::actingAs(recurringPermissions());
    $this->partner->forceFill(['email' => 'billing@example.test'])->save();
    $template = recurringTemplate(['auto_email' => true]);
    Auth::guard('web')->logout();
    auth()->forgetGuards();
    expect(Auth::guard('web')->user())->toBeNull();

    $invoice = app(RecurringInvoiceService::class)->generateDue((int) $template->id);

    expect($invoice)->toBeInstanceOf(Move::class)
        ->and($invoice->refresh()->state)->toBe(MoveState::POSTED)
        ->and($template->refresh()->last_failure_reason)->toBeNull()
        ->and(Auth::guard('web')->user())->toBeNull();

    Mail::assertSentCount(1);
});

it('does not generate while paused and stopping leaves prior draft invoices untouched', function (): void {
    FilamentHelper::actingAs(recurringPermissions());
    $template = recurringTemplate(['state' => 'paused']);
    $service = app(RecurringInvoiceService::class);

    expect($service->generateDue((int) $template->id))->toBeNull();

    $service->setState($template, 'active', auth()->user());
    $invoice = $service->generateDue((int) $template->id);
    $service->setState($template, 'stopped', auth()->user());

    expect($template->refresh()->state)->toBe('stopped')
        ->and($invoice->refresh()->state)->toBe(MoveState::DRAFT)
        ->and($service->generateDue((int) $template->id))->toBeNull();
});
