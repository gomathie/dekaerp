<?php

namespace Webkul\Security\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Webkul\Security\Models\User;
use Webkul\Support\Filament\Pages\Profile;

/**
 * Holds a user on the profile page until they replace a password an
 * administrator set for them.
 *
 * Without this the `must_change_password` checkbox would be decoration: the
 * flag would be stored, nothing would read it, and a password shared over chat
 * would stay valid forever while the UI implied otherwise.
 *
 * Registered on the admin panel's authMiddleware, so it only sees authenticated
 * panel routes. Livewire's own endpoint is not a panel route and is therefore
 * never blocked - which is what lets the profile form actually submit.
 *
 * The target is Support's Profile page, not Filament's built-in one. Both are
 * registered on this panel, but only Support's takes a current password and a
 * new one, and it is the page the user menu links to; sending someone to the
 * built-in page would be sending them somewhere they cannot comply.
 */
class RequirePasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user instanceof User || ! $user->must_change_password) {
            return $next($request);
        }

        try {
            $profileUrl = Profile::getUrl();
        } catch (Throwable) {
            // The page is not registered on this panel, so there is nowhere to
            // send them. Letting the request through beats a redirect loop or a
            // 500: the flag stays set and an administrator can still clear it.
            return $next($request);
        }

        if ($this->isAllowed($request, $profileUrl)) {
            return $next($request);
        }

        return redirect()->to($profileUrl);
    }

    /**
     * The profile page itself, and the routes needed to leave the panel, must
     * stay reachable or the user can neither comply nor log out.
     */
    protected function isAllowed(Request $request, string $profileUrl): bool
    {
        if ($request->url() === strtok($profileUrl, '?')) {
            return true;
        }

        $routeName = (string) $request->route()?->getName();

        foreach (['.auth.logout', '.auth.login', '.auth.password-reset.request', '.auth.password-reset.reset'] as $suffix) {
            if (str_ends_with($routeName, $suffix)) {
                return true;
            }
        }

        return false;
    }
}
