<?php

namespace Webkul\Sale\Filament\Widgets;

use BackedEnum;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Account\Enums\MoveType;
use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource;
use Webkul\Sale\Filament\Widgets\Concerns\GuardsSaleDashboardWidget;
use Webkul\Sale\Filament\Widgets\Concerns\HasSaleDashboardFilters;
use Webkul\Sale\Models\Order;
use Webkul\Support\Filament\Concerns\HasDashboardEmptyState;

class TopSalesOrdersTable extends TableWidget
{
    // DB-4 layout on the Overview's six-column grid (2026-11-23).
    protected int|string|array $columnSpan = ['default' => 'full', 'xl' => 3];

    use GuardsSaleDashboardWidget, HasSaleDashboardFilters;
    use HasDashboardEmptyState;

    protected static ?int $sort = 5;

    protected static bool $isLazy = true;

    public function table(Table $table): Table
    {
        // DB-3 (2026-11-22): an empty list offers the one next step.
        return $this->withDashboardEmptyState($table, 'order', OrderResource::canCreate() ? OrderResource::getUrl('create') : null)
            ->defaultKeySort(false)
            ->recordClasses('dashboard-table-row')
            // "View all" opens the whole list (DB-9).
            ->headerActions($this->viewAllActions(OrderResource::canViewAny() ? OrderResource::getUrl('index') : null))
            // Invoice rows carry a negative id and are not orders: no order link.
            ->recordUrl(fn ($record): ?string => $record->id > 0 && OrderResource::canAccess()
                ? OrderResource::getUrl('view', ['record' => $record->id])
                : null);
    }

    protected function getTableHeading(): string|Htmlable|null
    {
        return __('sales::filament/widgets/sales-dashboard.top-sales-orders.heading');
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    protected function getTableQuery(): Builder
    {
        // The biggest sales: confirmed orders and invoices made without one
        // (user, 2026-11-20), both in company currency. Each part is already
        // company-scoped; the outer query drops the model's scopes, and is
        // named sales_orders so the partner relation still resolves.
        $orders = $this->saleOrders()
            ->toBase()
            ->selectRaw('sales_orders.id as id, sales_orders.name as name, sales_orders.partner_id as partner_id, '
                .'sales_orders.delivery_status as delivery_status, sales_orders.invoice_status as invoice_status, '
                .'CASE WHEN sales_orders.currency_rate > 0 THEN sales_orders.amount_total / sales_orders.currency_rate ELSE sales_orders.amount_total END as amount_total');

        $invoices = $this->directInvoices();

        $invoices = $invoices
            ->where($invoices->qualifyColumn('move_type'), MoveType::OUT_INVOICE)
            ->toBase()
            ->selectRaw('-accounts_account_moves.id as id, accounts_account_moves.name as name, accounts_account_moves.partner_id as partner_id, '
                ."NULL as delivery_status, 'invoiced' as invoice_status, accounts_account_moves.amount_total_signed as amount_total");

        return Order::query()
            ->withoutGlobalScopes()
            ->fromSub($orders->unionAll($invoices), 'sales_orders')
            ->select('sales_orders.*')
            ->with(['partner'])
            ->where('amount_total', '>', 0)
            ->orderByDesc('amount_total')
            ->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label(__('sales::filament/widgets/sales-dashboard.top-sales-orders.columns.reference'))
                ->weight(FontWeight::Bold)
                ->color('primary')
                ->description(fn ($record): string => $record->partner?->name ?: '—'),
            Tables\Columns\TextColumn::make('delivery_status')
                ->label(__('sales::filament/widgets/sales-dashboard.delivery-status'))
                ->badge()
                ->placeholder('-'),
            Tables\Columns\TextColumn::make('invoice_status')
                ->label(__('sales::filament/widgets/sales-dashboard.top-sales-orders.columns.invoice-status'))
                ->badge()
                ->formatStateUsing(fn ($state) => $state instanceof BackedEnum ? $state->getLabel() : $state)
                ->color(fn ($state): string => match ($state instanceof BackedEnum ? $state->value : $state) {
                    'invoiced'   => 'success',
                    'to_invoice' => 'warning',
                    default      => 'gray',
                }),
            Tables\Columns\TextColumn::make('amount_total')
                ->label(__('sales::filament/widgets/sales-dashboard.top-sales-orders.columns.amount'))
                ->money(fn () => current_company()?->currency?->name)
                ->weight(FontWeight::Bold)
                ->alignEnd(),
        ];
    }
}
