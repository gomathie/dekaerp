<?php

namespace Webkul\Accounting\Filament;

use Illuminate\Support\Facades\DB;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Models\BankStatementLine;
use Webkul\Account\Models\Move;
use Webkul\Accounting\Filament\Clusters\Accounting\Pages\BankReconciliation;
use Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalEntryResource;
use Webkul\Accounting\Filament\Clusters\Reporting\Pages\CashFlow;
use Webkul\Accounting\Filament\Pages\Overview;
use Webkul\Accounting\Support\CompanyRateMap;
use Webkul\Support\Services\CompanyContext;

/**
 * Accounting on the combined Overview (accounting plan A5): the card on the
 * All tab, the section's headline figures, and the "needs attention" item.
 * One place computes the three figures, so the card, the headline and the
 * greeting never disagree.
 *
 * Visible exactly when the Accounting overview page is (module switch plus
 * its page permission). Each link appears only when its target opens for
 * this user.
 */
class OverviewCard
{
    public static function visible(): bool
    {
        return Overview::canAccess();
    }

    /**
     * @return array{cash: float, to_reconcile: int, drafts: int}
     */
    public static function figures(): array
    {
        $companyIds = app(CompanyContext::class)->activeIds();
        $rateMap = CompanyRateMap::make();

        // Raw ledger join, so the companies are filtered here explicitly;
        // CompanyScope would also count lines with no company.
        $cash = (float) DB::table('accounts_account_move_lines')
            ->join('accounts_account_moves', 'accounts_account_moves.id', '=', 'accounts_account_move_lines.move_id')
            ->join('accounts_accounts', 'accounts_accounts.id', '=', 'accounts_account_move_lines.account_id')
            ->where('accounts_account_moves.state', MoveState::POSTED)
            ->where('accounts_accounts.account_type', AccountType::ASSET_CASH)
            ->whereIn('accounts_account_moves.company_id', $rateMap->companyIds())
            ->value(DB::raw('COALESCE(SUM('.$rateMap->weight('accounts_account_move_lines.balance').'), 0)'));

        return [
            'cash'         => round($cash, 2),
            'to_reconcile' => BankStatementLine::query()
                ->whereIn('company_id', $companyIds)
                ->where('is_reconciled', false)
                ->count(),
            'drafts'       => Move::query()
                ->where('move_type', MoveType::ENTRY)
                ->where('state', MoveState::DRAFT)
                ->count(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function data(): array
    {
        $lang = 'accounting::filament/overview-card';
        $figures = static::figures();

        return [
            'label' => __($lang.'.label'),
            'url'   => Overview::getUrl(),
            'items' => [
                ['label' => __($lang.'.cash'), 'value' => compact_money($figures['cash'], current_company()?->currency?->name)],
                ['label' => __($lang.'.to-reconcile'), 'value' => number_format($figures['to_reconcile']), 'tone' => $figures['to_reconcile'] > 0 ? 'warning' : null],
                ['label' => __($lang.'.drafts'), 'value' => number_format($figures['drafts'])],
            ],
            'bar' => null,
        ];
    }

    /**
     * @return list<array{label: string, count: int, url: ?string, tone: string}>
     */
    public static function attention(): array
    {
        $count = static::figures()['to_reconcile'];

        return [[
            'label' => trans_choice('accounting::filament/overview-card.attention', $count, ['count' => $count]),
            'count' => $count,
            'url'   => BankReconciliation::canAccess() ? BankReconciliation::getUrl() : null,
            'tone'  => 'warning',
        ]];
    }

    /**
     * @return list<array{label: string, url: string, icon: string}>
     */
    public static function actions(): array
    {
        return BankReconciliation::canAccess() ? [[
            'label' => __('accounting::filament/overview-card.reconcile'),
            'url'   => BankReconciliation::getUrl(),
            'icon'  => 'heroicon-m-building-library',
        ]] : [];
    }

    public static function cashUrl(): ?string
    {
        return CashFlow::canAccess() ? CashFlow::getUrl() : null;
    }

    public static function draftsUrl(): ?string
    {
        return JournalEntryResource::canViewAny() ? JournalEntryResource::getUrl('index') : null;
    }
}
