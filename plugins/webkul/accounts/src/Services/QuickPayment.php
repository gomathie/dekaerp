<?php

namespace Webkul\Account\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Models\Payment;
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

    /**
     * The way this user last used for this company, when it is still one of
     * the ways this document can use (U98). A business that is mostly Mobile
     * Money should not start on Cash every time. Another company's payments
     * are ignored, and a journal that is no longer allowed falls through to
     * the caller's own default.
     *
     * @param  array<int, int|string>  $allowedJournalIds
     */
    public static function rememberedJournalId(int $companyId, array $allowedJournalIds, PaymentType $type): ?int
    {
        $userId = Auth::id();
        $allowed = array_values(array_unique(array_map(intval(...), $allowedJournalIds)));

        if ($userId === null || $companyId < 1 || $allowed === []) {
            return null;
        }

        $journalId = Payment::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('creator_id', $userId)
            ->where('payment_type', $type)
            ->whereIn('journal_id', $allowed)
            ->latest('id')
            ->value('journal_id');

        return $journalId === null ? null : (int) $journalId;
    }

    /**
     * What a part payment or an overpayment leaves behind (U99). The credit
     * sentence comes first, then the balance, so an amount above the invoice
     * says what is kept before what remains.
     *
     * @return array<int, string>
     */
    public static function remainderLines(float $due, float $paid, ?string $currencyName): array
    {
        $due = round($due, 2);
        $paid = round(max(0, $paid), 2);
        $lines = [];

        if ($paid > $due) {
            $lines[] = __('accounts::filament/resources/invoice/actions/pay-action.form.hints.kept-as-credit', [
                'amount' => money($paid - $due, $currencyName),
            ]);
        }

        $lines[] = __('accounts::filament/resources/invoice/actions/pay-action.form.hints.balance-after', [
            'amount' => money(max(0, $due - $paid), $currencyName),
        ]);

        return $lines;
    }

    /**
     * Credit this customer still has in this company, in this invoice's
     * currency (U97). These are the same open lines Outstanding credits
     * lists once the invoice is posted, so the question can be asked while
     * it is still a draft. Read past the global scopes and keep the company
     * filter: the credit belongs to the customer, and another company's
     * line, or another clerk's payment hidden by an ownership scope, must
     * neither be offered nor applied.
     *
     * @return array{amount: float, line_ids: array<int, int>}
     */
    public static function unusedCustomerCredit(Move $move): array
    {
        $empty = ['amount' => 0.0, 'line_ids' => []];
        $partnerId = (int) ($move->commercial_partner_id ?: $move->partner_id);

        if ($move->move_type !== MoveType::OUT_INVOICE || $partnerId < 1 || ! $move->company_id || ! $move->currency) {
            return $empty;
        }

        $accountIds = $move->paymentTermLines()
            ->whereNotNull('account_id')
            ->pluck('account_id')
            ->unique()
            ->all();

        if ($accountIds === []) {
            return $empty;
        }

        $lines = MoveLine::withoutGlobalScopes()
            ->where('company_id', $move->company_id)
            ->whereIn('account_id', $accountIds)
            ->where('parent_state', MoveState::POSTED)
            ->where('partner_id', $partnerId)
            ->where('move_id', '!=', $move->id)
            ->where('reconciled', false)
            ->where('balance', '<', 0)
            ->where(function ($query): void {
                $query->where('amount_residual', '!=', 0)
                    ->orWhere('amount_residual_currency', '!=', 0);
            })
            ->orderBy('date')
            ->orderBy('id')
            ->get();

        $amount = 0.0;
        $ids = [];

        foreach ($lines as $line) {
            if ((int) $line->currency_id === (int) $move->currency_id) {
                $lineAmount = abs((float) $line->amount_residual_currency);
            } else {
                $lineAmount = $line->companyCurrency->convert(
                    abs((float) $line->amount_residual),
                    $move->currency,
                    $move->company,
                    $line->date,
                );
            }

            if ($move->currency->isZero($lineAmount)) {
                continue;
            }

            $amount += $lineAmount;
            $ids[] = (int) $line->id;
        }

        return ['amount' => round($amount, 2), 'line_ids' => $ids];
    }

    /**
     * The confirm and Pay question. Null when there is nothing to apply, so
     * those screens stay as they were.
     */
    public static function unusedCreditQuestion(Move $move): ?string
    {
        $credit = static::unusedCustomerCredit($move);

        if ($credit['amount'] <= 0) {
            return null;
        }

        return __('accounts::filament/resources/invoice/actions/confirm-action.unused-credit.question', [
            'amount' => money($credit['amount'], $move->currency?->name),
        ]);
    }

    /**
     * Applies this customer's unused credit to this posted invoice, oldest
     * first, until the invoice is settled (U97). The lines are chosen here,
     * never from the request, and checked again for this company. This is
     * not a new payment, so it does not take the record-payment permission:
     * the screen that calls it has already decided the person may confirm
     * or pay this invoice. A second payment would count the money twice.
     */
    public static function applyUnusedCustomerCredit(Move $move): void
    {
        if ($move->state !== MoveState::POSTED || $move->move_type !== MoveType::OUT_INVOICE) {
            return;
        }

        $offer = static::unusedCustomerCredit($move);

        if ($offer['line_ids'] === []) {
            return;
        }

        $credits = MoveLine::withoutGlobalScopes()
            ->whereIn('id', $offer['line_ids'])
            ->where('company_id', $move->company_id)
            ->orderBy('date')
            ->orderBy('id')
            ->get();

        foreach ($credits as $credit) {
            $move->refresh();

            $open = $move->paymentTermLines()
                ->where('reconciled', false)
                ->where('account_id', $credit->account_id)
                ->get();

            if ($open->isEmpty()) {
                break;
            }

            AccountFacade::reconcile($open->push($credit));
        }
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
