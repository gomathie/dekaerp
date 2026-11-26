<?php

namespace Webkul\Account\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\PaymentRegister;

/**
 * "Paid now" on a new invoice (user, 2026-11-20: confirm and pay in one
 * step, for cash and walk-in sales). Confirms the invoice and records the
 * full amount through the chosen way to pay, with exactly the defaults the
 * Pay window would fill in, so both paths post the same entries.
 */
class QuickPayment
{
    /**
     * The Invoices module's payment permission (its PaymentPolicy::create).
     * Named here rather than through that policy: accounts is the base
     * plugin and must not reference the invoices plugin's classes.
     */
    public const PERMISSION = 'create_invoice_payment';

    /**
     * Who may record a payment from a screen: Record payment on an invoice,
     * bill or refund, and "Paid now" on a new invoice. Recording money on the
     * books needs its own permission (user, 2026-11-26); before, anyone who
     * could open the document could pay it. Fails closed with no signed-in
     * user. Super Admins and assigned Multi-Company Admins pass through their
     * Gate::before on this bare permission check, as they do everywhere.
     */
    public static function mayRecordPayment(): bool
    {
        return (bool) Auth::user()?->can(self::PERMISSION);
    }

    public function confirmAndPay(Move $move, int $journalId): Move
    {
        // Repeated here, not only on the form: the direct-sale link presets
        // "Paid now", and a Livewire update can set any form field.
        if (! static::mayRecordPayment()) {
            throw ValidationException::withMessages([
                'paid_journal_id' => __('accounts::filament/resources/invoice/actions/pay-action.paid-now.not-allowed'),
            ]);
        }

        return DB::transaction(function () use ($move, $journalId): Move {
            if ($move->state === MoveState::DRAFT) {
                $move->checked = $move->journal?->auto_check_on_post;
                $move = AccountFacade::confirmMove($move);
            }

            $move->refresh();

            $register = new PaymentRegister;
            $register->lines = $move->lines;
            $register->company = $move->company;
            $register->currency = $move->currency;
            $register->currency_id = $move->currency_id;
            $register->payment_type = $move->isInbound(true) ? PaymentType::RECEIVE : PaymentType::SEND;
            $register->computeBatches();
            $register->computeAvailableJournalIds();

            // Only a way to pay this invoice could use; never another
            // company's journal picked by id.
            if (! in_array($journalId, array_map('intval', $register->available_journal_ids), true)) {
                throw ValidationException::withMessages([
                    'paid_journal_id' => __('accounts::filament/resources/invoice/actions/pay-action.paid-now.invalid'),
                ]);
            }

            $register->journal_id = $journalId;
            $register->journal = Journal::find($journalId);
            $register->computePaymentMethodLineId();

            $amounts = $register->getTotalAmountsToPay($register->batches);

            $paymentRegister = PaymentRegister::create([
                'journal_id'             => $journalId,
                'payment_method_line_id' => $register->payment_method_line_id,
                'amount'                 => $amounts['amount_by_default'],
                'currency_id'            => $move->currency_id,
                'payment_date'           => now()->toDateString(),
                'communication'          => $move->name,
                'installments_mode'      => 'full',
            ]);

            $paymentRegister->lines()->sync(
                $move->paymentTermLines->filter(fn ($line) => ! $line->reconciled)->pluck('id')->all()
            );

            $paymentRegister->refresh();
            $paymentRegister->computeFromLines();
            $paymentRegister->save();

            AccountFacade::createPayments($paymentRegister);

            return $move->refresh();
        });
    }
}
