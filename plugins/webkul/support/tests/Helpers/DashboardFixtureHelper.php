<?php

use Illuminate\Support\Facades\DB;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Models\Move;
use Webkul\Partner\Models\Partner;
use Webkul\Product\Models\Category;
use Webkul\Product\Models\Product;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Models\OrderLine;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

require_once __DIR__.'/CompanyHelper.php';

/**
 * Dashboard plan DB-0/DB-2 (2026-11-23): one fixed dataset for two companies
 * with known totals, so a test can assert that a dashboard's tile, chart and
 * table agree, and that nothing from the other company, a draft or another
 * period leaks in. Needs the accounts plugin installed.
 *
 * Every row is written with nobody acting: the cross-company write guard
 * (UF-025) would refuse the acting user creating the other company's rows.
 * Amounts are written straight to the move, because these tests are about
 * what the dashboards read, not how a move is costed.
 */
class DashboardFixtureHelper
{
    /** The period every known total below is for. */
    public const START = '2026-03-01';

    public const END = '2026-03-31';

    /**
     * Customer invoices in March 2026 for $own:
     *
     *   INV-A  1,000  paid      Customer X  salesperson A  2 Mar
     *   INV-B    600  partial   Customer Y  salesperson B  3 Mar
     *   INV-C    400  not_paid  Customer X  salesperson A  4 Mar
     *   RINV     200  credit note for Customer X, salesperson A, 5 Mar
     *
     * and traps that must never count: a draft of 999 in March, a paid
     * invoice of 5,000 in February, and $other's paid invoice of 9,000 in
     * March.
     *
     * Known totals for March: invoiced net 1,800; 3 invoices; 1 paid, 2
     * unpaid (partial counts as unpaid); by state paid 1,000, partial 600,
     * not paid 400 (gross 2,000); by customer X 1,200, Y 600; by salesperson
     * A 1,200, B 600; paid per day 1,000 on 2 Mar.
     *
     * @return array{customerX: Partner, customerY: Partner, salespersonA: User, salespersonB: User, invoices: array<string, Move>}
     */
    public static function invoices(Company $own, Company $other): array
    {
        return CompanyHelper::asNobody(function () use ($own, $other): array {
            $customerX = Partner::factory()->create(['company_id' => $own->getKey(), 'name' => 'Customer X']);
            $customerY = Partner::factory()->create(['company_id' => $own->getKey(), 'name' => 'Customer Y']);
            $outsider = Partner::factory()->create(['company_id' => $other->getKey(), 'name' => 'Other client customer']);

            $salespersonA = User::factory()->create(['name' => 'Salesperson A']);
            $salespersonB = User::factory()->create(['name' => 'Salesperson B']);

            $invoices = [
                'A'        => static::move($own, $customerX, $salespersonA, 1000, '2026-03-02', 'paid'),
                'B'        => static::move($own, $customerY, $salespersonB, 600, '2026-03-03', 'partial'),
                'C'        => static::move($own, $customerX, $salespersonA, 400, '2026-03-04', 'not_paid'),
                'credit'   => static::move($own, $customerX, $salespersonA, 200, '2026-03-05', 'paid', MoveType::OUT_REFUND),
                'draft'    => static::move($own, $customerY, $salespersonB, 999, '2026-03-06', 'not_paid', state: MoveState::DRAFT),
                'february' => static::move($own, $customerY, $salespersonB, 5000, '2026-02-15', 'paid'),
                'other'    => static::move($other, $outsider, $salespersonA, 9000, '2026-03-02', 'paid'),
            ];

            return compact('customerX', 'customerY', 'salespersonA', 'salespersonB', 'invoices');
        });
    }

