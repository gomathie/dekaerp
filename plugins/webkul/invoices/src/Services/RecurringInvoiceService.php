<?php

namespace Webkul\Invoice\Services;

use Carbon\CarbonImmutable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Exceptions\MissingJournalException;
use Webkul\Account\Exceptions\PeriodLockedException;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions\PrintAndSendAction;
use Webkul\Account\Models\Move;
use Webkul\Account\Services\PeriodLock;
use Webkul\Invoice\Models\RecurringInvoicePeriod;
use Webkul\Invoice\Models\RecurringInvoiceTemplate;
use Webkul\Invoice\Notifications\RecurringInvoiceCreated;
use Webkul\Partner\Models\Partner;
use Webkul\PlatformBilling\Services\CompanyReadOnlyWriteGuard;
use Webkul\PluginManager\Support\PluginEntitlement;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Currency;
use Webkul\Support\Services\CompanyContext;
use Webkul\Support\Traits\PDFHandler;

class RecurringInvoiceService
{
    public const PERMISSION = 'create_invoice_recurring::invoice::template';

    public function generateDue(int $templateId, bool $manual = false, ?string $scheduledDate = null): ?Move
    {
        $template = RecurringInvoiceTemplate::withoutGlobalScopes()->find($templateId);

        if (! $template instanceof RecurringInvoiceTemplate) {
            return null;
        }

        $actingUser = auth()->user();

        return app(CompanyContext::class)->runFor((int) $template->company_id, function () use ($templateId, $manual, $actingUser, $scheduledDate): ?Move {
            try {
                return DB::transaction(function () use ($templateId, $manual, $actingUser, $scheduledDate): ?Move {
                    $template = RecurringInvoiceTemplate::withoutGlobalScopes()->lockForUpdate()->with('lines.taxes')->find($templateId);

                    if (! $template instanceof RecurringInvoiceTemplate || (! $manual && $template->state !== 'active') || ($manual && $template->state === 'stopped')) {
                        return null;
                    }

                    if ($actingUser instanceof User && ! $actingUser->hasPermissionInCompany(self::PERMISSION, (int) $template->company_id)) {
                        throw new AuthorizationException;
                    }

                    $this->assertCompanyCanGenerate($template);

                    $periodStart = CarbonImmutable::parse($scheduledDate ?? $template->next_invoice_date)->startOfDay();
                    $existingPeriod = RecurringInvoicePeriod::query()
                        ->where('recurring_template_id', $template->getKey())
                        ->whereDate('period_start', $periodStart->toDateString())
                        ->first();

                    if ($existingPeriod?->move_id) {
                        return $existingPeriod->invoice;
                    }

                    if ($periodStart->toDateString() !== CarbonImmutable::parse($template->next_invoice_date)->toDateString()) {
                        return null;
                    }
                    $periodEnd = $this->periodEnd($periodStart, $template->frequency);
                    if ($template->ends_on && $periodStart->gt(CarbonImmutable::parse($template->ends_on))) {
                        $template->update(['state' => 'completed']);

                        return null;
                    }
                    if ($template->ends_on && $periodEnd->gt(CarbonImmutable::parse($template->ends_on))) {
                        $periodEnd = CarbonImmutable::parse($template->ends_on);
                    }
                    $this->assertPeriodWritable($template, $periodStart);

                    $period = RecurringInvoicePeriod::create([
                        'recurring_template_id' => $template->getKey(),
                        'company_id'            => $template->company_id,
                        'period_start'          => $periodStart->toDateString(),
                        'period_end'            => $periodEnd->toDateString(),
                    ]);
                    $invoice = $this->createDraft($template, $periodStart);

                    if ($template->auto_email) {
                        $invoice = AccountFacade::confirmMove($invoice);
                    }

                    $period->update(['move_id' => $invoice->getKey()]);
                    $template->forceFill([
                        'next_invoice_date'   => $periodEnd->addDay()->toDateString(),
                        'last_generated_at'   => now(),
                        'last_failure_reason' => null,
                    ])->save();

                    if ($template->ends_on && CarbonImmutable::parse($template->next_invoice_date)->gt(CarbonImmutable::parse($template->ends_on))) {
                        $template->update(['state' => 'completed']);
                    }

                    DB::afterCommit(function () use ($template, $invoice, $actingUser): void {
                        if ($template->invoiceUser) {
                            $template->invoiceUser->notify(new RecurringInvoiceCreated(
                                (int) $invoice->getKey(),
                                (int) $template->company_id,
                                (string) $invoice->name,
                            ));
                        }

                        if ($template->auto_email) {
                            $this->sendSignedBy($template, $invoice, $actingUser instanceof User ? $actingUser : $template->invoiceUser);
                        }
                    });

                    return $invoice;
                });
            } catch (PeriodLockedException|ValidationException|MissingJournalException $exception) {
                $this->recordFailure($template, $exception->getMessage());

                return null;
            }
        });
    }

