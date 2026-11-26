<?php

namespace Webkul\Sale\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Webkul\Sale\Services\ZohoImport\ZohoExport;
use Webkul\Sale\Services\ZohoImport\ZohoImporter;
use Webkul\Sale\Services\ZohoImport\ZohoImportException;
use Webkul\Support\Models\Company;

/**
 * Step 2 of a Zoho Books migration: record the export in the company,
 * following an approved mapping (zoho:map). Safe to run again: what was
 * imported before is skipped. --dry-run does everything and rolls it back.
 */
class ZohoImport extends Command
{
    protected $signature = 'zoho:import
                            {company : DEKA company id}
                            {file : Path to the Zoho Books export (JSON)}
                            {--mapping= : The approved mapping.json from zoho:map}
                            {--dry-run : Do everything, then roll it back}
                            {--force : Import although the company already has documents of its own}
                            {--before-cutoff : After the initial load: import only records dated before its cut-off}';

    protected $description = 'Import a Zoho Books export into one company, following an approved mapping';

    public function handle(ZohoImporter $importer): int
    {
        // Runs as the console: no acting user, the company forced by the
        // importer. A user left signed in by an earlier step must not decide
        // whose company the records are written into.
        // A full history is hundreds of documents; a CLI time limit would
        // stop it half way (it would then continue on the next run).
        set_time_limit(0);

        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        }

        $company = Company::withoutGlobalScopes()->find((int) $this->argument('company'));

        if (! $company) {
            $this->error('No company with that id.');

            return self::FAILURE;
        }

        $mapping = json_decode((string) @file_get_contents((string) $this->option('mapping')), true);

        if (! is_array($mapping)) {
            $this->error('Give --mapping, the approved mapping.json from zoho:map.');

            return self::FAILURE;
        }

        try {
            $export = ZohoExport::fromFile((string) $this->argument('file'));

            $counts = $importer->import(
                $company,
                $export,
                $mapping,
                dryRun: (bool) $this->option('dry-run'),
                force: (bool) $this->option('force'),
                output: fn (string $line) => $this->line($line),
                beforeCutoff: (bool) $this->option('before-cutoff'),
            );

            // The cut-off every later import is held to (user, 2026-11-26).
            if (! $this->option('dry-run')) {
                ZohoImporter::recordInitialLoad((int) $company->id, null, $export->exportedAt(), $counts, $mapping);
            }
        } catch (ZohoImportException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->table(['What', 'Count'], collect($counts)->map(fn ($count, $what) => [$what, $count])->values()->all());

        $this->info($this->option('dry-run') ? 'Dry run: nothing was kept.' : 'Import finished. Run zoho:compare next.');

        return self::SUCCESS;
    }
}
