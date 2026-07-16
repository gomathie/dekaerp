<?php

namespace Webkul\Sale\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\PaymentRegister;
use Webkul\Accounting\Models\Journal;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Facades\Inventory as InventoryFacade;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Models\Partner;
use Webkul\PluginManager\Package;
use Webkul\Sale\Enums\AdvancedPayment;
use Webkul\Sale\Facades\SaleOrder as SaleFacade;
use Webkul\Sale\Models\Order;

/**
 * DirectSaleManager is a thin orchestration layer on top of the existing
 * Sale Order -> Delivery -> Invoice -> Payment pipeline.
 *
 * It does NOT introduce a separate sales engine. Every step delegates to the
 * existing managers (SaleManager, InventoryManager, AccountManager) so the
 * transaction is recorded exactly like a standard confirmed, delivered, paid
 * sale order and therefore flows into all existing accounting, inventory and
 * reporting features automatically.
 */
class DirectSaleManager
{
    /**
     * Run the full direct sale pipeline inside a single database transaction.
     *
     * @param  Order  $order         A freshly created DRAFT sale order (with lines).
     * @param  array  $paymentData   Optional: ['amount' => float, 'journal_id' => int, 'payment_date' => string, 'communication' => string]
     * @return Move   The posted customer invoice that backs this sale.
     */
    public function process(Order $order, array $paymentData = []): Move
    {
        return DB::transaction(function () use ($order, $paymentData) {
            // 1. Confirm the quotation -> becomes a sale order, generates the
            //    delivery operation(s) and flags lines "to invoice".
            $order = SaleFacade::confirmSaleOrder($order);

            // 2. Validate the delivery using the existing inventory flow so
            //    stock is actually reduced (confirm -> reserve -> done).
            $this->validateDeliveries($order);

            // 3. Create the customer invoice (account move) for the order.
            SaleFacade::createInvoice($order, [
                'advance_payment_method' => AdvancedPayment::DELIVERED->value,
            ]);

            $order->refresh();

            // 4. Retrieve and post (confirm) the invoice.
            $invoice = $order->accountMoves()->latest('id')->first();

            if (! $invoice) {
                throw new \Exception('The direct sale invoice could not be created. Please verify the product invoicing policy and quantities.');
            }

            $invoice->checked = (bool) ($invoice->journal?->auto_check_on_post ?? false);

            $invoice = AccountFacade::confirmMove($invoice);

            // 5. Register the payment (full or partial) against the invoice.
            $this->registerPayment($invoice, $paymentData);

            return $invoice->refresh();
        });
    }

    /**
     * Validate every pending delivery operation attached to the order using the
     * exact same manager methods the inventory UI uses (Todo + Validate).
     */
    protected function validateDeliveries(Order $order): void
    {
        if (! Package::isPluginInstalled('inventories')) {
            return;
        }

        $order->load('operations.moves');

        foreach ($order->operations as $operation) {
            if (in_array($operation->state, [OperationState::DONE, OperationState::CANCELED])) {
                continue;
            }

            if ($operation->state === OperationState::DRAFT) {
                $operation = InventoryFacade::confirmTransfer($operation);
            }

            // Reserve available stock. assignTransfer throws when there are no
            // assignable moves (e.g. nothing to reserve); that is non-fatal for
            // a cashier sale, the delivery is still validated below.
            try {
                $operation = InventoryFacade::assignTransfer($operation);
            } catch (\Throwable $e) {
                // No moves available to reserve - continue to validation.
            }

            $operation->refresh();

            // Validate the transfer (mark done) and never leave a backorder for
            // a point-of-sale style direct sale.
            InventoryFacade::doneTransfer($operation, true);
        }
    }

    /**
     * Register a customer payment against the posted invoice, reusing the same
     * PaymentRegister + AccountManager::createPayments() flow as the Pay action.
     */
    public function registerPayment(Move $invoice, array $paymentData = []): void
    {
        $lineIds = $invoice->paymentTermLines
            ->filter(fn ($line) => ! $line->reconciled)
            ->pluck('id')
            ->toArray();

        if (empty($lineIds)) {
            return;
        }

        $defaults = $this->computePaymentDefaults($invoice);

        if ($defaults['journal_id'] === null) {
            throw new \Exception('No payment journal is available to record this sale. Please configure a bank/cash journal.');
        }

        $data = [
            'journal_id'             => $paymentData['journal_id'] ?? $defaults['journal_id'],
            'payment_method_line_id' => $paymentData['payment_method_line_id'] ?? $defaults['payment_method_line_id'],
            'amount'                 => $paymentData['amount'] ?? $defaults['amount'],
            'currency_id'            => $invoice->currency_id,
            'payment_date'           => $paymentData['payment_date'] ?? now(),
            'communication'          => $paymentData['communication'] ?? $invoice->name,
        ];

        $paymentRegister = PaymentRegister::create($data);

        $paymentRegister->lines()->sync($lineIds);

        $paymentRegister->refresh();

        $paymentRegister->computeFromLines();

        $paymentRegister->save();

        AccountFacade::createPayments($paymentRegister);
    }

    /**
     * Compute the default journal, payment method line and full amount due for
     * the invoice, mirroring the logic in InvoiceResource\Actions\PayAction.
     *
     * @return array{journal_id: ?int, payment_method_line_id: ?int, amount: float}
     */
    protected function computePaymentDefaults(Move $invoice): array
    {
        $register = new PaymentRegister;

        $register->lines = $invoice->lines;
        $register->company = $invoice->company;
        $register->currency = $invoice->currency;
        $register->currency_id = $invoice->currency_id;
        $register->payment_type = $invoice->isInbound(true)
            ? PaymentType::RECEIVE
            : PaymentType::SEND;

        $register->computeBatches();
        $register->computeAvailableJournalIds();

        $register->journal_id = $register->available_journal_ids[0] ?? null;
        $register->journal = $register->journal_id ? Journal::find($register->journal_id) : null;

        $register->computePaymentMethodLineId();

        $amounts = $register->getTotalAmountsToPay($register->batches);

        return [
            'journal_id'             => $register->journal_id,
            'payment_method_line_id' => $register->payment_method_line_id,
            'amount'                 => $amounts['amount_by_default'] ?? 0.0,
        ];
    }

    /**
     * Resolve (or lazily create) the default "Walk-In Customer" partner used
     * when a cashier does not select a specific customer. Keeping it as a real
     * partner preserves customer reporting.
     */
    public static function resolveWalkInCustomer(): Partner
    {
        $authUser = Auth::user();

        return Partner::firstOrCreate(
            ['name' => 'Walk-In Customer', 'sub_type' => 'customer'],
            [
                'account_type' => AccountType::INDIVIDUAL,
                'company_id'   => $authUser?->default_company_id,
                'creator_id'   => $authUser?->id,
                'user_id'      => $authUser?->id,
            ],
        );
    }
}
