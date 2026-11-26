<?php

namespace Webkul\Sale\Console\Commands;

use Illuminate\Console\Command;
use Webkul\Sale\Services\ZohoImport\MappingProposer;
use Webkul\Sale\Services\ZohoImport\ZohoExport;
use Webkul\Sale\Services\ZohoImport\ZohoImportException;
use Webkul\Support\Models\Company;
use Webkul\Support\Services\CompanyContext;

/**
 * Step 1 of a Zoho Books migration: propose how the export maps onto the
 * company (accounts, taxes, banks, items, contacts, numbering) and write it
 * as JSON plus a readable Markdown table for the user to approve.
 *
 * Read-only. The output holds client data, so it is written where the
 * operator says, never into the repository or the logs.
 */
class ZohoMap extends Command
{
    protected $signature = 'zoho:map
                            {company : DEKA company id}
                            {file : Path to the Zoho Books export (JSON)}
                            {--output= : Directory for mapping.json and mapping.md (outside the repository)}
                            {--numbering=deka : New documents after the import: deka (the DEKA yearly series) or zoho (continue the Zoho counter)}
                            {--keep-samples : Also import Zoho demo contacts no document uses}';

    protected $description = 'Propose a Zoho Books to DEKA mapping for one company (writes nothing to the database)';

    public function handle(MappingProposer $proposer, CompanyContext $context): int
    {
        $company = Company::withoutGlobalScopes()->find((int) $this->argument('company'));

        if (! $company) {
            $this->error('No company with that id.');

            return self::FAILURE;
        }

        $output = rtrim((string) $this->option('output'), '/\\');

        if ($output === '' || ! is_dir($output) || ! is_writable($output)) {
            $this->error('Give --output, an existing directory you can write to, outside the repository.');

            return self::FAILURE;
        }

        if (str_starts_with(realpath($output) ?: $output, base_path())) {
            $this->error('The mapping holds client data: write it outside the repository.');

            return self::FAILURE;
        }

        try {
            $export = ZohoExport::fromFile((string) $this->argument('file'));

            $mapping = $context->runFor($company->id, fn (): array => $proposer->propose($company, $export, [
                'numbering'           => (string) $this->option('numbering'),
                'skip_unused_samples' => ! $this->option('keep-samples'),
            ]));
        } catch (ZohoImportException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        file_put_contents($output.'/mapping.json', json_encode($mapping, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        file_put_contents($output.'/mapping.md', $proposer->markdown($mapping));

        $actions = collect($mapping['accounts'])->countBy('action');

        $this->info('Mapping written to '.$output.'/mapping.json and mapping.md.');
        $this->line('Accounts: '.$actions->map(fn ($count, $action) => "{$count} {$action}")->implode(', ').'.');
        $this->line('Taxes to create: '.collect($mapping['taxes'])->where('action', 'create')->count().'.');
        $this->warn('Review and approve it (set "approved": true) before running zoho:import.');

        return self::SUCCESS;
    }
}
