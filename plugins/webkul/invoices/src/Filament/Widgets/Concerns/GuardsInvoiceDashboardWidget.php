<?php

namespace Webkul\Invoice\Filament\Widgets\Concerns;

use Webkul\Support\Filament\Concerns\GuardsDashboardWidget;

/**
 * Invoice dashboard widgets require the Invoices module and their own Shield
 * permission; the rule itself lives in GuardsDashboardWidget.
 */
trait GuardsInvoiceDashboardWidget
{
    use GuardsDashboardWidget;

    protected static function dashboardPluginName(): string
    {
        return 'invoices';
    }
}
