<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Follows the partners migration of the same day, which gave every
     * shared contact a company (user, 2026-11-10). An employee with no
     * company was shown in every company while its contact now belongs to
     * one, so elsewhere it would point at a contact that company cannot
     * read. The employee takes its contact's company. Supersedes "example
     * employees 1 to 10 stay shared" (2026-10-29, §4k).
     */
    public function up(): void
    {
        if (! Schema::hasTable('employees_employees') || ! Schema::hasTable('partners_partners')) {
            return;
        }

        $rows = DB::table('employees_employees as e')
            ->join('partners_partners as p', 'p.id', '=', 'e.partner_id')
            ->whereNull('e.company_id')
            ->whereNotNull('p.company_id')
            ->get(['e.id', 'p.company_id']);

        foreach ($rows as $row) {
            DB::table('employees_employees')
                ->where('id', $row->id)
                ->whereNull('company_id')
                ->update(['company_id' => $row->company_id]);
        }
    }

    /**
     * Not reversible: which employees were shared is not recorded.
     */
    public function down(): void
    {
        //
    }
};
