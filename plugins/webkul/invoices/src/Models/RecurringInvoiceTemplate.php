<?php

namespace Webkul\Invoice\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;
use Webkul\Account\Models\FiscalPosition;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Partner;
use Webkul\Account\Models\PaymentTerm;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Currency;
use Webkul\Support\Traits\BelongsToCompany;

class RecurringInvoiceTemplate extends Model
{
    use BelongsToCompany;

    protected $table = 'invoices_recurring_templates';

    protected $fillable = [
        'company_id', 'partner_id', 'journal_id', 'currency_id', 'invoice_user_id',
        'invoice_payment_term_id', 'fiscal_position_id', 'name', 'frequency',
        'next_invoice_date', 'ends_on', 'state', 'auto_email', 'narration',
        'last_generated_at', 'last_failure_reason',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $template): void {
            if ($template->partner_id && (int) Partner::withoutGlobalScopes()->whereKey($template->partner_id)->value('company_id') !== (int) $template->company_id) {
                throw ValidationException::withMessages(['partner_id' => __('invoices::recurring.errors.company-mismatch')]);
            }

            // Contacts have no active flag: an archived contact is soft-deleted.
            if ($template->partner_id && ! Partner::withoutGlobalScopes()->whereKey($template->partner_id)->whereNull('deleted_at')->exists()) {
                throw ValidationException::withMessages(['partner_id' => __('invoices::recurring.errors.inactive-customer')]);
            }

            if ($template->journal_id && (int) Journal::withoutGlobalScopes()->whereKey($template->journal_id)->value('company_id') !== (int) $template->company_id) {
                throw ValidationException::withMessages(['journal_id' => __('invoices::recurring.errors.company-mismatch')]);
            }

            if ($template->currency_id && ! Currency::query()->permittedFor((int) $template->company_id)->whereKey($template->currency_id)->exists()) {
                throw ValidationException::withMessages(['currency_id' => __('invoices::recurring.errors.currency')]);
            }

            if ($template->fiscal_position_id && (int) FiscalPosition::withoutGlobalScopes()->whereKey($template->fiscal_position_id)->value('company_id') !== (int) $template->company_id) {
                throw ValidationException::withMessages(['fiscal_position_id' => __('invoices::recurring.errors.company-mismatch')]);
            }

            if ($template->invoice_payment_term_id && ! PaymentTerm::withoutGlobalScopes()->whereKey($template->invoice_payment_term_id)->where(function ($terms) use ($template): void {
                $terms->whereNull('company_id')->orWhere('company_id', $template->company_id);
            })->exists()) {
                throw ValidationException::withMessages(['invoice_payment_term_id' => __('invoices::recurring.errors.company-mismatch')]);
            }

            if ($template->invoice_user_id && ! User::query()->whereKey($template->invoice_user_id)->whereHas('allowedCompanies', fn ($query) => $query->whereKey($template->company_id))->exists()) {
                throw ValidationException::withMessages(['invoice_user_id' => __('invoices::recurring.errors.responsible')]);
            }

            if ($template->ends_on && $template->next_invoice_date && $template->ends_on < $template->next_invoice_date) {
                throw ValidationException::withMessages(['ends_on' => __('invoices::recurring.errors.end-date')]);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'next_invoice_date' => 'date',
            'ends_on'           => 'date',
            'auto_email'        => 'boolean',
            'last_generated_at' => 'datetime',
        ];
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function invoiceUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invoice_user_id');
    }

    public function paymentTerm(): BelongsTo
    {
        return $this->belongsTo(PaymentTerm::class, 'invoice_payment_term_id');
    }

    public function fiscalPosition(): BelongsTo
    {
        return $this->belongsTo(FiscalPosition::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(RecurringInvoiceTemplateLine::class, 'recurring_template_id')->orderBy('sort');
    }

    public function periods(): HasMany
    {
        return $this->hasMany(RecurringInvoicePeriod::class, 'recurring_template_id')->latest('period_start');
    }
}