    /**
     * The email is signed by a person (PrintAndSendAction::defaultMessage()
     * reads the signed-in user). A scheduled run has nobody signed in, so the
     * template's responsible user signs for the length of the send only.
     * Named web guard: auth() may be Sanctum's guard, which has no setUser
     * restore and no forgetUser() (AGENTS, "Auth:: acts on whichever guard").
     * The invoice is already committed: a mail failure is recorded on the
     * template for a person to resend, never retried (a retry would find the
     * period done and send nothing).
     */
    protected function sendSignedBy(RecurringInvoiceTemplate $template, Move $invoice, ?User $signer): void
    {
        app(CompanyContext::class)->runFor((int) $template->company_id, function () use ($template, $invoice, $signer): void {
            $guard = Auth::guard('web');
            $previous = $guard->hasUser() ? $guard->user() : null;

            if ($signer instanceof User) {
                $guard->setUser($signer);
            }

            try {
                $this->send($invoice);
            } catch (\Throwable $exception) {
                report($exception);
                $this->recordFailure($template, __('invoices::recurring.errors.email-failed', ['number' => $invoice->name]));
            } finally {
                $previous ? $guard->setUser($previous) : $guard->forgetUser();
            }
        });
    }

    public function generateNow(RecurringInvoiceTemplate $template, User $actor): ?Move
    {
        if (! $actor->hasPermissionInCompany(self::PERMISSION, (int) $template->company_id)) {
            throw new AuthorizationException;
        }

        // No scheduled date: generateDue() reads the next date from the row it
        // locks. Passing the caller's copy re-made a period that had already
        // run whenever that copy was stale (found by the tests).
        return $this->generateDue((int) $template->getKey(), manual: true);
    }

    public function setState(RecurringInvoiceTemplate $template, string $state, User $actor): void
    {
        if (! in_array($state, ['active', 'paused', 'stopped'], true)) {
            throw ValidationException::withMessages(['state' => __('invoices::recurring.errors.state')]);
        }

        if (! $actor->hasPermissionInCompany('update_invoice_recurring::invoice::template', (int) $template->company_id)) {
            throw new AuthorizationException;
        }

        if ($template->state === 'completed' || ($template->state === 'stopped' && $state !== 'stopped')) {
            throw ValidationException::withMessages(['state' => __('invoices::recurring.errors.state')]);
        }

        $template->update(['state' => $state]);
    }

    public function send(Move $invoice): void
    {
        if (! $invoice->partner_id) {
            return;
        }

        $recipient = Partner::recipientsForCompany([$invoice->partner_id], (int) $invoice->company_id)->first();

        if (! $recipient?->email) {
            return;
        }

        $pdf = new class
        {
            use PDFHandler;

            public function store(string $html): string
            {
                return $this->savePDF($html, Str::lower(Str::random(24)));
            }
        };
        $path = $pdf->store(view('accounts::invoice/actions/preview.index', ['record' => $invoice])->render());

        if (! $path) {
            return;
        }

        AccountFacade::printAndSendMove($invoice, [
            'partners' => [$invoice->partner_id],
            'subject'  => __('accounts::filament/resources/invoice/actions/print-and-send.message.subject', [
                'partner' => $invoice->partner?->name,
                'number'  => $invoice->name,
            ]),
            'description' => PrintAndSendAction::defaultMessage($invoice),
            'files'       => [$path],
        ]);
    }

