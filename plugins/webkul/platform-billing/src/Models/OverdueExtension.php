<?php

namespace Webkul\PlatformBilling\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Security\Models\User;
use Webkul\Support\Traits\BelongsToCompany;
use Webkul\Support\Traits\InheritsParentCompany;

/**
 * Extra days before an overdue company becomes read-only (D-SAAS-04).
 * The company is the invoice's. One row per invoice and kind.
 */
class OverdueExtension extends Model
{
    use BelongsToCompany;
    use InheritsParentCompany;

    public const KIND_COMPANY_ADMIN = 'company_admin';

    public const KIND_SUPER_ADMIN = 'super_admin';

    public const UNIT_DAY = 'day';

    public const UNIT_WEEK = 'week';

    public const UNIT_MONTH = 'month';

    protected $table = 'platform_billing_overdue_extensions';

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
            'length' => 'integer',
            'granted_at' => 'immutable_datetime',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function grantor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'granted_by');
    }
}
