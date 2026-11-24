<?php

namespace Webkul\Sale\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Sale\Filament\Clusters\Products\Resources\ProductResource;
use Webkul\Sale\Filament\Widgets\Concerns\GuardsSaleDashboardWidget;
use Webkul\Sale\Filament\Widgets\Concerns\HasSaleDashboardFilters;
use Webkul\Sale\Models\OrderLine;
use Webkul\Support\Filament\Concerns\HasDashboardEmptyState;

class TopProductsTable extends TableWidget
{
    // DB-4 layout on the Overview's six-column grid (2026-11-23).
    protected int|string|array $columnSpan = ['default' => 'full', 'xl' => 3];

    use GuardsSaleDashboardWidget, HasDashboardEmptyState, HasSaleDashboardFilters;

    protected const BAR_COLOR = 'var(--primary-500)';

    protected const TRACK_COLOR = 'color-mix(in srgb, var(--gray-500) 22%, transparent)';

    protected static ?int $sort = 7;

    protected static bool $isLazy = true;

    protected ?float $peak = null;

    public function table(Table $table): Table
    {
        return $table
            ->defaultKeySort(false)
            ->recordClasses('dashboard-table-row')
            // "View all" opens the whole list (DB-9).
            ->headerActions($this->viewAllActions(ProductResource::canViewAny() ? ProductResource::getUrl('index') : null))
            ->recordUrl(fn ($record): ?string => $record->id && ProductResource::canAccess()
                ? ProductResource::getUrl('view', ['record' => $record->id])
                : null);
    }

    protected function getTableHeading(): string|Htmlable|null
    {
        return __('sales::filament/widgets/sales-dashboard.top-products.heading');
    }

    /**
     * Says what this counts (DB-2, 2026-11-23): sales value, not the
     * invoiced Revenue figure, so the two differ by orders not invoiced yet.
     */
    protected function getTableDescription(): string|Htmlable|null
    {
        return __('sales::filament/widgets/sales-dashboard.sales-value');
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    protected function getTableQuery(): Builder
    {
        // Orders and direct invoices together (productSaleRows). Each part
        // is already company-scoped, so the outer query drops the scopes
        // that would otherwise point at a table the subquery does not have.
        return OrderLine::query()
            ->withoutGlobalScopes()
            ->fromSub($this->productSaleRows(), 'sales_lines')
            ->leftJoin('products_products', 'products_products.id', '=', 'sales_lines.product_id')
            ->groupBy('products_products.id', 'products_products.name')
            ->selectRaw('COALESCE(products_products.id, 0) as id')
            ->addSelect('products_products.name as name')
            ->selectRaw('SUM(sales_lines.quantity) as quantity')
            ->selectRaw('SUM(sales_lines.revenue) as revenue')
            ->havingRaw('SUM(sales_lines.revenue) > 0')
            ->orderByDesc('revenue')
            ->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label(__('sales::filament/widgets/sales-dashboard.top-products.columns.name'))
                ->placeholder(__('sales::filament/widgets/sales-dashboard.unknown'))
                ->wrap(),
            Tables\Columns\TextColumn::make('share')
                ->label(__('sales::filament/widgets/sales-dashboard.share'))
                ->state(fn ($record): string => $this->shareBar((float) $record->revenue))
                ->html(),
            Tables\Columns\TextColumn::make('quantity')
                ->label(__('sales::filament/widgets/sales-dashboard.top-products.columns.quantity'))
                ->numeric()
                ->alignEnd(),
            Tables\Columns\TextColumn::make('revenue')
                ->label(__('sales::filament/widgets/sales-dashboard.top-products.columns.revenue'))
                ->formatStateUsing(fn ($state) => money($state ?? 0, current_company()?->currency?->name))
                ->alignEnd(),
        ];
    }

    protected function shareBar(float $revenue): string
    {
        $this->peak ??= (float) ($this->getTableQuery()->get()->max('revenue') ?? 0);

        $width = $this->peak > 0 ? max(2, min(100, (int) round($revenue / $this->peak * 100))) : 0;

        return '<span style="display:block;width:100%;height:6px;border-radius:3px;background:'.self::TRACK_COLOR.'">'
            .'<span style="display:block;width:'.$width.'%;height:6px;border-radius:3px;background:'.self::BAR_COLOR.'"></span>'
            .'</span>';
    }
}
