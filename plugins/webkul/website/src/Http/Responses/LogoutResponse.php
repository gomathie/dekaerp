<?php

namespace Webkul\Website\Http\Responses;

use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;

class LogoutResponse implements \Filament\Auth\Http\Responses\Contracts\LogoutResponse
{
    /**
     * Where someone lands after signing out.
     *
     * A portal user used to be sent to the CMS homepage. That page was removed
     * with the rest of the website on 2026-10-22, so they go to the portal's
     * own login screen instead - which is also what `/` now serves.
     *
     * Resolved through the panel rather than a hardcoded route name: the
     * customer panel owns its login page, and naming the route here would
     * break silently if that changed.
     */
    public function toResponse($request): RedirectResponse
    {
        if ($request->route()?->getName() === 'filament.customer.auth.logout') {
            return redirect()->to(Filament::getPanel('customer')->getLoginUrl());
        }

        return redirect()->route('filament.admin.auth.login');
    }
}
