<?php

namespace Webkul\Sale\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

/**
 * One Zoho Books import started from the Super Admin page.
 *
 * Not company-scoped on purpose: only an active Super Admin reaches the page
 * and the job, and both read a run by id.
 */
class ZohoImportRun extends Model
{
    public const PROPOSED = 'proposed';

    public const QUEUED = 'queued';

    public const RUNNING = 'running';

    public const FINISHED = 'finished';

    public const FAILED = 'failed';

    public const MODE_IMPORT = 'import';

    public const MODE_COMPARE = 'compare';

    protected $table = 'sales_zoho_import_runs';

    protected $fillable = [
        'company_id',
        'user_id',
        'mode',
        'status',
        'cutoff_at',
        'step',
        'phase',
        'export',
        'mapping',
        'counts',
        'comparison',
        'message',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'mapping'     => 'array',
        'counts'      => 'array',
        'comparison'  => 'array',
        'cutoff_at'   => 'datetime',
        'step'        => 'integer',
        'started_at'  => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool
    {
        return in_array($this->status, [self::QUEUED, self::RUNNING], true);
    }
}
