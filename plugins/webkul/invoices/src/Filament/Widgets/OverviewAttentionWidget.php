<?php

namespace Webkul\Invoice\Filament\Widgets;

use Filament\Widgets\Widget;
use Webkul\Invoice\Filament\Overview\OverviewRegistry;

/**
 * "Needs attention" on the Overview's All tab (user, 2026-11-13, after
 * finance dashboards on Dribbble): what is waiting on someone today, from
 * every visible section, each linking to its list. Empty shows "All clear"
 * rather than hiding, so the layout does not jump.
 *
 * Each section decides its own items and their links (OverviewRegistry
 * `attention`), so Invoices never names Sales or Purchases.
 */
class OverviewAttentionWidget extends Widget
{
    protected string $view = 'invoices::filament.widgets.overview-attention';

    protected static bool $isLazy = false;

    protected static bool $isDiscovered = false;

    protected int|string|array $columnSpan = ['default' => 'full', 'md' => 2, 'xl' => 1];

    public static function canView(): bool
    {
        return OverviewRegistry::visible() !== [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return ['items' => OverviewRegistry::attentionItems()];
    }
}
