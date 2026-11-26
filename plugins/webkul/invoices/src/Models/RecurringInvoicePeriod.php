<?php

namespace Webkul\Invoice\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;
use Webkul\Account\Models\Move;
use Webkul\Support\Traits\BelongsToCompany;
use Webkul\Support\Traits\InheritsParentCompany;

class RecurringInvoicePeriod extends Model
{
    use BelongsToCompany;
    use InheritsParentCompany;

    protected static string $parentCompanyModel = RecurringInvoiceTemplate::class;

    protected static string $parentCompanyKey = 'recurring_template_id';

    protected $table = 'invoices_recurring_periods';

    protected $fillable = ['recurring_template_id', 'company_id', 'period_start', 'period_end', 'move_id', 'status', 'failure_reason'];

    protected function casts(): array
    {
        return ['period_start' => 'date', 'period_end' => 'date'];
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(RecurringInvoiceTemplate::class, 'recurring_template_id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Move::class, 'move_id');
    }

    protected static function booted(): void
    {
        static::saving(function (self $period): void {
            if (! RecurringInvoiceTemplate::withoutGlobalScopes()->whereKey($period->recurring_template_id)->where('company_id', $period->company_id)->exists()) {
                throw ValidationException::withMessages(['recurring_template_id' => __('invoices::recurring.errors.company-mismatch')]);
            }
        });
    }
}
