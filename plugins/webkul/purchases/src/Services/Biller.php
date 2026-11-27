<?php

namespace Webkul\Purchase\Services;

use Illuminate\Support\Carbon;
use Webkul\Account\Enums as AccountEnums;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Inventory\Enums\OperationState;
use Webkul\PluginManager\Package;
use Webkul\Purchase\Models\AccountMove;
use Webkul\Purchase\Models\Order;
use Webkul\Purchase\Support\PurchasesAccess;

class Biller
{
    public function createBill(Order $record): void
    {
        // Revoking Purchases for a company (SAAS-05) must stop its billing too,
        // not only hide the screens: a job or another plugin can call this
        // directly, and the super-admin Gate::before skips the UI's policies.
        PurchasesAccess::assertForCompany((int) $record->company_id);

        // The goods-receipt date (plan 10 D4, user 2026-11-27). Without a
        // receipt the bill is dated today, so it can still be confirmed.
        $billDate = $this->billDate($record)->toDateString();

        $accountMove = AccountMove::create([
            'move_type'               => $record->qty_to_invoice >= 0
                ? AccountEnums\MoveType::IN_INVOICE
                : AccountEnums\MoveType::IN_REFUND,
            'invoice_origin'          => $record->name,
            'invoice_date'            => $billDate,
            'date'                    => $billDate,
            'company_id'              => $record->company_id,
            'currency_id'             => $record->currency_id,
            'invoice_payment_term_id' => $record->payment_term_id,
            'partner_id'              => $record->partner_id,
            'fiscal_position_id'      => $record->fiscal_position_id,
        ]);

        $record->accountMoves()->attach($accountMove->id);

        foreach ($record->lines as $line) {
            $this->createBillLine($accountMove, $line);
        }

        AccountFacade::computeAccountMove($accountMove);
    }

    /**
     * The latest done receipt. No receipt, or Inventory is not installed:
     * today. The date is this company's receipt, read through the order.
     */
    protected function billDate(Order $record): Carbon
    {
        if (! Package::isPluginInstalled('inventories')) {
            return now();
        }

        $closedAt = $record->operations()
            ->where('state', OperationState::DONE)
            ->orderByDesc('closed_at')
            ->value('closed_at');

        return $closedAt instanceof \DateTimeInterface ? Carbon::parse($closedAt) : now();
    }

    public function createBillLine($accountMove, $orderLine): void
    {
        $accountMoveLine = $accountMove->lines()->create([
            'state'                  => $accountMove->state,
            'name'                   => $orderLine->name,
            'date'                   => $accountMove->date,
            'parent_state'           => $accountMove->state,
            'quantity'               => abs($orderLine->qty_to_invoice),
            'price_unit'             => $orderLine->price_unit,
            'discount'               => $orderLine->discount,
            'company_id'             => $accountMove->company_id,
            'currency_id'            => $accountMove->currency_id,
            'company_currency_id'    => $accountMove->currency_id,
            'partner_id'             => $accountMove->partner_id,
            'product_id'             => $orderLine->product_id,
            'uom_id'                 => $orderLine->uom_id,
            'purchase_order_line_id' => $orderLine->id,
        ]);

        $accountMoveLine->taxes()->sync($orderLine->taxes->pluck('id'));
    }
}
