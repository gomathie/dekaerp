<?php

namespace App\Providers;

use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Throwable;

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
    /**
     * Registered rather than booted.
     *
     * Laravel runs every provider's register() before any provider's boot(),
     * and the disk is resolved during boot by packages we do not control -
     * guava/filament-icon-picker creates its icon directory the moment
     * IconFactory resolves. Extending in boot() left that ordering to chance,
     * and package:discover failed the build with "Driver [tenant-s3] is not
     * supported" because our provider had not booted yet.
     */
    public function register(): void
    {
        // FilesystemManager::extend() rebinds the callback's $this to itself.
        // Calling $this->tenantRoot() inside the closure therefore resolved
        // against the manager, missed, fell through to __call - which forwards
        // to $this->disk(), the default disk, which is this very driver - and
        // recursed until the process died with a segfault.
        //
        // This arrow function keeps $this bound to the provider because it is
        // never handed to extend(), so it is never rebound.
        $tenantRoot = fn (string $configuredRoot): string => $this->tenantRoot($configuredRoot);

        Storage::extend('tenant-s3', function ($app, array $config) use ($tenantRoot) {
            $config['driver'] = 's3';
            $config['root'] = $tenantRoot($config['root'] ?? '');

            /** @var FilesystemManager $manager */
            $manager = $app->make('filesystem');

            return $manager->createS3Driver($config);
        });
    }

    /**
     * Build the object-key prefix for the company in context.
     *
     * With no company, objects go under _system rather than under a tenant.
     * This is not a relaxation of the isolation guarantee - it is what makes it
     * hold. Tenant prefixes are always companies/{int}, so _system can never be
     * mistaken for one, and SecureStorageController serves only companies/{id}/
     * paths, so nothing written here is reachable by any user through any URL.
     *
     * The original version threw instead. That was wrong for a reason worth
     * recording: the disk is legitimately resolved with no user at all, during
     * package:discover, because guava/filament-icon-picker creates its icon
     * directory when IconFactory resolves. Throwing there failed the build.
     *
     * A tenant document reaching _system - from a queued job that forgot to set
     * company context, say - is still a bug. But it surfaces as a file nobody
     * can read, which is loud and harmless, rather than as one company quietly
     * holding another company's documents. The warning below is what makes it
     * findable.
     */
    protected function tenantRoot(string $configuredRoot): string
    {
        $base = trim($configuredRoot, '/');
        $base = $base !== '' ? $base.'/' : '';

        $companyId = current_company_id();

        if (blank($companyId)) {
            // Never let diagnostics break the disk: this runs during early boot,
            // where the log stack may not be usable yet.
            try {
                Log::warning('tenant-s3 resolved with no company in context; using the _system prefix.', [
                    'console' => app()->runningInConsole(),
                    'command' => app()->runningInConsole() ? implode(' ', array_slice($_SERVER['argv'] ?? [], 1, 2)) : null,
                ]);
            } catch (Throwable) {
                // Nothing to do - the prefix below is still correct.
            }

            return $base.'_system';
        }

        return $base."companies/{$companyId}";
    }
}
