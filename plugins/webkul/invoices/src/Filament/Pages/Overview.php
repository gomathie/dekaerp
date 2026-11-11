<?php

namespace Webkul\Invoice\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Invoice\Filament\Overview\OverviewRegistry;
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

    public function mount(): void
    {
        // An old dashboard link opens here with its section chosen.
        $section = request()->query('section');

        if (is_string($section) && array_key_exists($section, OverviewRegistry::visible())) {
            $this->filters = array_merge($this->filters ?? [], ['sections' => [$section]]);
        }
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

        // Each section's own filters, shown only while that section alone is
        // chosen: the sections name some filters differently, so mixing them
        // would filter one section and silently ignore the rest.
        foreach ($sections as $key => $section) {
            foreach (($section['filters'])() as $field) {
                $extraFields[] = $field->visible(fn (Get $get): bool => (array) $get('sections') === [$key]);
            }
        }

        return $form->schema([
            DashboardFilters::make([
                Select::make('sections')
                    ->label(__('invoices::filament/pages/overview.filters.sections'))
                    ->options(array_map(fn (array $section): string => ($section['label'])(), $sections))
                    ->multiple()
                    ->placeholder(__('invoices::filament/pages/overview.filters.all-sections'))
                    ->live(),

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
     * On phones the "Top" lists are hidden to keep the page short (user,
     * 2026-11-11); this button shows them. Hidden on wider screens, where
     * the lists always show (topbar CSS, `.dashboard-lists-toggle`).
     *
     * @return array<int, Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('toggleLists')
                ->label(__('support::filament/dashboard.show-lists'))
                ->icon('heroicon-o-list-bullet')
                ->color('gray')
                ->extraAttributes(['class' => 'dashboard-lists-toggle'])
                ->alpineClickHandler("document.querySelectorAll('[data-dashboard-widgets]').forEach(el => el.classList.toggle('dashboard-show-lists'))"),
        ];
    }

    public function getWidgetsContentComponent(): Component
    {
        return parent::getWidgetsContentComponent()
            ->extraAttributes([
                'data-dashboard-widgets' => 'true',
                'wire:loading.class'     => 'dashboard-widgets--loading',
                'wire:target'            => 'filters',
            ]);
    }

    /**
     * The chosen sections' widgets, or every visible section's when none is
     * chosen. Each widget still applies its own canView().
     */
    public function getWidgets(): array
    {
        $sections = OverviewRegistry::visible();
        $chosen = array_values(array_intersect((array) ($this->filters['sections'] ?? []), array_keys($sections)));

        $widgets = [];

        foreach ($sections as $key => $section) {
            if ($chosen === [] || in_array($key, $chosen, true)) {
                array_push($widgets, ...$section['widgets']);
            }
        }

        return $widgets;
    }
}
