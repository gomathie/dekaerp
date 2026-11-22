<?php

use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Models\Move;
use Webkul\Partner\Models\Partner;
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
