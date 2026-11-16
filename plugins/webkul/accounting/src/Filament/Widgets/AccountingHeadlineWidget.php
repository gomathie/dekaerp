<?php

namespace Webkul\Accounting\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Webkul\Accounting\Filament\Clusters\Accounting\Pages\BankReconciliation;
use Webkul\Accounting\Filament\OverviewCard;

/**
 * The Accounting figures on the combined Overview's "At a glance" row
 * (accounting plan A5): cash at bank, bank lines to reconcile, draft
 * journal entries. Today's position, so the Overview's date filters do not
 * apply. Figures come from OverviewCard, the one place that computes them.
 */
class AccountingHeadlineWidget extends StatsOverviewWidget
{
    // No 5-second refresh (Filament default; Sentry DEKAERP-11).
    protected ?string $pollingInterval = null;

    protected static bool $isLazy = false;

    protected static bool $isDiscovered = false;

    public static function canView(): bool
    {
        return OverviewCard::visible();
    }

    /**
     * @param  array<string, mixed>  $filters  accepted for the shared contract, unused
     * @return array<int, Stat>
     */
    public function headlineStats(array $filters): array
    {
        return $this->getStats();
    }

    protected function getStats(): array
    {
        $lang = 'accounting::filament/overview-card';
        $figures = OverviewCard::figures();

        return [
            Stat::make(__($lang.'.cash'), compact_money($figures['cash'], current_company()?->currency?->name))
                ->url(OverviewCard::cashUrl())
                ->icon('heroicon-o-banknotes')
                ->description(__($lang.'.cash-hint'))
                ->color('primary')
                ->extraAttributes(['class' => 'overview-headline-stat']),

            Stat::make(__($lang.'.to-reconcile'), number_format($figures['to_reconcile']))
                ->url(BankReconciliation::canAccess() ? BankReconciliation::getUrl() : null)
                ->icon('heroicon-o-building-library')
                ->description(__($lang.'.to-reconcile-hint'))
                ->color($figures['to_reconcile'] > 0 ? 'warning' : 'success')
                ->extraAttributes(['class' => 'overview-headline-stat']),

            Stat::make(__($lang.'.drafts'), number_format($figures['drafts']))
                ->url(OverviewCard::draftsUrl())
                ->icon('heroicon-o-pencil-square')
                ->description(__($lang.'.drafts-hint'))
                ->color($figures['drafts'] > 0 ? 'warning' : 'success')
                ->extraAttributes(['class' => 'overview-headline-stat']),
        ];
    }
}
