<?php

namespace Webkul\Sale\Filament\Widgets\Concerns;

use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Carbon;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Models\Move;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Models\OrderLine;
use Webkul\Sale\Support\DirectInvoices;
use Webkul\Support\Filament\DashboardPeriod;

trait HasSaleDashboardFilters
{
    use InteractsWithPageFilters;

    /**
     * The page's filters, also read under the combined Overview's names.
     * The Overview shares one product, category and date filter across
     * sections, using the Invoices and Purchases names (product_id,
     * category_id, start_date, end_date); without this the Sales widgets
     * would ignore them there (2026-11-11).
     *
     * @return array<string, mixed>
     */
    protected function saleDashboardFilters(): array
    {
        $filters = $this->pageFilters ?? [];

        $filters['products'] = $filters['products'] ?? $filters['product_id'] ?? null;
        $filters['categories'] = $filters['categories'] ?? $filters['category_id'] ?? null;
        $filters['startDate'] = $filters['startDate'] ?? $filters['start_date'] ?? null;
        $filters['endDate'] = $filters['endDate'] ?? $filters['end_date'] ?? null;

        return $filters;
    }

    protected function applyFilters(Builder $query, ?Carbon $start = null, ?Carbon $end = null): Builder
    {
        $filters = $this->saleDashboardFilters();

        [$defaultStart, $defaultEnd] = $this->periodRange();

        $query->whereDate($query->qualifyColumn('date_order'), '>=', $start ?? $defaultStart);
        $query->whereDate($query->qualifyColumn('date_order'), '<=', $end ?? $defaultEnd);

        if (! empty($filters['countries'])) {
            $query->whereHas('partner', fn (Builder $q) => $q->whereIn('country_id', $filters['countries']));
        }

        if (! empty($filters['products'])) {
            $query->whereHas('lines', fn (Builder $q) => $q->whereIn('product_id', $filters['products']));
        }

        if (! empty($filters['categories'])) {
            $query->whereHas('lines.product', fn (Builder $q) => $q->whereIn('category_id', $filters['categories']));
        }

        if (! empty($filters['teams'])) {
            $query->whereIn($query->qualifyColumn('team_id'), $filters['teams']);
        }

        if (! empty($filters['salesPersons'])) {
            $query->whereIn($query->qualifyColumn('user_id'), $filters['salesPersons']);
        }

        return $query;
    }

    protected function applyConfirmedScope(Builder $query, ?Carbon $start = null, ?Carbon $end = null): Builder
    {
        return $this->applyFilters($query, $start, $end)
            ->where($query->qualifyColumn('state'), OrderState::SALE->value);
    }

    protected function saleOrders(?Carbon $start = null, ?Carbon $end = null): Builder
    {
        return $this->applyConfirmedScope(Order::query(), $start, $end);
    }

    /**
     * Invoiced sales: posted customer invoices less credit notes, whether or
     * not a sales order came first (DB-8, user, 2026-11-19). TRACE365 invoices
     * straight from Invoices, so revenue read from sales orders showed nothing
     * while invoices were being paid. Dated by invoice date; the dashboard
     * filters apply, a team through the orders the invoice came from.
     */
    protected function invoicedSales(?Carbon $start = null, ?Carbon $end = null): Builder
    {
        $filters = $this->saleDashboardFilters();

        [$defaultStart, $defaultEnd] = $this->periodRange();

        $query = Move::query();

        $query->whereIn($query->qualifyColumn('move_type'), [MoveType::OUT_INVOICE, MoveType::OUT_REFUND])
            ->where($query->qualifyColumn('state'), MoveState::POSTED)
            ->whereDate($query->qualifyColumn('invoice_date'), '>=', $start ?? $defaultStart)
            ->whereDate($query->qualifyColumn('invoice_date'), '<=', $end ?? $defaultEnd);

        if (! empty($filters['countries'])) {
            $query->whereHas('partner', fn (Builder $q) => $q->whereIn('country_id', $filters['countries']));
        }

        if (! empty($filters['products'])) {
            $query->whereHas('lines', fn (Builder $q) => $q->whereIn('product_id', $filters['products']));
        }

        if (! empty($filters['categories'])) {
            $query->whereHas('lines.product', fn (Builder $q) => $q->whereIn('category_id', $filters['categories']));
        }

        if (! empty($filters['salesPersons'])) {
            $query->whereIn($query->qualifyColumn('invoice_user_id'), $filters['salesPersons']);
        }

        if (! empty($filters['teams'])) {
            $query->whereIn($query->qualifyColumn('id'), fn ($q) => $q->select('sales_order_invoices.move_id')
                ->from('sales_order_invoices')
                ->join('sales_orders', 'sales_orders.id', '=', 'sales_order_invoices.order_id')
                ->whereIn('sales_orders.team_id', $filters['teams']));
        }

        return $query;
    }

    /**
     * Invoiced revenue in the company's currency (amount_total_signed is
     * already converted and negative on credit notes).
     */
    protected function invoicedRevenue(?Carbon $start = null, ?Carbon $end = null): float
    {
        $query = $this->invoicedSales($start, $end);

        return (float) $query->sum($query->qualifyColumn('amount_total_signed'));
    }

