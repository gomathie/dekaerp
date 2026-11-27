<?php

namespace Webkul\Account\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Models\MoveReversal;
use Webkul\Account\Models\Payment;
use Webkul\Account\Models\PaymentRegister;
use Webkul\Support\Services\CompanyContext;

/**
 * Plan 10 D5 (user, 2026-11-27): one credit note from a posted customer
 * invoice. It is posted in the same transaction. A refusal leaves no draft.
 */
class CreditNoteFromInvoice
{
    public const APPLY = 'apply';

    public const REFUND = 'refund';

    public const CREDIT = 'credit';

    /**
     * @param  array<int, int|string>  $lineIds  Empty means the whole invoice.
     */
    public function issue(Move $invoice, string $reason, string $disposition, array $lineIds = [], ?string $date = null): Move
    {
        $reason = trim($reason);
        $lineIds = array_values(array_unique(array_map('intval', $lineIds)));

        if ($reason === '') {
            throw ValidationException::withMessages([
                'reason' => __('accounts::filament/resources/invoice/actions/reverse.one-step.reason'),
            ]);
        }

        if (! in_array($disposition, [self::APPLY, self::REFUND, self::CREDIT], true)) {
            throw ValidationException::withMessages([
                'disposition' => __('accounts::filament/resources/invoice/actions/reverse.one-step.disposition-invalid'),
            ]);
        }

        // Pay it back records a payment. The same permission as Record payment,
        // so a clerk who can credit an invoice cannot also send the money.
        if ($disposition === self::REFUND && ! QuickPayment::mayRecordPayment()) {
            throw ValidationException::withMessages([
                'disposition' => __('accounts::filament/resources/invoice/actions/reverse.one-step.not-allowed'),
            ]);
        }

        if (! app(CompanyContext::class)->mayWriteTo($invoice->company_id)) {
            throw ValidationException::withMessages([
                'company' => __('accounts::filament/resources/invoice/actions/reverse.one-step.company'),
            ]);
        }

        $this->assertLinesBelongToInvoice($invoice, $lineIds);

        return DB::transaction(function () use ($invoice, $reason, $disposition, $lineIds, $date): Move {
            $locked = Move::withoutGlobalScopes()->whereKey($invoice->id)->lockForUpdate()->first();

            if (
                $locked === null
                || $locked->move_type !== MoveType::OUT_INVOICE
                || $locked->state !== MoveState::POSTED
                || ! app(CompanyContext::class)->mayWriteTo($locked->company_id)
            ) {
                throw ValidationException::withMessages([
                    'invoice' => __('accounts::filament/resources/invoice/actions/reverse.one-step.invoice'),
                ]);
            }

            $startingResidual = (float) $locked->amount_residual;
            $when = $date !== null && $date !== '' ? $date : now()->toDateString();

            // Copies follow this id order. buildReversal walks $move->lines,
            // so the relation is set in that order and the new ids line up.
            $locked->setRelation('lines', MoveLine::withoutGlobalScopes()
                ->where('move_id', $locked->id)
                ->where('company_id', $locked->company_id)
                ->orderBy('id')
                ->get());

            $reversal = MoveReversal::create([
                'reason'     => $reason,
                'date'       => $when,
                'journal_id' => $locked->journal_id,
                'company_id' => $locked->company_id,
            ]);

            $reversal->moves()->attach($locked);

            $credit = AccountFacade::reverseMoves(collect([$locked]), [[
                'reference'        => Str::limit('Reversal of: '.$locked->name.', '.$reason, 250),
                'date'             => $when,
                'invoice_date_due' => $when,
                'invoice_date'     => $when,
                'journal_id'       => $locked->journal_id,
                'invoice_user_id'  => $locked->invoice_user_id,
                'auto_post'        => false,
            ]], false)->first();

            $reversal->newMoves()->attach($credit->id);

            if ($lineIds !== []) {
                $this->dropUnselectedLines($locked, $credit, $lineIds);
            }

            $credit = AccountFacade::computeAccountMove($credit->refresh());
            $credit = AccountFacade::confirmMove($credit);

            if ($credit->state !== MoveState::POSTED) {
                throw ValidationException::withMessages([
                    'invoice' => __('accounts::filament/resources/invoice/actions/reverse.one-step.invoice'),
                ]);
            }

            if ($disposition === self::APPLY) {
                $locked->refresh();

                if ((float) $locked->amount_residual > $startingResidual - 0.01) {
                    throw ValidationException::withMessages([
                        'disposition' => __('accounts::filament/resources/invoice/actions/reverse.one-step.not-applied'),
                    ]);
                }
            }

            if ($disposition === self::CREDIT || $disposition === self::REFUND) {
                // Leave the invoice open. Applying and paying the cash back
                // would reduce the debt and send the money.
                $this->detachFromInvoice($credit);
                $locked->refresh();

                if (abs((float) $locked->amount_residual - $startingResidual) > 0.05) {
                    throw ValidationException::withMessages([
                        'disposition' => __('accounts::filament/resources/invoice/actions/reverse.one-step.not-applied'),
                    ]);
                }
            }

            if ($disposition === self::CREDIT && (float) $credit->refresh()->amount_residual <= 0.01) {
                throw ValidationException::withMessages([
                    'disposition' => __('accounts::filament/resources/invoice/actions/reverse.one-step.not-applied'),
                ]);
            }

            if ($disposition === self::REFUND) {
                $this->refundCreditNote($credit->refresh());
                $locked->refresh();

                if (abs((float) $locked->amount_residual - $startingResidual) > 0.05) {
                    throw ValidationException::withMessages([
                        'disposition' => __('accounts::filament/resources/invoice/actions/reverse.one-step.not-settled'),
                    ]);
                }
            }

            $locked->addMessage([
                'type' => 'note',
                'body' => __('accounts::filament/resources/invoice/actions/reverse.one-step.history', [
                    'disposition' => e(__('accounts::filament/resources/invoice/actions/reverse.one-step.'.$disposition)),
                    'reason'      => e($reason),
                ]),
            ]);

            return $credit->refresh();
        });
    }

