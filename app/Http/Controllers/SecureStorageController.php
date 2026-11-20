<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Webkul\Support\Services\CompanyContext;

/**
 * Serves objects from the tenant bucket behind authorization.
 *
 * Uploads are written through Storage::disk('public'), which in production is
 * the tenant-s3 driver: a private bucket with every key prefixed by the owning
 * company (companies/{id}/...). Private objects have no publicly fetchable URL,
 * so the disk's "url" is pointed at this route instead - see
 * config/filesystems.php. Every Filament component that calls ->url() therefore
 * produces a link that lands here, with no change to the ~30 upstream call
 * sites that hardcode disk('public').
 *
 * Authorization is by company, not by URL secrecy. A signed temporary URL would
 * let anyone holding the link read the file regardless of which company they
 * belong to; this checks the company segment of the key against the companies
 * the authenticated user is actually allowed to see.
 */
class SecureStorageController extends Controller
{
    /**
     * The disk used to read objects back.
     *
     * Deliberately not 'public'. That disk resolves through tenant-s3, which
     * prefixes every path with the *current* company - asking it for
     * "companies/3/x.png" would look up "companies/3/companies/3/x.png". The
     * plain s3 disk shares the same bucket and credentials with no prefix, so
     * the full object key can be used as-is.
     */
    protected const OBJECT_DISK = 's3';

    public function __invoke(Request $request, string $path): StreamedResponse
    {
        $path = ltrim($path, '/');

        // Traversal cannot escape a bucket the way it escapes a filesystem, but
        // a key containing ".." would still resolve to a different object than
        // the one authorized below.
        if (str_contains($path, '..')) {
            abort(404);
        }

        $companyId = $this->companyIdFor($path);

        if ($companyId === null) {
            abort(404);
        }

        if (! in_array($companyId, app(CompanyContext::class)->allowedIds(), true)) {
            // 404 rather than 403: whether an object exists under another
            // company is itself information this user should not have.
            abort(404);
        }

        $disk = Storage::disk(self::OBJECT_DISK);

        if (! $disk->exists($path)) {
            abort(404);
        }

        $response = $disk->response($path);

        // private, never public: these responses vary by authenticated user, so
        // a shared or edge cache must not be allowed to hold and replay them.
        $response->headers->set('Cache-Control', 'private, max-age=3600');

        return $response;
    }

    /**
     * Extract the owning company from an object key.
     *
     * Keys look like companies/{id}/users/avatars/x.png, optionally behind the
     * disk's configured root when AWS_ROOT is set.
     */
    protected function companyIdFor(string $path): ?int
    {
        $base = trim((string) config('filesystems.disks.public.root'), '/');

        if ($base !== '' && str_starts_with($path, $base.'/')) {
            $path = substr($path, strlen($base) + 1);
        }

        if (! preg_match('#^companies/(\d+)/#', $path, $matches)) {
            return null;
        }

        return (int) $matches[1];
    }
}
