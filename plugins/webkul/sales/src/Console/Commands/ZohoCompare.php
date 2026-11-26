<?php

namespace Webkul\Sale\Console\Commands;

use Illuminate\Console\Command;
use Webkul\Sale\Services\ZohoImport\ZohoComparison;
use Webkul\Sale\Services\ZohoImport\ZohoExport;
use Webkul\Sale\Services\ZohoImport\ZohoImportException;
use Webkul\Support\Models\Company;

/**
 * Step 3 of a Zoho Books migration: Zoho against DEKA, row by row, OK or
 * DIFF. --anonymous replaces customer names with numbers, for sharing.
 */
class ZohoCompare extends Command
{
    protected $signature = 'zoho:compare
                            {company : DEKA company id}
                            {file : Path to the Zoho Books export (JSON)}
                            {--mapping= : The mapping.json the import followed}
                            {--anonymous : Number the customers instead of naming them}
                            {--only-diff : Show only the rows that differ}';

    protected $description = 'Compare a Zoho Books export with what DEKA holds for the company';

    public function handle(ZohoComparison $comparison): int
    {
        $company = Company::withoutGlobalScopes()->find((int) $this->argument('company'));
        $mapping = json_decode((string) @file_get_contents((string) $this->option('mapping')), true);

        if (! $company || ! is_array($mapping)) {
            $this->error('Give an existing company id and --mapping, the mapping.json the import followed.');

            return self::FAILURE;
        }

        try {
            $export = ZohoExport::fromFile((string) $this->argument('file'));
            $rows = $comparison->compare($company, $export, $mapping, (bool) $this->option('anonymous'));
            $unmatched = $comparison->unmatched($company, $export, $mapping, (bool) $this->option('anonymous'));
        } catch (ZohoImportException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $shown = $this->option('only-diff') ? array_filter($rows, fn ($row) => $row['status'] === 'DIFF') : $rows;

        $this->table(
            ['Section', 'Row', 'Zoho', 'DEKA', 'Difference', ''],
            array_map(fn ($row) => [
                $row['section'],
                $row['label'],
                number_format($row['zoho'], 2),
                number_format($row['deka'], 2),
                number_format($row['difference'], 2),
                $row['status'],
            ], $shown),
        );

        if ($unmatched === []) {
            $this->info('Every invoice, payment and expense is in both systems.');
        } else {
            $this->warn(count($unmatched).' document(s) are in one system only:');

            foreach ($unmatched as $document) {
                $this->line('  '.$document['text']);
            }
        }

        $differences = count(array_filter($rows, fn ($row) => $row['status'] === 'DIFF'));

        if ($differences > 0 || $unmatched !== []) {
            $this->warn("{$differences} of ".count($rows).' rows differ.');

            return self::FAILURE;
        }

        $this->info('All '.count($rows).' rows match.');

        return self::SUCCESS;
    }
}
