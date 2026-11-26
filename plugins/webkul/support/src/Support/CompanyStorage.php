<?php

namespace Webkul\Support\Support;

use Illuminate\Support\Facades\Storage;
use Webkul\Support\Services\CompanyContext;

/**
 * Files on the public disk under a record's own company prefix.
 *
 * With the tenant-s3 driver the disk adds `companies/<id>/` from the active
 * company, so a file must be read and written while the record's company is
 * active, not the actor's. Promoted from Logistics (2026-11-26, Expenses
 * receipts) so core plugins can share one rule; Logistics keeps its class
 * name as a subclass.
 */
class CompanyStorage
{
    public static function url(int $companyId, string $path): string
    {
        if (config('filesystems.disks.public.driver') !== 'tenant-s3') {
            return Storage::disk('public')->url($path);
        }

        return route('secure-storage', ['path' => static::objectKey($companyId, $path)]);
    }

    public static function objectKey(int $companyId, string $path): string
    {
        $root = trim((string) config('filesystems.disks.public.root'), '/');

        return implode('/', array_filter([
            $root,
            'companies/'.$companyId,
            ltrim($path, '/'),
        ], fn (string $segment): bool => $segment !== ''));
    }

    /**
     * Run storage work under the owning tenant prefix, then restore the request context.
     */
    public static function run(int $companyId, callable $callback): mixed
    {
        $context = app(CompanyContext::class);
        $activeIds = $context->activeIds();
        $currentId = $context->currentId();
        $usesTenantDisk = config('filesystems.disks.public.driver') === 'tenant-s3';

        $context->setActive(array_values(array_unique([...$activeIds, $companyId])), $companyId);

        if ($usesTenantDisk) {
            Storage::forgetDisk('public');
        }

        try {
            return $callback();
        } finally {
            if ($usesTenantDisk) {
                Storage::forgetDisk('public');
            }

            $context->setActive($activeIds, $currentId);

            if ($usesTenantDisk) {
                Storage::forgetDisk('public');
            }
        }
    }
}
