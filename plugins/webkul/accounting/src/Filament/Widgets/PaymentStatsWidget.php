<?php

namespace Webkul\Accounting\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Livewire\Attributes\Locked;
use Webkul\Account\Filament\Concerns\ShowsPaymentStats;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\PaymentResource;
use Webkul\PluginManager\Support\PluginEntitlement;
use Webkul\Support\Filament\DashboardAccess;

/**
 * This month's money received above Accounting's own payments list (plan #9
 * R-9), the same cards as the Invoices plugin's list. The figures come from
 * ShowsPaymentStats and RecordedPayments in Accounts, so both lists and the
 * Overview chart agree; Accounting does not depend on Invoices.
 *
 * Not discovered: it belongs to the list, not to a dashboard.
 */
class PaymentStatsWidget extends BaseWidget
{
    use ShowsPaymentStats;

    // No 5-second refresh (Filament default, Sentry DEKAERP-11).
    protected ?string $pollingInterval = null;

    protected static bool $isDiscovered = false;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    /** Names the period. Locked: only the page that places it sets it. */
    #[Locked]
    public ?string $periodHeading = null;

    /**
     * Payment amounts: the Accounting module, this list's own permission, and
     * the user's money-figures switch. The widget is reachable through
     * Livewire on its own, so the page hiding it is not enough.
     */
    public static function canView(): bool
    {
        return PluginEntitlement::enabledForCurrent('accounting')
            && DashboardAccess::mayViewMoney()
            && PaymentResource::canViewAny();
    }

    protected function paymentStatsLabel(string $key): string
    {
        return __('accounting::filament/widgets/payment-stats.'.$key);
    }
}
