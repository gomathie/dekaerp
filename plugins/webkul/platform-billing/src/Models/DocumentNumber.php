<?php

namespace Webkul\PlatformBilling\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\PlatformBilling\Enums\PlatformDocumentType;

/**
 * The last number issued for one document type in one year.
 *
 * Deliberately **not** company-scoped: DEKA issues these documents, not the
 * tenant, so there is no `company_id` and no `CompanyScope`. That is also why
 * the policy for it denies every tenant ability - see DocumentNumberPolicy.
 */
class DocumentNumber extends Model
{
    protected $table = 'platform_billing_document_numbers';

    protected $fillable = [
        'document_type',
        'year',
        'last_number',
    ];

    protected $casts = [
        'document_type' => PlatformDocumentType::class,
        'year'          => 'integer',
        'last_number'   => 'integer',
    ];
}
