<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Serves brand logos and favicons without authentication.
 *
 * The login and password-reset pages are unauthenticated, so they cannot use
 * SecureStorageController - it authorizes by company, and there is no user or
 * company in context yet. This route exists for that narrow case and must
 * stay narrow: it resolves a single filename inside branding/ and nothing
 * else.
 *
 * It deliberately never accepts a path. Under the tenant-s3 driver the public
 * disk shares a bucket with invoice PDFs, chatter attachments and avatars, so
 * an unauthenticated endpoint that took a caller-supplied key would serve any
 * of them - and, if it searched across company prefixes, would serve them
 * across tenants too. The filename is basename()'d here as well as being
 * constrained by the route pattern, so the guarantee does not depend on the
 * route definition staying as it is.
 *
 * Consequence worth knowing: a logo uploaded with a company in context lives
 * under companies/{id}/branding/, so it resolves on authenticated pages but
 * not on the login page, which has no company and reads _system/branding/.
 * The login page falls back to the bundled logo rather than guessing which
 * tenant's brand an anonymous visitor should see.
 */
class BrandingController extends Controller
{
    public function __invoke(Request $request, string $filename): Response
    {
        $filename = basename($filename);

        if ($filename === '' || str_starts_with($filename, '.')) {
            abort(404);
        }

        return $this->resolveFileResponse($filename) ?? $this->fallbackResponse($filename);
    }

    /**
     * Locate the asset in branding/, or return null to fall back.
     */
    protected function resolveFileResponse(string $filename): ?Response
    {
        $localPublicPath = public_path('branding/'.$filename);

        if (is_file($localPublicPath)) {
            return response()->file($localPublicPath, [
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }

        // With the local driver this is storage/app/public/branding; under
        // tenant-s3 the disk applies its own prefix - _system/branding with no
        // company in context, companies/{id}/branding with one.
        try {
            $disk = Storage::disk('public');
            $key = 'branding/'.$filename;

            if ($disk->exists($key)) {
                return $disk->response($key, null, [
                    'Cache-Control' => 'public, max-age=86400',
                ]);
            }
        } catch (Throwable) {
            // An unreachable object store must not break the login page; the
            // bundled fallback below is served instead.
        }

        return null;
    }

    /**
     * Serve the bundled asset when the configured one is missing.
     */
    protected function fallbackResponse(string $filename): Response
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if ($extension === 'ico' || str_contains($filename, 'favicon')) {
            $faviconPath = public_path('images/favicon.ico');

            if (is_file($faviconPath)) {
                return response()->file($faviconPath, [
                    'Cache-Control' => 'public, max-age=3600',
                ]);
            }
        }

        $logoPath = public_path('images/logo.svg');

        if (is_file($logoPath)) {
            return response()->file($logoPath, [
                'Content-Type'  => 'image/svg+xml',
                'Cache-Control' => 'public, max-age=3600',
            ]);
        }

        abort(404);
    }
}