    protected function quotations(?Carbon $start = null, ?Carbon $end = null): Builder
    {
        $query = Order::query();

        return $this->applyFilters($query, $start, $end)
            ->whereIn($query->qualifyColumn('state'), [OrderState::DRAFT->value, OrderState::SENT->value]);
    }

    protected function confirmedLines(?Carbon $start = null, ?Carbon $end = null): Builder
    {
        return OrderLine::query()
            ->whereHas('order', fn (Builder $query) => $this->applyConfirmedScope($query, $start, $end));
    }

    /**
     * The invoiced sales of this period that no sales order produced
     * (DirectInvoices: why, and why order invoices stay out).
     */
    protected function directInvoices(?Carbon $start = null, ?Carbon $end = null): Builder
    {
        $query = $this->invoicedSales($start, $end);

        return $query->whereIn($query->qualifyColumn('id'), DirectInvoices::invoices()->select('accounts_account_moves.id'));
    }

    /**
     * How many sales the period had: confirmed orders plus invoices made
     * without one (credit notes are not sales). User, 2026-11-20: "all
     * products sold and paid by invoices created directly should reflect in
     * sales" - every count of sales reads this, not the order count alone.
     */
    protected function salesCount(?Carbon $start = null, ?Carbon $end = null): int
    {
        $invoices = $this->directInvoices($start, $end);

        return $this->saleOrders($start, $end)->count()
            + $invoices->where($invoices->qualifyColumn('move_type'), MoveType::OUT_INVOICE)->count();
    }

    /**
     * Sales per day over the period (orders by order date, direct invoices by
     * invoice date), keyed Y-m-d.
     *
     * @return array<string, int>
     */
    protected function dailySalesCounts(?Carbon $start = null, ?Carbon $end = null): array
    {
        $orders = $this->saleOrders($start, $end);
        $orderDay = 'CAST('.$orders->qualifyColumn('date_order').' AS date)';

        $invoices = $this->directInvoices($start, $end);
        $invoiceDay = 'CAST('.$invoices->qualifyColumn('invoice_date').' AS date)';

        $counts = [];

        foreach ([
            [$orders, $orderDay],
            [$invoices->where($invoices->qualifyColumn('move_type'), MoveType::OUT_INVOICE), $invoiceDay],
        ] as [$query, $day]) {
            foreach ($query->toBase()->selectRaw("{$day} as day, COUNT(*) as total")->groupByRaw($day)->pluck('total', 'day') as $date => $total) {
                $key = Carbon::parse($date)->toDateString();
                $counts[$key] = ($counts[$key] ?? 0) + (int) $total;
            }
        }

        ksort($counts);

        return $counts;
    }

    /**
     * Product lines of directInvoices(), joined to their invoice as
     * `direct_moves`, with the product and category filters.
     */
    protected function directInvoiceLines(?Carbon $start = null, ?Carbon $end = null): Builder
    {
        $filters = $this->saleDashboardFilters();

        $query = DirectInvoices::lines($this->directInvoices($start, $end));

        if (! empty($filters['products'])) {
            $query->whereIn('accounts_account_move_lines.product_id', $filters['products']);
        }

        if (! empty($filters['categories'])) {
            $query->whereHas('product', fn (Builder $q) => $q->whereIn('category_id', $filters['categories']));
        }

        return $query;
    }

    /**
     * One row per sold product line, from confirmed orders and from direct
     * invoices: product_id, quantity and revenue (tax included, company
     * currency), ready to group.
     */
    protected function productSaleRows(): QueryBuilder
    {
        $orderLines = $this->confirmedLines()
            ->leftJoin('sales_orders', 'sales_orders.id', '=', 'sales_order_lines.order_id')
            ->toBase()
            ->selectRaw('sales_order_lines.product_id as product_id, sales_order_lines.product_uom_qty as quantity, '
                .'CASE WHEN sales_orders.currency_rate > 0 THEN sales_order_lines.price_total / sales_orders.currency_rate ELSE sales_order_lines.price_total END as revenue');

        $invoiceLines = $this->directInvoiceLines()
            ->toBase()
            ->selectRaw('accounts_account_move_lines.product_id as product_id, '.DirectInvoices::LINE_QUANTITY_SQL.' as quantity, '.DirectInvoices::lineAmountSql('price_total').' as revenue');

        return $orderLines->unionAll($invoiceLines);
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function periodRange(): array
    {
        $filters = $this->saleDashboardFilters();

        // No period chosen: the year to date, like every dashboard (U68).
        // It was the whole calendar year, future months included.
        [$defaultStart, $defaultEnd] = DashboardPeriod::defaultRange();

        $start = ! empty($filters['startDate'])
            ? Carbon::parse($filters['startDate'])->startOfDay()
            : $defaultStart;

        $end = ! empty($filters['endDate'])
            ? Carbon::parse($filters['endDate'])->endOfDay()
            : $defaultEnd;

        return [$start, $end];
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function previousPeriodRange(): array
    {
        [$start, $end] = $this->periodRange();

        return DashboardPeriod::previous($start, $end);
    }
}
