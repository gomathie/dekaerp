<?php

namespace Webkul\Invoice\Filament\Widgets;

use Filament\Widgets\Widget;
use Webkul\Support\Filament\Contributions\DashboardCardRegistry;

/**
 * "Across the company" on the Overview's All tab (mockup approved by the
 * user, 2026-11-13): every other module's key items as compact cards, read
 * from DashboardCardRegistry. One Livewire component for all of them, so the
 * All tab stays well under the per-request component limit (DEKAERP-16).
 */
class OverviewModuleCards extends Widget
{
    protected string $view = 'invoices::filament.widgets.overview-module-cards';

    protected static bool $isLazy = false;

    protected static bool $isDiscovered = false;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return DashboardCardRegistry::visible() !== [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return ['cards' => DashboardCardRegistry::visible()];
    }
}
