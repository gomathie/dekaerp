<?php

namespace Webkul\Sale\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\MoveLine;

/**
 * Sales invoiced without a sales order: posted customer invoices and credit
 * notes that no order produced.
 *
 * User, 2026-11-20 (TRACE365 invoices without orders): the Sales dashboard
 * and the Sales by product report read confirmed orders only, so a sale
 * invoiced directly showed nowhere. They add these. An invoice made from an
 * order stays out: its order is already counted, and counting both would
 * double the sale. One definition, used by every sales figure.
 */
class DirectInvoices
{
    /**
     * A product line's amount in company currency. The invoice's
     * signed-to-unsigned ratio carries both its exchange rate and the
     * credit-note sign. Use with lines joined to their invoice as
     * `direct_moves`. Fixed SQL, never input.
     */
    public static function lineAmountSql(string $column): string
    {
        return "CASE WHEN direct_moves.amount_total <> 0 THEN accounts_account_move_lines.{$column} * direct_moves.amount_total_signed / direct_moves.amount_total ELSE 0 END";
    }

    public const LINE_QUANTITY_SQL = "CASE WHEN direct_moves.move_type = 'out_refund' THEN -accounts_account_move_lines.quantity ELSE accounts_account_move_lines.quantity END";

    /**
     * The invoices, through Move's own company and ownership scopes.
     */
    public static function invoices(): Builder
    {
        $query = static::postedInvoices();

        return $query->whereNotIn($query->qualifyColumn('id'), static::orderInvoiceIds(...));
    }

    /**
     * Every posted customer invoice and credit note, whether an order
     * produced it or not, through Move's own scopes.
     */
    public static function postedInvoices(): Builder
    {
        $query = Move::query();

        return $query
            ->whereIn($query->qualifyColumn('move_type'), [MoveType::OUT_INVOICE, MoveType::OUT_REFUND])
            ->where($query->qualifyColumn('state'), MoveState::POSTED);
    }

    /**
     * The lines an order does not already count, from the given posted
     * invoices: every line of an invoice made without an order, and every
     * line without a product of any invoice.
     *
     * Lines without a product (plan #9 R-5; user, 2026-11-25: "count, no
     * stock") never reach a sales order line: OrderFromInvoice copies
     * product lines only, and an order's own lines always carry a product.
     * So the invoice is the only place such a line is counted, even when an
     * order made it or was made from it; leaving it out with the rest of
     * that invoice would count it nowhere.
     */
    public static function soldLines(Builder $postedInvoices): Builder
    {
        return static::lines($postedInvoices)
            ->where(fn (Builder $query): Builder => $query
                ->whereNull('accounts_account_move_lines.product_id')
                ->orWhereNotIn('accounts_account_move_lines.move_id', static::orderInvoiceIds(...)));
    }

    /**
     * Lines without a product of the given invoices that an order produced or
     * was made from. Figures read per order (customers, salespeople) add
     * these, because the order's own total leaves them out (see soldLines()).
     */
    public static function orderInvoiceLinesWithoutProduct(Builder $postedInvoices): Builder
    {
        return static::lines($postedInvoices)
            ->whereNull('accounts_account_move_lines.product_id')
            ->whereIn('accounts_account_move_lines.move_id', static::orderInvoiceIds(...));
    }

    /**
     * Product-type lines (with or without a product) of the given invoices,
     * joined to them as `direct_moves`. Sections and notes are left out.
     */
    protected static function lines(Builder $invoices): Builder
    {
        return MoveLine::query()
            ->join('accounts_account_moves as direct_moves', 'direct_moves.id', '=', 'accounts_account_move_lines.move_id')
            ->whereIn('accounts_account_move_lines.move_id', $invoices->select($invoices->qualifyColumn('id')))
            ->where('accounts_account_move_lines.display_type', DisplayType::PRODUCT->value);
    }

    /**
     * Invoices attached to a sales order, made from one or one made from them.
     */
    protected static function orderInvoiceIds(QueryBuilder $query): QueryBuilder
    {
        return $query->select('sales_order_invoices.move_id')->from('sales_order_invoices');
    }
}
