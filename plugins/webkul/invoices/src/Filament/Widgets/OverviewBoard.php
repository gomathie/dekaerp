<?php

namespace Webkul\Invoice\Filament\Widgets;

use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Widget;
use Throwable;
use Webkul\Invoice\Filament\Overview\OverviewRegistry;

/**
 * The Overview's All tab as one board (mockup approved by the user,
 * 2026-11-13, after the most-viewed ERP dashboards on Dribbble): one panel
 * per module holding its figures, a small visual per figure and, for
 * Invoices, the list to act on.
 *
 * Layout: a main column (Sales, money in and out, Purchases) and a side
 * column (Needs attention, Invoices). A company without a module simply has
 * no panel for it and the column closes up; with only one column's panels,
 * that column takes the full width (user: "render cleanly when modules are
 * not active").
 *
 * Each section computes its own figures in its own plugin (the `panel()`
 * method on the headline widget it registered), so Invoices never names
 * Sales or Purchases. A panel whose query fails is reported and left out.
 * Refreshes every 5 minutes (user, 2026-11-13).
 */
class OverviewBoard extends Widget
{
    use InteractsWithPageFilters;

    protected string $view = 'invoices::filament.widgets.overview-board';

    protected static bool $isLazy = false;

    protected static bool $isDiscovered = false;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return OverviewRegistry::visible() !== [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $sections = OverviewRegistry::visible();
        $filters = $this->pageFilters ?? [];

        $panels = [];

        foreach (['sales', 'purchases', 'invoices'] as $key) {
            $class = $sections[$key]['headline'] ?? null;

            if ($class === null || ! method_exists($class, 'panel')) {
                continue;
            }

            try {
                $panels[$key] = app($class)->panel($filters);
            } catch (Throwable $exception) {
                report($exception);
            }
        }

        return [
            'panels'       => $panels,
            'attention'    => OverviewRegistry::attentionItems(),
            'showCashFlow' => array_key_exists('invoices', $sections) && OverviewCashFlowChart::canView(),
            'filters'      => $filters,
            'updatedAt'    => now(),
        ];
    }
}
