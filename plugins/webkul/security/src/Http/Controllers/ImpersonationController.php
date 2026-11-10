<?php

namespace Webkul\Security\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Webkul\Security\Services\TenantImpersonation;

/**
 * The banner's End button. A POST with the session's CSRF token, so another
 * site cannot end (or probe) someone's impersonation with a link.
 */
class ImpersonationController
{
    public function end(TenantImpersonation $impersonation): RedirectResponse
    {
        $impersonation->end();

        return redirect()->to(filament()->getPanel('admin')->getUrl() ?? '/');
    }
}
