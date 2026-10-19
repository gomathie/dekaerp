<?php

namespace Webkul\Logistics\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Webkul\Account\Enums as AccountEnums;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Move as AccountMove;
use Webkul\Logistics\Exceptions\NothingToInvoice;
use Webkul\Logistics\Models\CompanySetting;
use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Models\ShipmentCharge;
use Webkul\Logistics\Support\LogisticsAccess;
use Webkul\Product\Models\Product;

/**
 * Turns a shipment's billable charges into a customer invoice.
 *
 * This deliberately builds nothing of its own: it creates the same
 * Account\Models\Move that Sales creates, in the same shape, and hands it to
 * AccountFacade::computeAccountMove() for totals and taxes. Logistics must not
 * grow a second invoice system - the accounting module owns invoices, their
 * numbering, their journals and their payment state.
 *
 * Follows plugins/webkul/sales/src/Services/Invoicer.php. If that file changes,
 * re-read it before changing this one.
 */
class ShipmentInvoicer
{
    /**
     * Invoice every billable charge that has not been invoiced yet.
     *
     * Re-running it only picks up charges added since the last invoice, because
     * each invoiced charge keeps the move line it produced.
     */
    public function createInvoice(Shipment $shipment): AccountMove
    {
        LogisticsAccess::ensureEnabled((int) $shipment->company_id);

        $this->authorize($shipment);

        // Re-read under the company scope before anything else. The policy
        // grants on permission plus the per-company switch, so a shipment
        // obtained another way - held from before the active company changed,
        // or passed straight in - would otherwise pass authorisation. Doing
        // this first also keeps the error honest: loading charges first would
        // find none (they are scoped out too) and report "nothing to invoice"
        // for a shipment that simply is not ours.
        $shipment = Shipment::query()->whereKey($shipment->getKey())->firstOrFail();

        $charges = $shipment->charges()->uninvoiced()->orderBy('sort')->get();

        if ($charges->isEmpty()) {
            throw NothingToInvoice::forShipment((string) $shipment->name);
        }

        return DB::transaction(function () use ($shipment, $charges): AccountMove {
            // Locked for the write, as DeliveryService does.
            $shipment = Shipment::query()
                ->whereKey($shipment->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $accountMove = AccountMove::create([
                'move_type'      => AccountEnums\MoveType::OUT_INVOICE,
                'invoice_origin' => $shipment->name,
                'date'           => now(),
                'company_id'     => $shipment->company_id,
                // Falls back to the company's currency when the shipment has
                // none. Move::computeCurrencyId() runs before
                // computeJournalId() in the saving hook, so a move created with
                // neither currency nor journal dereferences a null journal and
                // fails. Supplying the currency lets accounting pick its own
                // journal, which is the module's job, not this one's.
                'currency_id'    => $shipment->currency_id ?? $shipment->company?->currency_id,
                'partner_id'     => $shipment->customer_id,
                'creator_id'     => Auth::id(),
            ]);

            $shipment->invoices()->attach($accountMove->id);

            foreach ($charges as $charge) {
                $this->createInvoiceLine($accountMove, $charge);
            }

            // Totals, taxes and the move's own state are the accounting
            // module's job. Never compute them here.
            return AccountFacade::computeAccountMove($accountMove);
        });
    }

    public function createInvoiceLine(AccountMove $accountMove, ShipmentCharge $charge): void
    {
        $accountMoveLine = $accountMove->lines()->create([
            'name'         => $charge->description,
            'date'         => $accountMove->date,
            'creator_id'   => $accountMove->creator_id,
            'parent_state' => $accountMove->state,
            'quantity'     => $charge->quantity,
            'price_unit'   => $charge->price_unit,
            'discount'     => $charge->discount,
            'currency_id'  => $accountMove->currency_id,
            'product_id'   => $charge->product_id,
            'uom_id'       => $charge->uom_id,
        ]);

        // The charge keeps its line, which is what makes a second invoice pick
        // up only new charges (see ShipmentCharge::scopeUninvoiced).
        $charge->forceFill(['move_line_id' => $accountMoveLine->id])->save();

        // Existing Tax records only: the charge's taxes are copied, never
        // invented, so the invoice computes exactly what Accounting would.
        $accountMoveLine->taxes()->sync($charge->taxes->pluck('id'));
    }

    /**
     * Detention the customer could be billed for, as suggestions only.
     *
     * A stop that sat longer than the company's free waiting time produces one
     * suggestion for the excess. Nothing is created here: D14 says waiting time
     * is billed only when a person confirms it, because a long wait is often
     * the carrier's own fault and auto-billing it would put invented charges on
     * real customer invoices. The caller turns a confirmed suggestion into a
     * charge with addWaitingTimeCharge().
     *
     * @return array<int, array{stop_id: int, minutes: int, billable_minutes: int, description: string}>
     */
    public function waitingTimeSuggestions(Shipment $shipment): array
    {
        $freeMinutes = (int) CompanySetting::forCompany((int) $shipment->company_id)->free_waiting_minutes;

        $suggestions = [];

        foreach ($shipment->stops()->whereNotNull('actual_arrival_at')->whereNotNull('actual_departure_at')->get() as $stop) {
            $minutes = (int) $stop->actual_arrival_at->diffInMinutes($stop->actual_departure_at);

            $billable = $minutes - $freeMinutes;

            if ($billable <= 0) {
                continue;
            }

            $suggestions[] = [
                'stop_id'          => (int) $stop->getKey(),
                'minutes'          => $minutes,
                'billable_minutes' => $billable,
                'description'      => __('logistics::invoicing.waiting-time.description', [
                    'minutes' => $billable,
                    'stop'    => $stop->type?->getLabel() ?? (string) $stop->getKey(),
                ]),
            ];
        }

        return $suggestions;
    }

    /**
     * Create the waiting-time charge a user confirmed.
     *
     * @param  array{stop_id: int, billable_minutes: int, description: string}  $suggestion
     */
    public function addWaitingTimeCharge(Shipment $shipment, array $suggestion): ShipmentCharge
    {
        LogisticsAccess::ensureEnabled((int) $shipment->company_id);

        $this->authorize($shipment);

        $product = Product::withoutGlobalScopes()
            ->where('company_id', $shipment->company_id)
            ->where('reference', 'LOG-WAITING')
            ->first();

        return $shipment->charges()->create([
            // company_id is left to InheritsParentCompany: the charge takes the
            // shipment's company, never the session's.
            'description' => $suggestion['description'],
            'quantity'    => round($suggestion['billable_minutes'] / 60, 2),
            'price_unit'  => $product?->price ?? 0,
            'is_billable' => true,
            'product_id'  => $product?->id,
            'uom_id'      => $product?->uom_id,
            'currency_id' => $shipment->currency_id,
        ]);
    }

    protected function authorize(Shipment $shipment): void
    {
        if (! Auth::check()) {
            return;
        }

        Gate::authorize('createInvoice', $shipment);
    }
}
