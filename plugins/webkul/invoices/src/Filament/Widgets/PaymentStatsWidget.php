<?php

namespace Webkul\Invoice\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Livewire\Attributes\Locked;
use Webkul\Account\Filament\Concerns\ShowsPaymentStats;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\PaymentResource;
use Webkul\PluginManager\Support\PluginEntitlement;
use Webkul\Support\Filament\DashboardAccess;

/**
 * Money received and the number of payments, above the payments received
 * list (plan #9 R-9). No dashboard had these as cards, so this is the one
 * widget; which payments count is the Overview chart's rule
 * (RecordedPayments), so "Received" equals the dashboard's "Money in" for
 * the same period. Figures and layout: ShowsPaymentStats, shared with
 * Accounting's payments list.
 *
 * Not discovered: it belongs to the list, not to a dashboard, and is still
 * registered with Livewire by the plugin's widget discovery.
 */
class PaymentStatsWidget extends BaseWidget
{
    use ShowsPaymentStats;

    // No 5-second refresh (Filament default): every open dashboard re-ran all
    // its queries 12 times a minute (Sentry DEKAERP-11). Filters reload it.
    protected ?string $pollingInterval = null;

    protected static bool $isDiscovered = false;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    /**
     * Names the period, as on the invoice list. Locked: only the page that
     * places it sets it.
     */
    #[Locked]
    public ?string $periodHeading = null;

    /**
     * Payment amounts, so the same people as the list itself (the Invoices
     * module and the payments permission, through the list's own resource),
     * and only with the user's money-figures switch on: a widget is reachable
     * through Livewire on its own, so hiding the list is not enough.
     */
    public static function canView(): bool
    {
        return PluginEntitlement::enabledForCurrent('invoices')
            && DashboardAccess::mayViewMoney()
            && PaymentResource::canViewAny();
    }

    protected function paymentStatsLabel(string $key): string
    {
        return __('invoices::filament/widgets/invoice-dashboard.payment-stats.'.$key);
    }
}
