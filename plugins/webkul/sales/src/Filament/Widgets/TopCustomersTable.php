<?php

namespace Webkul\Sale\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Account\Enums\MoveType;
use Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource;
use Webkul\Sale\Filament\Widgets\Concerns\GuardsSaleDashboardWidget;
use Webkul\Sale\Filament\Widgets\Concerns\HasSaleDashboardFilters;
use Webkul\Sale\Models\Order;

class TopCustomersTable extends TableWidget
{
    // DB-4 layout on the Overview's six-column grid (2026-11-23).
    protected int|string|array $columnSpan = ['default' => 'full', 'xl' => 3];

    use GuardsSaleDashboardWidget, HasSaleDashboardFilters;

    protected const BAR_COLOR = 'var(--primary-500)';

    protected const TRACK_COLOR = 'color-mix(in srgb, var(--gray-500) 22%, transparent)';

    protected static ?int $sort = 6;

    protected static bool $isLazy = true;

    protected ?float $peak = null;

    public function table(Table $table): Table
    {
        return $table
            ->defaultKeySort(false)
            ->recordClasses('dashboard-table-row')
            ->recordUrl(fn ($record): ?string => $record->id && CustomerResource::canAccess()
                ? CustomerResource::getUrl('view', ['record' => $record->id])
                : null);
    }

    protected function getTableHeading(): string|Htmlable|null
    {
        return __('sales::filament/widgets/sales-dashboard.top-customers.heading');
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
        $orders = $this->saleOrders()
            ->toBase()
            ->selectRaw('sales_orders.partner_id as partner_id, 1 as is_sale, CASE WHEN sales_orders.currency_rate > 0 THEN sales_orders.amount_total / sales_orders.currency_rate ELSE sales_orders.amount_total END as revenue');

        // Invoices made without an order count for their customer too
        // (directInvoices()). Each invoice is one sale; a credit note lowers
        // the value but is not a sale. Counting it made the "Sales" column
        // add up to more than the Sales figure (DB-2, 2026-11-23).
        $invoices = $this->directInvoices();

        $invoices = $invoices->toBase()
            ->selectRaw($invoices->qualifyColumn('partner_id').' as partner_id, '
                ."CASE WHEN {$invoices->qualifyColumn('move_type')} = '".MoveType::OUT_INVOICE->value."' THEN 1 ELSE 0 END as is_sale, "
                .$invoices->qualifyColumn('amount_total_signed').' as revenue');

        // Both parts are already company-scoped; the outer query drops the
        // scopes that would point at a table the subquery does not have.
        return Order::query()
            ->withoutGlobalScopes()
            ->fromSub($orders->unionAll($invoices), 'sales_rows')
            ->leftJoin('partners_partners', 'partners_partners.id', '=', 'sales_rows.partner_id')
            ->groupBy('partners_partners.id', 'partners_partners.name')
            ->selectRaw('COALESCE(partners_partners.id, 0) as id')
            ->addSelect('partners_partners.name as name')
            ->selectRaw('SUM(sales_rows.is_sale) as orders_count')
            ->selectRaw('SUM(sales_rows.revenue) as revenue')
            ->havingRaw('SUM(sales_rows.revenue) > 0')
            ->orderByDesc('revenue')
            ->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label(__('sales::filament/widgets/sales-dashboard.top-customers.columns.name'))
                ->placeholder(__('sales::filament/widgets/sales-dashboard.unknown'))
                ->wrap(),
            Tables\Columns\TextColumn::make('share')
                ->label(__('sales::filament/widgets/sales-dashboard.share'))
                ->state(fn ($record): string => $this->shareBar((float) $record->revenue))
                ->html(),
            Tables\Columns\TextColumn::make('orders_count')
                ->label(__('sales::filament/widgets/sales-dashboard.top-customers.columns.orders'))
                ->numeric()
                ->alignEnd(),
            Tables\Columns\TextColumn::make('revenue')
                ->label(__('sales::filament/widgets/sales-dashboard.top-customers.columns.revenue'))
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