    /**
     * @param  array<int, int>  $lineIds
     */
    private function assertLinesBelongToInvoice(Move $invoice, array $lineIds): void
    {
        if ($lineIds === []) {
            return;
        }

        $owned = MoveLine::withoutGlobalScopes()
            ->where('move_id', $invoice->id)
            ->where('company_id', $invoice->company_id)
            ->where('display_type', DisplayType::PRODUCT)
            ->pluck('id')
            ->map(fn ($id): int => (int) $id);

        if (collect($lineIds)->diff($owned)->isNotEmpty()) {
            throw ValidationException::withMessages([
                'lines' => __('accounts::filament/resources/invoice/actions/reverse.one-step.lines-invalid'),
            ]);
        }
    }

    /**
     * @param  array<int, int>  $lineIds
     */
    private function dropUnselectedLines(Move $invoice, Move $credit, array $lineIds): void
    {
        $source = MoveLine::withoutGlobalScopes()
            ->where('move_id', $invoice->id)
            ->where('company_id', $invoice->company_id)
            ->orderBy('id')
            ->get()
            ->values();

        $copies = MoveLine::withoutGlobalScopes()
            ->where('move_id', $credit->id)
            ->where('company_id', $credit->company_id)
            ->orderBy('id')
            ->get()
            ->values();

        $wanted = collect($lineIds);

        if ($source->count() !== $copies->count() || $wanted->isEmpty()) {
            throw ValidationException::withMessages([
                'lines' => __('accounts::filament/resources/invoice/actions/reverse.one-step.lines-invalid'),
            ]);
        }

        foreach ($source as $index => $line) {
            if ($line->display_type === DisplayType::PRODUCT && ! $wanted->contains((int) $line->id)) {
                $copies[$index]->delete();
            }
        }

        $remaining = MoveLine::withoutGlobalScopes()
            ->where('move_id', $credit->id)
            ->where('company_id', $credit->company_id)
            ->where('display_type', DisplayType::PRODUCT)
            ->count();

        if ($remaining < 1) {
            throw ValidationException::withMessages([
                'lines' => __('accounts::filament/resources/invoice/actions/reverse.one-step.lines-invalid'),
            ]);
        }
    }