    /**
     * Sales in March 2026 for $own (needs the sales plugin):
     *
     *   SO-1   confirmed 1,000  Customer X  salesperson A  2 Mar  Goods 10
     *          invoiced by INV-1, 1,000 on 10 Mar
     *   SO-2   confirmed   500  Customer Y  salesperson B  3 Mar  Services 5
     *          not invoiced yet
     *   DIR-1  invoice without an order, 300, X, A, 4 Mar, Goods 3
     *   DIR-R  credit note without an order, 100, Y, B, 5 Mar, Services 1
     *   QUO-1  quotation sent, 700, Y, B, 6 Mar
     *
     * and traps that must never count: a cancelled order of 999 in March, a
     * confirmed order of 5,000 in February, a draft direct invoice of 888,
     * and $other's confirmed order and direct invoice of 9,000 each.
     *
     * Known totals for March. Revenue (invoiced): 1,200 = INV-1 + DIR-1 -
     * DIR-R. Sales: 3 (SO-1, SO-2, DIR-1; a credit note is not a sale).
     * Sales value (orders and invoices without one): 1,700; by customer X
     * 1,300 in 2 sales, Y 400 in 1; by salesperson A 1,300, B 400; by
     * category Goods 1,300, Services 400; top products Goods 1,300 (13),
     * Services 400 (4). Sales value less revenue is 500: SO-2, not invoiced.
     * Open quotations: 1.
     *
     * @return array{orders: array<string, Order>, invoices: array<string, Move>}
     */
    public static function sales(Company $own, Company $other): array
    {
        return CompanyHelper::asNobody(function () use ($own, $other): array {
            $customerX = Partner::factory()->create(['company_id' => $own->getKey(), 'name' => 'Customer X']);
            $customerY = Partner::factory()->create(['company_id' => $own->getKey(), 'name' => 'Customer Y']);
            $outsider = Partner::factory()->create(['company_id' => $other->getKey(), 'name' => 'Other client customer']);

            $salespersonA = User::factory()->create(['name' => 'Salesperson A']);
            $salespersonB = User::factory()->create(['name' => 'Salesperson B']);

            $goods = Product::factory()->create([
                'company_id'  => $own->getKey(),
                'name'        => 'Goods',
                'category_id' => Category::factory()->create(['company_id' => $own->getKey(), 'name' => 'Goods category'])->getKey(),
            ]);
            $services = Product::factory()->create([
                'company_id'  => $own->getKey(),
                'name'        => 'Services',
                'category_id' => Category::factory()->create(['company_id' => $own->getKey(), 'name' => 'Services category'])->getKey(),
            ]);
            $outsiderProduct = Product::factory()->create(['company_id' => $other->getKey()]);

            $orders = [
                'SO-1'      => static::order($own, $customerX, $salespersonA, OrderState::SALE, '2026-03-02', $goods, 10, 1000),
                'SO-2'      => static::order($own, $customerY, $salespersonB, OrderState::SALE, '2026-03-03', $services, 5, 500),
                'QUO-1'     => static::order($own, $customerY, $salespersonB, OrderState::SENT, '2026-03-06', $goods, 7, 700),
                'cancelled' => static::order($own, $customerX, $salespersonA, OrderState::CANCEL, '2026-03-07', $goods, 9, 999),
                'february'  => static::order($own, $customerY, $salespersonB, OrderState::SALE, '2026-02-15', $goods, 50, 5000),
                'other'     => static::order($other, $outsider, $salespersonA, OrderState::SALE, '2026-03-02', $outsiderProduct, 90, 9000),
            ];

            $invoices = [
                'INV-1'       => static::move($own, $customerX, $salespersonA, 1000, '2026-03-10', 'not_paid'),
                'DIR-1'       => static::move($own, $customerX, $salespersonA, 300, '2026-03-04', 'not_paid'),
                'DIR-R'       => static::move($own, $customerY, $salespersonB, 100, '2026-03-05', 'paid', MoveType::OUT_REFUND),
                'draft'       => static::move($own, $customerY, $salespersonB, 888, '2026-03-08', 'not_paid', state: MoveState::DRAFT),
                'other'       => static::move($other, $outsider, $salespersonA, 9000, '2026-03-02', 'paid'),
            ];

            // INV-1 was made from SO-1: the order is what Sales counts.
            DB::table('sales_order_invoices')->insert(['order_id' => $orders['SO-1']->getKey(), 'move_id' => $invoices['INV-1']->getKey()]);

            static::invoiceLine($invoices['DIR-1'], $goods, 3, 300);
            static::invoiceLine($invoices['DIR-R'], $services, 1, 100);
            static::invoiceLine($invoices['draft'], $goods, 8, 888);
            static::invoiceLine($invoices['other'], $outsiderProduct, 90, 9000);

            return compact('orders', 'invoices');
        });
    }

    protected static function order(Company $company, Partner $partner, User $salesperson, OrderState $state, string $date, Product $product, float $quantity, float $amount): Order
    {
        $order = Order::factory()->create(['company_id' => $company->getKey(), 'partner_id' => $partner->getKey()]);

        $line = OrderLine::factory()->create(['order_id' => $order->getKey(), 'product_id' => $product->getKey(), 'company_id' => $company->getKey()]);

        // Written after the line, so nothing recomputes the order's total.
        OrderLine::withoutGlobalScopes()->whereKey($line->getKey())->update([
            'state'           => $state,
            'product_id'      => $product->getKey(),
            'product_uom_qty' => $quantity,
            'price_total'     => $amount,
        ]);

        Order::withoutGlobalScopes()->whereKey($order->getKey())->update([
            'partner_id'    => $partner->getKey(),
            'user_id'       => $salesperson->getKey(),
            'state'         => $state,
            'date_order'    => $date,
            'currency_rate' => 1,
            'amount_total'  => $amount,
        ]);

        return Order::withoutGlobalScopes()->findOrFail($order->getKey());
    }

    protected static function invoiceLine(Move $move, Product $product, float $quantity, float $amount): void
    {
        DB::table('accounts_account_move_lines')->insert([
            'move_id'      => $move->getKey(),
            'company_id'   => $move->company_id,
            'currency_id'  => $move->currency_id,
            'product_id'   => $product->getKey(),
            'display_type' => DisplayType::PRODUCT->value,
            'quantity'     => $quantity,
            'price_total'  => $amount,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }

    protected static function move(
        Company $company,
        Partner $partner,
        User $salesperson,
        float $amount,
        string $date,
        string $paymentState,
        MoveType $type = MoveType::OUT_INVOICE,
        MoveState $state = MoveState::POSTED,
    ): Move {
        $move = Move::factory()->create(['company_id' => $company->getKey()]);

        // A credit note's signed amount is negative, as the real ones are.
        $signed = $type === MoveType::OUT_REFUND ? -$amount : $amount;

        Move::withoutGlobalScopes()->whereKey($move->getKey())->update([
            'company_id'             => $company->getKey(),
            'partner_id'             => $partner->getKey(),
            'invoice_user_id'        => $salesperson->getKey(),
            'move_type'              => $type,
            'state'                  => $state,
            'payment_state'          => $paymentState,
            'invoice_date'           => $date,
            'invoice_date_due'       => $date,
            'amount_total'           => $amount,
            'amount_total_signed'    => $signed,
            'amount_residual'        => $paymentState === 'paid' ? 0 : $amount,
            'amount_residual_signed' => $paymentState === 'paid' ? 0 : $signed,
        ]);

        return Move::withoutGlobalScopes()->findOrFail($move->getKey());
    }
}
