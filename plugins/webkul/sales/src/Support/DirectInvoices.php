<?php

namespace Webkul\Sale\Support;

use Illuminate\Database\Eloquent\Builder;
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
        $query = Move::query();

        return $query
            ->whereIn($query->qualifyColumn('move_type'), [MoveType::OUT_INVOICE, MoveType::OUT_REFUND])
            ->where($query->qualifyColumn('state'), MoveState::POSTED)
            ->whereNotIn($query->qualifyColumn('id'), fn ($q) => $q->select('sales_order_invoices.move_id')->from('sales_order_invoices'));
    }

    /**
     * Product lines of the given invoices, joined to them as `direct_moves`.
     */
    public static function lines(Builder $invoices): Builder
    {
        return MoveLine::query()
            ->join('accounts_account_moves as direct_moves', 'direct_moves.id', '=', 'accounts_account_move_lines.move_id')
            ->whereIn('accounts_account_move_lines.move_id', $invoices->select($invoices->qualifyColumn('id')))
            ->where('accounts_account_move_lines.display_type', DisplayType::PRODUCT->value)
            ->whereNotNull('accounts_account_move_lines.product_id');
    }
}
