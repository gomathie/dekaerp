<?php

namespace App\Providers;

use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

/**
 * Registers the "tenant-s3" filesystem driver.
 *
 * Every upload in this application is written through Storage::disk('public'),
 * hardcoded in roughly 27 places across the upstream plugins. Rather than edit
 * those call sites - which would diverge from upstream permanently - this
 * driver changes what that disk *is*: object storage, private, with every path
 * transparently prefixed by the owning company.
 *
 *     companies/{company_id}/users/avatars/xyz.png
 *
 * Laravel's own S3 driver already applies $config['root'] as an object-key
 * prefix, so the tenant segment costs nothing beyond computing it.
 *
 * Nothing changes until FILESYSTEM_PUBLIC_DRIVER=tenant-s3; local development
 * keeps the plain local driver.
 */
class TenantFilesystemServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Storage::extend('tenant-s3', function ($app, array $config) {
            $config['driver'] = 's3';
            $config['root'] = $this->tenantRoot($config['root'] ?? '');

            /** @var FilesystemManager $manager */
            $manager = $app->make('filesystem');

            return $manager->createS3Driver($config);
        });
    }

    /**
     * Build the object-key prefix for the company in context.
     *
     * Deliberately throws when there is no company. Queue jobs and artisan
     * commands run without one - CompanyScope skips itself entirely in console
     * context - so a fallback here would silently file one tenant's documents
     * under another, or under a shared prefix that every tenant can reach.
     * Failing is recoverable; mis-filing is not.
     */
    protected function tenantRoot(string $configuredRoot): string
    {
        $companyId = current_company_id();

        if (blank($companyId)) {
            throw new RuntimeException(
                'The tenant-s3 disk was resolved without a company in context, so there is '
                .'no safe prefix to write under. This normally means a queued job, artisan '
                .'command or unauthenticated request touched Storage::disk("public"). Set the '
                .'company context explicitly for that code path rather than relaxing this check.'
            );
        }

        $base = trim($configuredRoot, '/');

        return ($base !== '' ? $base.'/' : '')."companies/{$companyId}";
    }
}
