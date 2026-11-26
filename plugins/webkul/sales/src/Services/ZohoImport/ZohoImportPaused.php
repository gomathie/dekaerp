<?php

namespace Webkul\Sale\Services\ZohoImport;

use RuntimeException;

/**
 * An import that stopped at its time budget, between two documents, to be
 * continued by the next queued job. Not a failure: everything recorded so
 * far is kept, and the next run skips it by Zoho id.
 */
class ZohoImportPaused extends RuntimeException
{
    /**
     * @param  array<string, int>  $counts  what this part recorded
     */
    public function __construct(public readonly array $counts)
    {
        parent::__construct('The import paused at its time budget.');
    }
}
