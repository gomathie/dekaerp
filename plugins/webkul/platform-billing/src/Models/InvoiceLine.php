<?php

namespace Webkul\PlatformBilling\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Support\Traits\BelongsToCompany;
use Webkul\Support\Traits\InheritsParentCompany;

/** A posted invoice line. Its company is the invoice's company. */
class InvoiceLine extends Model
{
    use BelongsToCompany;
    use InheritsParentCompany;

    protected $table = 'platform_billing_invoice_lines';

    protected $guarded = [];

    protected static string $parentCompanyModel = Invoice::class;

    protected static string $parentCompanyKey = 'invoice_id';

    public static function autoAssignsCompany(): bool
    {
        return false;
    }

    protected function casts(): array
    {
        return [
            'position' => 'integer',
            'amount_minor' => 'integer',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
