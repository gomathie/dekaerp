<?php

namespace Webkul\Support\Http\Controllers;

use Filament\Facades\Filament;
use Symfony\Component\HttpFoundation\Response;
use Webkul\Support\Services\ApiReference;

/**
 * The full API reference at /api/docs, its OpenAPI document and its Postman
 * collection (user decision, 2026-11-25: sign-in only).
 *
 * Replaces the routes Scribe registers itself (`scribe.laravel.add_routes` is
 * off). Those served storage/app/private/scribe/openapi.yaml, which only exists
 * where someone ran `scribe:generate`, so production answered 500 on every
 * visit (Sentry DEKAERP-26). The documents are now built from the committed
 * .scribe/endpoints files by ApiReference, the same data the Help page's
 * Developers section reads.
 *
 * Sign-in only, always: the reference lists every endpoint of every module,
 * which is more than an outsider needs to know. A guest is sent to the panel
 * login and comes back here afterwards. All three routes check, not just the
 * page, or the spec URL would publish the same content.
 */
class ApiDocsController
{
    public function page(): Response
    {
        return $this->guard() ?? response()->view('support::api-docs', [
            'title'   => (string) config('scribe.title'),
            'specUrl' => route('scribe.openapi'),
        ]);
    }

    public function openapi(): Response
    {
        return $this->guard() ?? response()->file(app(ApiReference::class)->openApiPath(), [
            'Content-Type'  => 'application/json',
            'Cache-Control' => 'private, no-cache',
        ]);
    }

    public function postman(): Response
    {
        return $this->guard() ?? response()->file(app(ApiReference::class)->postmanPath(), [
            'Content-Type'        => 'application/json',
            'Cache-Control'       => 'private, no-cache',
            'Content-Disposition' => 'attachment; filename="collection.json"',
        ]);
    }

    /**
     * A redirect for a guest, a refusal for an account that may not use the
     * panel, or null to carry on.
     *
     * The web guard by name: the default guard is not always `web` (see
     * AGENTS.md), and these are browser pages. canAccessPanel() turns away a
     * deactivated user whose session outlived the deactivation, which the
     * panel itself would also refuse.
     */
    protected function guard(): ?Response
    {
        $panel = Filament::getPanel('admin');
        $user = auth()->guard('web')->user();

        if (! $user) {
            return redirect()->guest($panel->getLoginUrl());
        }

        abort_unless(method_exists($user, 'canAccessPanel') && $user->canAccessPanel($panel), 403);

        return null;
    }
}
