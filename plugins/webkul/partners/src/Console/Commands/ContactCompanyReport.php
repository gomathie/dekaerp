<?php

namespace Webkul\Partner\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Read-only report: what would happen if contacts were scoped per company.
 *
 * Contacts are currently shared. Upstream made them so deliberately in
 * commit 57e23422 ("Fixed shared partner issue"), which added
 * Partner::autoAssignsCompany() returning false and a migration that nulled
 * company_id on every partner not tied to a user. Scoping them per company is
 * therefore a fork from upstream, and the thing that decides whether it is
 * safe is data rather than code: a contact referenced by two companies'
 * documents cannot be filed under one of them without the other losing sight
 * of it - silently, because a scoped relation read returns null rather than
 * raising.
 *
 * This command writes nothing. Run it before the backfill:
 *
 *     php artisan partners:company-report
 *
 * Raw queries rather than Eloquent on purpose: it inspects the schema to find
 * every table that references a partner, so there is no model to go through,
 * and it must not trigger the global scopes it is reporting on.
 */
class ContactCompanyReport extends Command
{
    protected $signature = 'partners:company-report {--conflicts=20 : How many conflicting contacts to list}';

    protected $description = 'Report what a per-company backfill of contacts would do (read-only)';

    public function handle(): int
    {
        if (! Schema::hasTable('partners_partners')) {
            $this->error('The partners_partners table does not exist.');

            return self::FAILURE;
        }

        $this->components->info('Contacts: current company assignment');

        $total = DB::table('partners_partners')->whereNull('deleted_at')->count();
        $scoped = DB::table('partners_partners')->whereNull('deleted_at')->whereNotNull('company_id')->count();
        $shared = $total - $scoped;

        $this->table(['', 'Contacts'], [
            ['Total (not deleted)', number_format($total)],
            ['Already assigned to a company', number_format($scoped)],
            ['Shared / unassigned', number_format($shared)],
        ]);

        if ($shared === 0) {
            $this->components->info('Nothing to backfill.');

            return self::SUCCESS;
        }

        $fallbackCompanyId = DB::table('companies')->orderBy('id')->value('id');

        if ($fallbackCompanyId === null) {
            $this->error('There are no companies, so there is nothing to backfill into.');

            return self::FAILURE;
        }

        $this->reportResolution($fallbackCompanyId);
        $this->reportConflicts();

        $this->newLine();
        $this->components->warn('This command changed nothing. The backfill is a separate, deliberate step.');

        return self::SUCCESS;
    }

    /**
     * How each unassigned contact would be resolved under the agreed rule:
     * the creating user's default company, otherwise the oldest company.
     */
    protected function reportResolution(int $fallbackCompanyId): void
    {
        $this->components->info('How unassigned contacts would be resolved');

        $viaCreator = DB::table('partners_partners as p')
            ->join('users as u', 'u.id', '=', 'p.creator_id')
            ->whereNull('p.deleted_at')
            ->whereNull('p.company_id')
            ->whereNotNull('u.default_company_id')
            ->count();

        $viaParent = DB::table('partners_partners as p')
            ->whereNull('p.deleted_at')
            ->whereNull('p.company_id')
            ->whereNotNull('p.parent_id')
            ->count();

        $shared = DB::table('partners_partners')->whereNull('deleted_at')->whereNull('company_id')->count();

        $fallbackName = DB::table('companies')->where('id', $fallbackCompanyId)->value('name');

        $this->table(['Resolution', 'Contacts'], [
            ['By the creating user’s default company', number_format($viaCreator)],
            ['Children that must inherit their parent instead', number_format($viaParent)],
            ['Falling back to the oldest company ('.$fallbackName.')', number_format(max(0, $shared - $viaCreator))],
        ]);

        $this->line('  Note: there is no record of which company was active when a contact was created.');
        $this->line('  The creating user’s <options=bold>current</> default company is the closest available signal.');
    }

    /**
     * Contacts referenced by more than one company's records.
     *
     * These are the ones that cannot be filed under a single company without
     * breaking something, so they are the whole reason to run this first.
     */
    protected function reportConflicts(): void
    {
        $this->components->info('Contacts referenced by more than one company');

        $tables = $this->tablesReferencingPartners();

        if ($tables === []) {
            $this->line('  No table carries both partner_id and company_id, so there is nothing to conflict.');

            return;
        }

        $this->line('  Checking '.count($tables).' table(s): '.implode(', ', $tables));

        $unions = [];

        foreach ($tables as $table) {
            $unions[] = 'SELECT partner_id, company_id FROM '.$table
                .' WHERE partner_id IS NOT NULL AND company_id IS NOT NULL';
        }

        $sql = 'SELECT partner_id, COUNT(DISTINCT company_id) AS companies FROM ('
            .implode(' UNION ALL ', $unions)
            .') AS refs GROUP BY partner_id HAVING COUNT(DISTINCT company_id) > 1 ORDER BY companies DESC, partner_id';

        $rows = DB::select($sql);

        if ($rows === []) {
            $this->components->info('None. Every referenced contact is used by a single company, so the backfill is safe.');

            return;
        }

        $this->components->error(count($rows).' contact(s) are referenced by several companies.');
        $this->line('  Filing these under one company hides them from the others. Leave them shared.');

        $limit = (int) $this->option('conflicts');

        $display = [];

        foreach (array_slice($rows, 0, $limit) as $row) {
            $display[] = [
                $row->partner_id,
                DB::table('partners_partners')->where('id', $row->partner_id)->value('name'),
                $row->companies,
            ];
        }

        $this->table(['Contact id', 'Name', 'Companies'], $display);

        if (count($rows) > $limit) {
            $this->line('  ... and '.(count($rows) - $limit).' more. Use --conflicts=N to list more.');
        }
    }

    /**
     * Every existing table that carries both a partner_id and a company_id
     * that are actually foreign keys.
     *
     * Discovered from the schema rather than hardcoded: sixteen plugins
     * reference partners, and which of them are installed varies per
     * deployment.
     *
     * Both columns must be an integer type, and that is not a formality.
     * `companies.company_id` is a unique *string* - the company's own
     * registration identifier, a text input on the company form - and nothing
     * to do with owning a tenant. Matching on the column name alone pulled
     * `companies` in and the UNION then died with "bigint and character
     * varying cannot be matched". Requiring an integer excludes that column on
     * principle rather than by naming it, so any other table that reuses the
     * name for a label is excluded too.
     *
     * @return array<int, string>
     */
    protected function tablesReferencingPartners(): array
    {
        $rows = DB::select(<<<'SQL'
            SELECT table_name
            FROM information_schema.columns
            WHERE table_schema = current_schema()
              AND column_name IN ('partner_id', 'company_id')
              AND data_type IN ('bigint', 'integer', 'smallint')
            GROUP BY table_name
            HAVING COUNT(DISTINCT column_name) = 2
            ORDER BY table_name
        SQL);

        $tables = [];

        foreach ($rows as $row) {
            $table = $row->table_name;

            // The partners table itself is the subject, not a reference to it.
            if ($table === 'partners_partners') {
                continue;
            }

            $tables[] = $table;
        }

        return $tables;
    }
}
