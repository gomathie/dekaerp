<?php

namespace Webkul\Account\Http\Controllers\API\V1\Concerns;

/**
 * Accounting plan, A2b (2026-11-13): resetting a posted document needs a
 * reason. The API takes an optional `reason`; without one it records that the
 * change came through the API, so existing clients keep working (no contract
 * change) while the history still says how the document was reopened.
 */
trait ReadsChangeReason
{
    protected function changeReason(): string
    {
        $reason = trim((string) request()->input('reason', ''));

        return $reason !== '' ? mb_substr($reason, 0, 500) : __('accounts::account-manager.change-reason.api');
    }
}