    private function detachFromInvoice(Move $credit): void
    {
        $partials = collect();

        MoveLine::withoutGlobalScopes()
            ->where('move_id', $credit->id)
            ->where('company_id', $credit->company_id)
            ->get()
            ->each(function (MoveLine $line) use ($partials): void {
                $partials->push(...$line->matchedDebits, ...$line->matchedCredits);
            });

        $reconciler = app(Reconciler::class);

        $partials->unique('id')->each(function ($partial) use ($reconciler): void {
            $fresh = $partial->newQuery()->find($partial->id);

            if ($fresh === null) {
                return;
            }

            $reconciler->unReconcile($fresh);
        });
    }

    /**
     * One outbound payment, settled only against this credit note.
     * The amount is the posted credit note's open balance.
     */
    private function refundCreditNote(Move $credit): void
    {
        $register = new PaymentRegister;
        $register->lines = $credit->lines()->get();
        $register->company = $credit->company;
        $register->currency = $credit->currency;
        $register->currency_id = $credit->currency_id;
        $register->payment_type = PaymentType::SEND;
        $register->computeBatches();
        $register->computeAvailableJournalIds();

        $journal = Journal::withoutGlobalScopes()
            ->where('company_id', $credit->company_id)
            ->whereKey($register->available_journal_ids[0] ?? null)
            ->first();

        if ($journal === null) {
            throw ValidationException::withMessages([
                'journal' => __('accounts::filament/resources/invoice/actions/reverse.one-step.journal'),
            ]);
        }

        $register->journal_id = $journal->id;
        $register->journal = $journal;
        $register->computePaymentMethodLineId();

        if ($register->payment_method_line_id === null) {
            throw ValidationException::withMessages([
                'journal' => __('accounts::filament/resources/invoice/actions/reverse.one-step.journal'),
            ]);
        }

        $amounts = $register->getTotalAmountsToPay($register->batches);

        $lineIds = $credit->paymentTermLines()
            ->get()
            ->filter(fn (MoveLine $line): bool => ! $line->reconciled)
            ->pluck('id')
            ->all();

        $before = Payment::withoutGlobalScopes()->where('company_id', $credit->company_id)->count();

        $paymentRegister = PaymentRegister::create([
            'currency_id'                 => $credit->currency_id,
            'journal_id'                  => $journal->id,
            'payment_method_line_id'      => $register->payment_method_line_id,
            'company_id'                  => $credit->company_id,
            'partner_id'                  => $credit->partner_id,
            'payment_type'                => PaymentType::SEND,
            'payment_date'                => now()->toDateString(),
            'amount'                      => $amounts['amount_by_default'],
            'installments_mode'           => 'full',
            'payment_difference_handling' => 'open',
            'communication'               => $credit->name,
        ]);

        $paymentRegister->lines()->sync($lineIds);
        $paymentRegister->refresh();
        $paymentRegister->computeFromLines();
        $paymentRegister->save();

        AccountFacade::createPayments($paymentRegister);

        $payments = Payment::withoutGlobalScopes()->where('company_id', $credit->company_id)->count();

        if ($payments !== $before + 1 || (float) $credit->refresh()->amount_residual > 0.05) {
            throw ValidationException::withMessages([
                'disposition' => __('accounts::filament/resources/invoice/actions/reverse.one-step.not-settled'),
            ]);
        }
    }
}
