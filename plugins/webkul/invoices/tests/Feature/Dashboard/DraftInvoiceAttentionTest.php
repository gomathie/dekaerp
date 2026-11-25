<?php

use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Models\Move;
use Webkul\Invoice\Filament\Overview\InvoiceAttention;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('invoices');
    PluginAccess::flush();
});

/**
 * A move written past the model hooks, as OverviewTest does: only the
 * columns Needs attention reads matter.
 */
function attentionMove(Company $company, MoveType $type, MoveState $state, float $signed): void
{
    $move = Move::factory()->create(['company_id' => $company->getKey()]);

    Move::withoutGlobalScopes()->whereKey($move->getKey())->update([
        'company_id'          => $company->getKey(),
        'move_type'           => $type,
        'state'               => $state,
        'payment_state'       => 'not_paid',
        'invoice_date_due'    => now()->addMonth()->toDateString(),
        'amount_total_signed' => $signed,
        'creator_id'          => auth()->id(),
    ]);
}

/**
 * TRACE365, 2026-11-25 (user): five customer invoices sat in draft, two of
 * them from confirmed orders, and Needs attention said "All clear". Every
 * draft customer invoice of this company now shows, with the count, the
 * total and a link to the invoices list's Draft view. Posted invoices,
 * draft bills and another company's drafts do not count.
 */
it('lists the company\'s draft customer invoices with their total in needs attention', function (): void {
    $company = Company::factory()->create();
    $other = CompanyHelper::asNobody(fn () => Company::factory()->create());

    CompanyHelper::actingAsCompanyUser($company, ['page_invoice_invoices', 'view_any_invoice_invoice']);

    attentionMove($company, MoveType::OUT_INVOICE, MoveState::DRAFT, 337.5);
    attentionMove($company, MoveType::OUT_INVOICE, MoveState::DRAFT, 225.0);
    attentionMove($company, MoveType::OUT_INVOICE, MoveState::POSTED, 999.0);
    attentionMove($company, MoveType::IN_INVOICE, MoveState::DRAFT, -500.0);
    CompanyHelper::asNobody(fn () => attentionMove($other, MoveType::OUT_INVOICE, MoveState::DRAFT, 7000.0));

    $draft = InvoiceAttention::items()[1];

    expect($draft['count'])->toBe(2)
        ->and((string) $draft['url'])->toContain('activeTableView=draft')
        ->and($draft['label'])->toBe(trans_choice('invoices::filament/pages/overview.attention.draft-invoices-amount', 2, [
            'count'  => 2,
            'amount' => compact_money(562.5, current_company()?->currency?->name),
        ]))
        ->and($draft['label'])->toContain('562.50');
});

it('leaves the draft invoices amount out when the user\'s money figures are off', function (): void {
    $company = Company::factory()->create();
    $user = CompanyHelper::actingAsCompanyUser($company, ['page_invoice_invoices', 'view_any_invoice_invoice']);

    attentionMove($company, MoveType::OUT_INVOICE, MoveState::DRAFT, 337.5);

    $user->forceFill(['can_see_money_figures' => false])->save();

    $draft = InvoiceAttention::items()[1];

    expect($draft['count'])->toBe(1)
        ->and($draft['label'])->toBe(trans_choice('invoices::filament/pages/overview.attention.draft-invoices', 1, ['count' => 1]))
        ->and($draft['label'])->not->toContain('337');
});

it('counts no draft invoices when there are none, so the line stays out', function (): void {
    $company = Company::factory()->create();
    CompanyHelper::actingAsCompanyUser($company, ['page_invoice_invoices', 'view_any_invoice_invoice']);

    attentionMove($company, MoveType::OUT_INVOICE, MoveState::POSTED, 100.0);

    $draft = InvoiceAttention::items()[1];

    expect($draft['count'])->toBe(0);
});
