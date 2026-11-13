<?php

namespace Webkul\Support\Filament;

use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * Turns a Filament FileUpload state from an action modal into a readable
 * local path, for imports (contacts, bank statements).
 *
 * Moved from Partners' ListPartners on 2026-11-13 so the bank statement
 * import (accounting plan A3) reuses it instead of a second copy.
 *
 * In an action modal Filament leaves the state as a TemporaryUploadedFile,
 * which is the normal case and needs no copy. The string branch covers a
 * file already stored on a disk: that disk may be S3, where there is no
 * local path at all, so the contents are streamed to a temporary file and
 * the caller deletes it afterwards (third element true).
 */
class ImportUpload
{
    /**
     * @return array{0: string, 1: string, 2: bool}|null path, extension, is a temporary copy
     */
    public static function resolve(mixed $file, string $prefix = 'import-'): ?array
    {
        if (is_array($file)) {
            $file = reset($file);
        }

        if ($file instanceof TemporaryUploadedFile) {
            $path = $file->getRealPath();

            if (! is_string($path) || ! is_file($path)) {
                return null;
            }

            return [$path, $file->getClientOriginalExtension(), false];
        }

        if (! is_string($file) || $file === '') {
            return null;
        }

        $disk = Storage::disk(config('filament.default_filesystem_disk'));

        if (! $disk->exists($file)) {
            return null;
        }

        $extension = pathinfo($file, PATHINFO_EXTENSION);
        $temporaryPath = tempnam(sys_get_temp_dir(), $prefix);

        if ($temporaryPath === false) {
            return null;
        }

        file_put_contents($temporaryPath, $disk->get($file));

        return [$temporaryPath, $extension, true];
    }
}