    protected function assertCompanyCanGenerate(RecurringInvoiceTemplate $template): void
    {
        PluginEntitlement::assertForCompany('invoices', (int) $template->company_id);

        try {
            app(CompanyReadOnlyWriteGuard::class)->assertCompanyMayWrite((int) $template->company_id);
        } catch (ValidationException $exception) {
            throw $exception;
        }

        // An archived (soft-deleted) contact resolves to null through the relation.
        if (! $template->partner || $template->partner->trashed()) {
            throw ValidationException::withMessages(['partner_id' => __('invoices::recurring.errors.inactive-customer')]);
        }

        if (! $template->partner->email && $template->auto_email) {
            throw ValidationException::withMessages(['partner_id' => __('invoices::recurring.errors.customer-email')]);
        }

        if (! $template->journal?->default_account_id) {
            throw ValidationException::withMessages(['journal_id' => __('invoices::recurring.errors.journal')]);
        }

        if (! $template->journal || (int) $template->journal->company_id !== (int) $template->company_id || $template->journal->type !== JournalType::SALE) {
            throw ValidationException::withMessages(['journal_id' => __('invoices::recurring.errors.journal')]);
        }

        if (! Currency::query()->active()->permittedFor((int) $template->company_id)->whereKey($template->currency_id)->exists()) {
            throw ValidationException::withMessages(['currency_id' => __('invoices::recurring.errors.currency')]);
        }

        if (! $template->lines->isNotEmpty()) {
            throw ValidationException::withMessages(['lines' => __('invoices::recurring.errors.lines')]);
        }

        foreach ($template->lines as $line) {
            if ($line->product_id && (int) $line->product?->company_id !== (int) $template->company_id) {
                throw ValidationException::withMessages(['lines' => __('invoices::recurring.errors.company-mismatch')]);
            }

            foreach ($line->taxes as $tax) {
                if ($tax->type_tax_use !== TypeTaxUse::SALE || ($tax->company_id !== null && (int) $tax->company_id !== (int) $template->company_id)) {
                    throw ValidationException::withMessages(['lines' => __('invoices::recurring.errors.company-mismatch')]);
                }
            }
        }
    }

    protected function assertPeriodWritable(RecurringInvoiceTemplate $template, CarbonImmutable $date): void
    {
        if (app(PeriodLock::class)->isLocked((int) $template->company_id, $date)) {
            throw ValidationException::withMessages(['invoice_date' => __('invoices::recurring.errors.locked-period')]);
        }
    }

    protected function createDraft(RecurringInvoiceTemplate $template, CarbonImmutable $date): Move
    {
        $invoice = Move::create([
            'company_id'              => $template->company_id,
            'partner_id'              => $template->partner_id,
            'journal_id'              => $template->journal_id,
            'currency_id'             => $template->currency_id,
            'invoice_user_id'         => $template->invoice_user_id,
            'invoice_payment_term_id' => $template->invoice_payment_term_id,
            'fiscal_position_id'      => $template->fiscal_position_id,
            'move_type'               => MoveType::OUT_INVOICE,
            'state'                   => MoveState::DRAFT,
            'date'                    => $date->toDateString(),
            'invoice_date'            => $date->toDateString(),
            'narration'               => $template->narration,
            'invoice_origin'          => $template->name,
        ]);

        foreach ($template->lines as $line) {
            $moveLine = $invoice->lines()->create([
                // This is an invoice product line even when it debits the receivable account.
                'display_type' => DisplayType::PRODUCT,
                'product_id'   => $line->product_id,
                'account_id'   => $line->account_id,
                'uom_id'       => $line->uom_id ?: $line->product?->uom_id,
                'name'         => $line->name,
                'quantity'     => $line->quantity,
                'price_unit'   => $line->price_unit,
                'discount'     => $line->discount,
                'currency_id'  => $template->currency_id,
            ]);

            $moveLine->taxes()->sync($line->taxes->modelKeys());
        }

        return AccountFacade::computeAccountMove($invoice->refresh());
    }

    protected function recordFailure(RecurringInvoiceTemplate $template, string $reason): void
    {
        // Query writes bypass the global read-only hook so its refusal can be recorded without permitting invoice or template Eloquent writes.
        DB::table('invoices_recurring_templates')->where('id', $template->getKey())->update([
            'last_failure_reason' => mb_substr($reason, 0, 2000),
            'updated_at'          => now(),
        ]);
    }

    protected function periodEnd(CarbonImmutable $start, string $frequency): CarbonImmutable
    {
        return match ($frequency) {
            'weekly'    => $start->addWeek()->subDay(),
            'monthly'   => $start->addMonthNoOverflow()->subDay(),
            'quarterly' => $start->addMonthsNoOverflow(3)->subDay(),
            'yearly'    => $start->addYearNoOverflow()->subDay(),
            default     => throw ValidationException::withMessages(['frequency' => __('invoices::recurring.errors.frequency')]),
        };
    }
}
