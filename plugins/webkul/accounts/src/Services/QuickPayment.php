<?php

namespace Webkul\Account\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Enums\PaymentStatus;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Models\Partner;
use Webkul\Account\Models\Payment;
use Webkul\Account\Models\PaymentRegister;
use Webkul\Security\Models\User;
use Webkul\Security\Services\MultiCompanyAdminService;
use Webkul\Support\Services\CompanyContext;

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

    public static function isReceive(mixed $type): bool
    {
        if ($type instanceof PaymentType) {
            return $type === PaymentType::RECEIVE;
        }

        return $type === PaymentType::RECEIVE->value;
    }

    /**
     * The company whose invoices this customer can be paid in. A contact
     * with no company uses the company the person is working in.
     */
    public static function companyIdForPartner(int $partnerId): ?int
    {
        $partner = Partner::withoutGlobalScopes()->find($partnerId);

        if ($partner === null) {
            return null;
        }

        $companyId = (int) ($partner->company_id ?: current_company_id());

        return $companyId > 0 ? $companyId : null;
    }

    /**
     * Cash, bank and card journals of this company, oldest id first.
     *
     * @return array<int, int>
     */
    public static function receivableJournalIds(int $companyId): array
    {
        return Journal::query()
            ->where('company_id', $companyId)
            ->whereIn('type', [JournalType::BANK, JournalType::CASH, JournalType::CREDIT_CARD])
            ->orderBy('id')
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();
    }

    /**
     * This customer's posted invoices that still have a balance, oldest due
     * date first (U95). Read past the global scopes and keep the company
     * filter: the list is this company's, and another company's invoice
     * must not appear because the viewer can see every company.
     *
     * @return Collection<int, Move>
     */
    public static function openCustomerInvoices(int $partnerId, ?int $companyId = null): Collection
    {
        $companyId ??= static::companyIdForPartner($partnerId);

        if ($partnerId < 1 || $companyId === null) {
            return collect();
        }

        return Move::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('partner_id', $partnerId)
            ->where('move_type', MoveType::OUT_INVOICE)
            ->where('state', MoveState::POSTED)
            ->whereIn('payment_state', [PaymentState::NOT_PAID, PaymentState::PARTIAL, PaymentState::IN_PAYMENT])
            ->where('amount_residual', '>', 0)
            ->orderByRaw('invoice_date_due asc nulls last')
            ->orderBy('id')
            ->get();
    }

    public static function openAmount(Move $invoice): float
    {
        return round((float) $invoice->paymentTermLines()->withoutGlobalScopes()
            ->where('reconciled', false)
            ->get()
            ->sum(fn (MoveLine $line): float => abs((float) $line->amount_residual_currency)), 2);
    }

    /**
     * Rows for the payment form and the invoice-list dialog. Each starts
     * at its open balance, which is "Pay in full".
     *
     * @return array<int, array{invoice_id: int, name: string, open: float, amount: float}>
     */
    public static function allocationRows(int $partnerId, ?int $companyId = null): array
    {
        return static::openCustomerInvoices($partnerId, $companyId)
            ->map(fn (Move $invoice): array => [
                'invoice_id' => (int) $invoice->id,
                'name'       => (string) $invoice->name,
                'open'       => $open = static::openAmount($invoice),
                'amount'     => $open,
            ])
            ->filter(fn (array $row): bool => $row['open'] > 0)
            ->values()
            ->all();
    }

    /**
     * One received payment for the amounts named per invoice (U95, U96).
     * The lines are loaded here, never trusted from the form beyond their
     * ids and amounts, and they must be one customer in one company.
     * Anything else is refused before a payment exists. The books then
     * apply those amounts, so a smaller figure on an older invoice is not
     * replaced by taking that invoice in full.
     *
     * @param  array<int, float>  $amountsByInvoiceId
     */
    public static function payCustomerInvoices(int $partnerId, array $amountsByInvoiceId, int $journalId, string $date): Payment
    {
        if (! static::mayRecordPayment()) {
            throw ValidationException::withMessages([
                'partner_id' => __('accounts::filament/resources/invoice/actions/record-several.not-allowed'),
            ]);
        }

        $ids = array_values(array_unique(array_map(intval(...), array_keys($amountsByInvoiceId))));

        $invoices = Move::withoutGlobalScopes()
            ->whereIn('id', $ids)
            ->where('move_type', MoveType::OUT_INVOICE)
            ->get()
            ->keyBy('id');

        if ($ids === []) {
            throw ValidationException::withMessages([
                'allocations' => __('accounts::filament/resources/invoice/actions/record-several.nothing'),
            ]);
        }

        if ($invoices->count() !== count($ids)) {
            throw ValidationException::withMessages([
                'allocations' => __('accounts::filament/resources/invoice/actions/record-several.not-together'),
            ]);
        }

        $companyId = (int) $invoices->first()->company_id;
        $currencyId = (int) $invoices->first()->currency_id;

        if ($invoices->contains(fn (Move $invoice): bool => (int) $invoice->company_id !== $companyId
            || (int) $invoice->partner_id !== $partnerId
            || (int) $invoice->currency_id !== $currencyId
            || $invoice->state !== MoveState::POSTED)) {
            throw ValidationException::withMessages([
                'allocations' => __('accounts::filament/resources/invoice/actions/record-several.not-together'),
            ]);
        }

        // The form is not the only caller. A crafted invoice id from
        // another client is refused here, after the scope-free read.
        if (! app(CompanyContext::class)->mayWriteTo($companyId)) {
            throw ValidationException::withMessages([
                'partner_id' => __('accounts::filament/resources/invoice/actions/record-several.company'),
            ]);
        }

        $caps = [];
        $paying = $invoices->sortBy(fn (Move $invoice): array => [
            $invoice->invoice_date_due?->toDateString() ?? '9999-12-31',
            $invoice->id,
        ]);

        foreach ($paying as $invoice) {
            $amount = round((float) ($amountsByInvoiceId[$invoice->id] ?? 0), 2);

            if ($amount == 0.0) {
                continue;
            }

            $open = static::openAmount($invoice);

            if ($amount < 0 || $amount - $open > 0.009) {
                throw ValidationException::withMessages([
                    'allocations' => __('accounts::filament/resources/invoice/actions/record-several.amount', [
                        'invoice' => $invoice->name,
                    ]),
                ]);
            }

            $left = $amount;

            $lines = $invoice->paymentTermLines()->withoutGlobalScopes()
                ->where('reconciled', false)
                ->where('company_id', $companyId)
                ->orderBy('date_maturity')
                ->orderBy('id')
                ->get();

            foreach ($lines as $line) {
                $lineOpen = round(abs((float) $line->amount_residual_currency), 2);

                if ($lineOpen <= 0 || $left <= 0) {
                    continue;
                }

                $take = min($lineOpen, $left);
                $caps[(int) $line->id] = $take;
                $left = round($left - $take, 2);
            }

            if ($left > 0.009) {
                throw ValidationException::withMessages([
                    'allocations' => __('accounts::filament/resources/invoice/actions/record-several.amount', [
                        'invoice' => $invoice->name,
                    ]),
                ]);
            }
        }

        if ($caps === []) {
            throw ValidationException::withMessages([
                'allocations' => __('accounts::filament/resources/invoice/actions/record-several.nothing'),
            ]);
        }

        return DB::transaction(function () use ($invoices, $caps, $companyId, $partnerId, $journalId, $date, $currencyId): Payment {
            $before = Payment::withoutGlobalScopes()->where('company_id', $companyId)->count();
            $lines = MoveLine::withoutGlobalScopes()
                ->whereIn('id', array_keys($caps))
                ->where('company_id', $companyId)
                ->with(['account', 'move', 'currency', 'company'])
                ->get();

            $register = new PaymentRegister;
            $register->lines = $lines;
            $register->company = $invoices->first()->company;
            $register->currency = $invoices->first()->currency;
            $register->currency_id = $currencyId;
            $register->payment_type = PaymentType::RECEIVE;
            $register->computeBatches();
            $register->computeAvailableJournalIds();

            if (! in_array($journalId, array_map(intval(...), $register->available_journal_ids), true)) {
                throw ValidationException::withMessages([
                    'journal_id' => __('accounts::filament/resources/invoice/actions/record-several.journal'),
                ]);
            }

            $register->journal_id = $journalId;
            $register->journal = Journal::find($journalId);
            $register->computePaymentMethodLineId();
            $register->allocationCaps = $caps;

            $saved = PaymentRegister::create([
                'journal_id'                  => $journalId,
                'payment_method_line_id'      => $register->payment_method_line_id,
                'company_id'                  => $companyId,
                'partner_id'                  => $partnerId,
                'currency_id'                 => $currencyId,
                'payment_type'                => PaymentType::RECEIVE,
                'partner_type'                => 'customer',
                'payment_date'                => $date,
                'amount'                      => round(array_sum($caps), 2),
                'installments_mode'           => 'full',
                'payment_difference_handling' => 'open',
                'communication'               => mb_substr($invoices->pluck('name')->implode(', '), 0, 250),
            ]);

            $saved->lines()->sync(array_keys($caps));
            $saved->refresh();
            $saved->allocationCaps = $caps;
            $saved->computeFromLines();
            $saved->save();
            $saved->allocationCaps = $caps;

            AccountFacade::createPayments($saved);

            $after = Payment::withoutGlobalScopes()->where('company_id', $companyId)->count();

            // One deposit is one payment. Two would be two bank lines,
            // which is the gap this exists to close.
            if ($after !== $before + 1) {
                throw ValidationException::withMessages([
                    'allocations' => __('accounts::filament/resources/invoice/actions/record-several.not-together'),
                ]);
            }

            return Payment::withoutGlobalScopes()
                ->where('company_id', $companyId)
                ->latest('id')
                ->firstOrFail();
        });
    }

    /**
     * A received payment saved with no invoice amounts is still posted now
     * (U96): the form no longer leaves a draft to confirm afterwards.
     * Vendor payments do not come here.
     */
    public static function postReceivedPayment(Payment $payment): Payment
    {
        if (! static::mayRecordPayment()) {
            throw ValidationException::withMessages([
                'partner_id' => __('accounts::filament/resources/invoice/actions/record-several.not-allowed'),
            ]);
        }

        if (! app(CompanyContext::class)->mayWriteTo($payment->company_id)) {
            throw ValidationException::withMessages([
                'partner_id' => __('accounts::filament/resources/invoice/actions/record-several.company'),
            ]);
        }

        return DB::transaction(function () use ($payment): Payment {
            $payment = AccountFacade::postPayment($payment);

            if (! $payment->move_id && in_array($payment->state, [PaymentStatus::IN_PROCESS, PaymentStatus::PAID], true)) {
                $payment->generateJournalEntry();
                $payment->refresh();
            }

            if ($payment->move) {
                AccountFacade::confirmMove($payment->move);
            }

            return $payment->refresh();
        });
    }

    /**
     * Save on a customer payment (U96). Amounts on the open invoices become
     * one posted payment. With none, the amount typed is posted and left
     * as credit. The invoice ids are checked again in payCustomerInvoices.
     *
     * @param  array<string, mixed>  $data
     */
    public static function createReceivedPayment(array $data): Payment
    {
        $amounts = [];

        foreach ($data['allocations'] ?? [] as $row) {
            if (! is_array($row) || ! isset($row['invoice_id'])) {
                continue;
            }

            $amounts[(int) $row['invoice_id']] = (float) ($row['amount'] ?? 0);
        }

        unset($data['allocations']);

        if (array_filter($amounts, fn (float $amount): bool => $amount > 0) !== []) {
            return static::payCustomerInvoices(
                (int) $data['partner_id'],
                $amounts,
                (int) $data['journal_id'],
                (string) ($data['date'] ?? now()->toDateString()),
            );
        }

        $payment = Payment::create(Arr::only($data, (new Payment)->getFillable()));

        return static::postReceivedPayment($payment);
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

    /**
     * Company Admin and up, for this payment's company (U100). A global
     * Company Admin role is true for every company, so membership is the
     * boundary. A clerk who can record payments still cannot refund.
     */
    public static function mayRefundUnused(Payment $payment): bool
    {
        $user = Auth::user();
        $companyId = (int) $payment->company_id;

        if (! $user instanceof User || ! $user->is_active || $companyId < 1) {
            return false;
        }

        $administration = app(MultiCompanyAdminService::class);

        if ($administration->mayAdministerCompany($user, $companyId)) {
            return true;
        }

        return $user->isCompanyAdmin($companyId)
            && $administration->isCompanyAssigned($user, $companyId);
    }

    /**
     * Pay the unused amount back (U100). The amount is read here, under a
     * lock, never from the form: a second refund, or a changed figure,
     * would pay the customer twice. The payment out is matched to the
     * open credit, so the money leaves once and the credit is gone.
     */
    public static function refundUnused(Payment $payment, int $journalId, string $date): Payment
    {
        if (! static::mayRefundUnused($payment)) {
            throw ValidationException::withMessages([
                'payment' => __('accounts::filament/resources/payment/actions/refund-unused.not-allowed'),
            ]);
        }

        if (! app(CompanyContext::class)->mayWriteTo($payment->company_id)) {
            throw ValidationException::withMessages([
                'payment' => __('accounts::filament/resources/payment/actions/refund-unused.company'),
            ]);
        }

        if ($payment->payment_type !== PaymentType::RECEIVE) {
            throw ValidationException::withMessages([
                'payment' => __('accounts::filament/resources/payment/actions/refund-unused.not-allowed'),
            ]);
        }

        return DB::transaction(function () use ($payment, $journalId, $date): Payment {
            $locked = Payment::withoutGlobalScopes()
                ->whereKey($payment->getKey())
                ->lockForUpdate()
                ->firstOrFail();
            $locked->unsetRelation('entryLines');

            $amount = $locked->unusedAmount();

            if ($amount === null || $amount <= 0) {
                throw ValidationException::withMessages([
                    'payment' => __('accounts::filament/resources/payment/actions/refund-unused.nothing'),
                ]);
            }

            $journals = static::receivableJournalIds((int) $locked->company_id);

            if (! in_array($journalId, $journals, true)) {
                throw ValidationException::withMessages([
                    'journal_id' => __('accounts::filament/resources/payment/actions/refund-unused.journal'),
                ]);
            }

            $before = Payment::withoutGlobalScopes()->where('company_id', $locked->company_id)->count();

            // The saving hook reads the method line before it chooses one,
            // so the outbound line is set here. Without it the payment out
            // cannot be posted.
            $draft = new Payment;
            $draft->payment_type = PaymentType::SEND;
            $draft->partner_type = 'customer';
            $draft->partner_id = $locked->partner_id;
            $draft->journal_id = $journalId;
            $draft->setRelation('journal', Journal::query()->find($journalId));
            $draft->setRelation('partner', $locked->partner);
            $draft->computePaymentMethodLineId();

            if (! $draft->payment_method_line_id) {
                throw ValidationException::withMessages([
                    'journal_id' => __('accounts::filament/resources/payment/actions/refund-unused.journal'),
                ]);
            }

            $refund = Payment::create([
                'payment_type'           => PaymentType::SEND,
                'partner_type'           => 'customer',
                'partner_id'             => $locked->partner_id,
                'journal_id'             => $journalId,
                'company_id'             => $locked->company_id,
                'currency_id'            => $locked->currency_id,
                'amount'                 => $amount,
                'date'                   => $date,
                'source_payment_id'      => $locked->id,
                'destination_account_id' => $locked->destination_account_id,
                'payment_method_line_id' => $draft->payment_method_line_id,
                'memo'                   => $locked->name,
            ]);

            $refund = AccountFacade::postPayment($refund->refresh());

            if (! $refund->move_id && in_array($refund->state, [PaymentStatus::IN_PROCESS, PaymentStatus::PAID], true)) {
                $refund->generateJournalEntry();
                $refund->refresh();
            }

            if (! $refund->move_id) {
                throw ValidationException::withMessages([
                    'journal_id' => __('accounts::filament/resources/payment/actions/refund-unused.journal'),
                ]);
            }

            AccountFacade::confirmMove($refund->move);

            $lines = MoveLine::withoutGlobalScopes()
                ->where('company_id', $locked->company_id)
                ->where('account_id', $locked->destination_account_id)
                ->whereIn('move_id', [$locked->move_id, $refund->move_id])
                ->where('reconciled', false)
                ->get();

            AccountFacade::reconcile($lines);

            $locked->unsetRelation('entryLines');

            if (($locked->unusedAmount() ?? 0) > 0.009) {
                throw ValidationException::withMessages([
                    'payment' => __('accounts::filament/resources/payment/actions/refund-unused.not-settled'),
                ]);
            }

            if (Payment::withoutGlobalScopes()->where('company_id', $locked->company_id)->count() !== $before + 1) {
                throw ValidationException::withMessages([
                    'payment' => __('accounts::filament/resources/payment/actions/refund-unused.not-settled'),
                ]);
            }

            $actor = Auth::user();

            $locked->addMessage([
                'type' => 'note',
                'body' => e(__('accounts::filament/resources/payment/actions/refund-unused.history', [
                    'name'   => $actor?->name,
                    'amount' => money($amount, $locked->currency?->name),
                ])),
            ]);

            return $refund->refresh();
        });
    }
}
