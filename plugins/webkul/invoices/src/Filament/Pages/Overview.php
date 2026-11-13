<?php

namespace Webkul\Invoice\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\TableWidget;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Invoice\Filament\Overview\OverviewRegistry;
use Webkul\Invoice\Filament\Widgets\OverviewAttentionWidget;
use Webkul\Invoice\Filament\Widgets\OverviewCashFlowChart;
use Webkul\Invoice\Filament\Widgets\OverviewHeadlineWidget;
use Webkul\Invoice\Filament\Widgets\OverviewModuleCards;
use Webkul\Product\Models\Category;
use Webkul\Product\Models\Product;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Support\Filament\Forms\Components\DashboardDateRange;
use Webkul\Support\Filament\Forms\Components\DashboardFilters;

/**
 * Sales, Purchases and Invoices on one page (user, 2026-11-11), replacing
 * the three dashboards in the menu. Their pages still exist and redirect
 * here with their section chosen, so old links keep working.
 *
 * Access has no permission of its own on purpose: a user may open the page
 * when they may see at least one section, and each section is decided by
 * its own dashboard's canAccess() (module switch plus that page's Shield
 * permission). A new permission would have hidden the page from every
 * existing role until someone ticked it.
 */
class Overview extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;

    protected static string $routePath = 'overview';

    protected static ?int $navigationSort = -10;

    public static function canAccess(): bool
    {
        return OverviewRegistry::visible() !== [];
    }

    /**
     * Filters that mean the same thing but are named differently by each
     * section. The Overview shows one field per group and copies its value
     * to every name in the group, so one Vendor filter filters both
     * Purchases (partner_id) and Bills (vendor_id).
     *
     * @var list<list<string>>
     */
    protected const FILTER_ALIASES = [
        ['vendor_id', 'partner_id'],
        ['salesperson_id', 'salesPersons'],
        ['country_id', 'countries'],
    ];

    public function mount(): void
    {
        // An old dashboard link opens here with its section chosen.
        $section = request()->query('section');

        if (is_string($section) && array_key_exists($section, OverviewRegistry::visible())) {
            $this->filters = array_merge($this->filters ?? [], ['sections' => [$section]]);
        }

        $this->filters = static::mirrorAliases($this->filters ?? []);
    }

    /**
     * Livewire hook: copy the changed filter to its other names. It uses the
     * value that changed, not "the first one set", so clearing a filter
     * clears every name instead of being restored from a stale copy.
     */
    public function updatedFilters(mixed $value = null, ?string $key = null): void
    {
        // A multi-select may report "vendor_id.0"; the field is the first part.
        $field = $key === null ? null : explode('.', $key)[0];

        foreach (static::FILTER_ALIASES as $group) {
            if ($field !== null && in_array($field, $group, true)) {
                $current = $this->filters[$field] ?? null;

                foreach ($group as $name) {
                    $this->filters[$name] = $current;
                }
            }
        }
    }

    /** The index of the alias group a filter name belongs to, if any. */
    protected static function aliasGroupOf(string $name): ?int
    {
        foreach (static::FILTER_ALIASES as $index => $group) {
            if (in_array($name, $group, true)) {
                return $index;
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public static function mirrorAliases(array $filters): array
    {
        foreach (static::FILTER_ALIASES as $group) {
            $value = null;

            foreach ($group as $name) {
                if (! empty($filters[$name])) {
                    $value = $filters[$name];

                    break;
                }
            }

            foreach ($group as $name) {
                $filters[$name] = $value;
            }
        }

        return $filters;
    }

    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/pages/overview.navigation.title');
    }

    public function getTitle(): string|Htmlable
    {
        return __('invoices::filament/pages/overview.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Dashboard;
    }

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return null;
    }

    public function filtersForm(Schema $form): Schema
    {
        $sections = OverviewRegistry::visible();

        $extraFields = [];
        $shownGroups = [];

        // Every section's own filters, shown by default (user, 2026-11-11).
        // Filters that mean the same thing under different names appear once;
        // updatedFilters() copies the value to the other names.
        foreach ($sections as $section) {
            foreach (($section['filters'])() as $field) {
                $group = static::aliasGroupOf($field->getName());

                if ($group !== null) {
                    if (in_array($group, $shownGroups, true)) {
                        continue;
                    }

                    $shownGroups[] = $group;
                }

                $extraFields[] = $field;
            }
        }

        return $form->schema([
            DashboardFilters::make([
                ...DashboardDateRange::make(
                    __('invoices::filament/pages/overview.filters.date-range'),
                    'start_date',
                    'end_date',
                ),

                Select::make('product_id')
                    ->label(__('invoices::filament/pages/overview.filters.product'))
                    ->options(fn () => Product::pluck('name', 'id')->toArray())
                    ->multiple()
                    ->searchable()
                    ->placeholder(__('invoices::filament/pages/overview.filters.all-products'))
                    ->live(),

                Select::make('category_id')
                    ->label(__('invoices::filament/pages/overview.filters.category'))
                    ->options(fn () => Category::pluck('name', 'id')->toArray())
                    ->multiple()
                    ->searchable()
                    ->placeholder(__('invoices::filament/pages/overview.filters.all-categories'))
                    ->live(),

                ...$extraFields,
            ], [
                'default' => 1,
                'sm'      => 2,
                'md'      => 3,
                'xl'      => 4,
            ]),
        ]);
    }

    /**
     * Sections are tabs above the filters, not a filter (user, 2026-11-12,
     * matching the Accounting overview's journal tabs). One section or all;
     * the value stays a list in filters['sections'], so old dashboard links
     * (?section=) and getWidgets() are unchanged.
     */
    public function content(Schema $schema): Schema
    {
        return $schema->components([
            View::make('invoices::filament.pages.overview-sections')
                ->viewData(fn (): array => [
                    'sections' => array_map(fn (array $section): string => ($section['label'])(), OverviewRegistry::visible()),
                    'active'   => ((array) ($this->filters['sections'] ?? []))[0] ?? null,
                ]),
            $this->getFiltersFormContentComponent(),
            $this->getWidgetsContentComponent(),
        ]);
    }

    public function selectSection(?string $section = null): void
    {
        // Only a section this user may see; anything else means "All".
        $this->filters['sections'] = $section !== null && array_key_exists($section, OverviewRegistry::visible())
            ? [$section]
            : [];
    }

    /**
     * On phones only "At a glance" shows (user, 2026-11-11: the scroll was
     * too long; show what is needed). This button reveals the charts,
     * detailed stats and lists. Hidden on wider screens, where everything
     * shows (topbar CSS, `.dashboard-lists-toggle`).
     *
     * @return array<int, Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('toggleLists')
                ->label(__('support::filament/dashboard.show-details'))
                ->icon('heroicon-o-chart-bar')
                ->color('gray')
                ->extraAttributes(['class' => 'dashboard-lists-toggle'])
                ->alpineClickHandler("document.querySelectorAll('[data-dashboard-widgets]').forEach(el => el.classList.toggle('dashboard-show-lists'))"),
        ];
    }

    /**
     * Three charts across on wide screens (user, 2026-11-11: "Monthly Sales
     * is too big"). Full-width widgets (At a glance, stat blocks, lists) still
     * span the row.
     *
     * @return int|array<string, int>
     */
    public function getColumns(): int|array
    {
        return ['md' => 2, 'xl' => 3];
    }

    public function getWidgetsContentComponent(): Component
    {
        return parent::getWidgetsContentComponent()
            ->extraAttributes([
                'data-dashboard-widgets' => 'true',
                // Phone CSS hides everything but "At a glance" only on this
                // page, which has the Show details button to bring it back.
                'data-overview'          => 'true',
                'wire:loading.class'     => 'dashboard-widgets--loading',
                'wire:target'            => 'filters',
            ]);
    }

    /**
     * Most relevant first (user, 2026-11-11): the combined "At a glance"
     * figures, then every chosen section's charts, then their detailed stat
     * cards, then their lists. Grouped by kind across sections rather than
     * section by section, so the first screen shows all parts at once. Each
     * widget still applies its own canView().
     */
    public function getWidgets(): array
    {
        $sections = OverviewRegistry::visible();
        $chosen = array_values(array_intersect((array) ($this->filters['sections'] ?? []), array_keys($sections)));

        // "All" is a summary, not every module's charts stacked (user,
        // 2026-11-13, after Dribbble finance dashboards): the headline cards
        // with their trends, one money in/out chart, and Needs attention.
        // The module tabs keep the full sets. This also keeps a filter change
        // well under Livewire's per-request component limit: All used to
        // send 23 widgets in one update (Sentry DEKAERP-16).
        if ($chosen === [] && count($sections) > 1) {
            return [
                OverviewHeadlineWidget::class,
                ...(array_key_exists('invoices', $sections) ? [OverviewCashFlowChart::class] : []),
                OverviewAttentionWidget::class,
                // Every other module's key items (mockup approved 2026-11-13).
                // Only modules the company has and the user may see; with
                // none, the widget's canView() hides the whole section.
                OverviewModuleCards::class,
            ];
        }

        $charts = $stats = $tables = [];

        foreach ($sections as $key => $section) {
            if ($chosen !== [] && ! in_array($key, $chosen, true)) {
                continue;
            }

            foreach ($section['widgets'] as $widget) {
                match (true) {
                    is_subclass_of($widget, TableWidget::class)         => $tables[] = $widget,
                    is_subclass_of($widget, StatsOverviewWidget::class) => $stats[] = $widget,
                    default                                             => $charts[] = $widget,
                };
            }
        }

        return [OverviewHeadlineWidget::class, ...$charts, ...$stats, ...$tables];
    }
}
