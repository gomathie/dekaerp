<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * Points branding settings back at the bundled assets when the uploaded file
 * they name is gone - container redeploys wiped local storage, leaving rows
 * referencing objects that 404.
 *
 * Two things this is careful about, because it edits live settings:
 *
 * - It only rewrites a row when a lookup actually completed and reported the
 *   file missing. If the object store cannot be reached, "unknown" is left
 *   alone rather than treated as "absent".
 * - It resolves keys under the row's own company prefix. Migrations run with
 *   no company in context, so asking the public disk would check _system/ and
 *   report every company's logo missing - wiping settings that are fine.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        $hasCompanyColumn = Schema::hasColumn('settings', 'company_id');

        $records = DB::table('settings')
            ->where('group', 'branding')
            ->whereIn('name', ['light_logo', 'dark_logo', 'favicon'])
            ->get();

        foreach ($records as $record) {
            $payload = json_decode($record->payload, true);

            if (! is_string($payload) || blank($payload)) {
                continue;
            }

            // Already a bundled asset shipped in public/.
            if (str_starts_with($payload, 'images/')) {
                continue;
            }

            $status = $this->locate($payload, $hasCompanyColumn ? $record->company_id : null);

            if ($status !== 'missing') {
                continue;
            }

            DB::table('settings')
                ->where('id', $record->id)
                ->update([
                    'payload' => json_encode(
                        $record->name === 'favicon' ? 'images/favicon.ico' : 'images/logo.svg'
                    ),
                ]);
        }
    }

    public function down(): void {}

    /**
     * @return 'found'|'missing'|'unknown'
     */
    private function locate(string $payload, mixed $companyId): string
    {
        if (file_exists(public_path($payload))) {
            return 'found';
        }

        // Tracks whether any store actually answered. If none did, the file's
        // absence is unproven and the setting is left alone.
        $answered = false;

        try {
            if (Storage::disk('public')->exists($payload)) {
                return 'found';
            }

            $answered = true;
        } catch (Throwable) {
        }

        // Checked through the raw s3 disk so the key is explicit rather than
        // depending on whatever company context the migration runs in: objects
        // live under the owning company's prefix, or _system when uploaded
        // without one.
        try {
            $disk = Storage::disk('s3');

            $keys = ['_system/'.$payload, $payload];

            if (filled($companyId)) {
                array_unshift($keys, "companies/{$companyId}/{$payload}");
            }

            foreach ($keys as $key) {
                if ($disk->exists($key)) {
                    return 'found';
                }
            }

            $answered = true;
        } catch (Throwable) {
        }

        return $answered ? 'missing' : 'unknown';
    }
};
