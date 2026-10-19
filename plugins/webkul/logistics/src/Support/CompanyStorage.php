<?php

namespace Webkul\Logistics\Support;

use Illuminate\Support\Facades\Storage;
use Webkul\Support\Services\CompanyContext;

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
