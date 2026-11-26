<?php

namespace Webkul\Sale\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * One Zoho Books record and the DEKA record the import made of it.
 *
 * Not company-scoped on purpose: only zoho:import and zoho:compare read it,
 * in the console, always filtered by company_id explicitly.
 */
class ZohoImportRecord extends Model
{
    protected $table = 'sales_zoho_import_records';

    protected $fillable = [
        'company_id',
        'zoho_type',
        'zoho_id',
        'record_type',
        'record_id',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'record_id'  => 'integer',
    ];
}
