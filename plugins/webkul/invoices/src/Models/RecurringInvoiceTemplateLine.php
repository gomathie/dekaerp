<?php

namespace Webkul\Invoice\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Validation\ValidationException;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Tax;
use Webkul\Product\Models\Product;
use Webkul\Support\Models\UOM;
use Webkul\Support\Traits\BelongsToCompany;
use Webkul\Support\Traits\InheritsParentCompany;

class RecurringInvoiceTemplateLine extends Model
{
    use BelongsToCompany;
    use InheritsParentCompany;

    protected static string $parentCompanyModel = RecurringInvoiceTemplate::class;

    protected static string $parentCompanyKey = 'recurring_template_id';

    protected $table = 'invoices_recurring_template_lines';

    protected $fillable = ['recurring_template_id', 'product_id', 'account_id', 'uom_id', 'name', 'quantity', 'price_unit', 'discount', 'sort'];

    /**
     * Match the column defaults: a form row that leaves a field out sends
     * null, which the NOT NULL columns refuse.
     */
    protected $attributes = ['quantity' => 1, 'price_unit' => 0, 'discount' => 0, 'sort' => 0];

    protected static function booted(): void
    {
        static::saving(function (self $line): void {
            $companyId = (int) RecurringInvoiceTemplate::withoutGlobalScopes()->whereKey($line->recurring_template_id)->value('company_id');

            if ($line->product_id && (int) Product::withoutGlobalScopes()->whereKey($line->product_id)->value('company_id') !== $companyId) {
                throw ValidationException::withMessages(['product_id' => __('invoices::recurring.errors.company-mismatch')]);
            }

            if ($line->account_id && ! Account::withoutGlobalScopes()->whereKey($line->account_id)->where(function ($query) use ($companyId): void {
                $query->where(owned_by_company($companyId));
            })->exists()) {
                throw ValidationException::withMessages(['account_id' => __('invoices::recurring.errors.company-mismatch')]);
            }

            if ($line->product_id && $line->uom_id && (int) UOM::withoutGlobalScopes()->whereKey($line->uom_id)->value('category_id') !== (int) UOM::withoutGlobalScopes()->whereKey(Product::withoutGlobalScopes()->whereKey($line->product_id)->value('uom_id'))->value('category_id')) {
                throw ValidationException::withMessages(['uom_id' => __('invoices::recurring.errors.company-mismatch')]);
            }

            $line->setAttribute('company_id', $companyId);
            $line->discount ??= 0;
            $line->price_unit ??= 0;
            $line->sort ??= 0;

            if ($line->product_id) {
                $line->account_id = null;
            } else {
                $template = RecurringInvoiceTemplate::withoutGlobalScopes()->find($line->recurring_template_id);
                $line->account_id ??= Journal::withoutGlobalScopes()->whereKey($template?->journal_id)->value('default_account_id');

                if ($line->account_id && ! Account::withoutGlobalScopes()->whereKey($line->account_id)->where(function ($query) use ($companyId): void {
                    $query->where(owned_by_company($companyId));
                })->exists()) {
                    throw ValidationException::withMessages(['account_id' => __('invoices::recurring.errors.company-mismatch')]);
                }
            }
        });

        static::saved(function (self $line): void {
            $companyId = (int) RecurringInvoiceTemplate::withoutGlobalScopes()->whereKey($line->recurring_template_id)->value('company_id');

            if ($line->taxes()->where(function ($query) use ($companyId): void {
                $query->where('type_tax_use', '!=', 'sale')
                    ->orWhere(function ($taxes) use ($companyId): void {
                        $taxes->whereNotNull('company_id')->where('company_id', '!=', $companyId);
                    });
            })->exists()) {
                throw ValidationException::withMessages(['taxes' => __('invoices::recurring.errors.company-mismatch')]);
            }
        });
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(RecurringInvoiceTemplate::class, 'recurring_template_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function uom(): BelongsTo
    {
        return $this->belongsTo(UOM::class);
    }

    public function taxes(): BelongsToMany
    {
        return $this->belongsToMany(Tax::class, 'invoices_recurring_template_line_taxes', 'recurring_template_line_id', 'tax_id');
    }
}
