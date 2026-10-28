<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One membership row per (user, company).
     *
     * Eloquent's `sync()` usually avoids duplicates, but nothing else did: raw
     * writes, imports and two concurrent requests could each add a row, and the
     * new tenant-role pivot keys on the same pair. The audit recorded the gap
     * (docs/saas-administration-billing-audit-plan.md, "Known related
     * defects"), so this migration closes it - but refuses to guess what a
     * duplicate row meant.
     */
    public function up(): void
    {
        $duplicates = DB::table('user_allowed_companies')
            ->select('user_id', 'company_id', DB::raw('COUNT(*) AS occurrences'))
            ->groupBy('user_id', 'company_id')
            ->havingRaw('COUNT(*) > 1')
            ->orderBy('user_id')
            ->get();

        if ($duplicates->isNotEmpty()) {
            $sample = $duplicates
                ->take(20)
                ->map(fn (object $row): string => sprintf(
                    'user %d / company %d (%d rows)',
                    (int) $row->user_id,
                    (int) $row->company_id,
                    (int) $row->occurrences,
                ))
                ->implode('; ');

            throw new RuntimeException(
                'user_allowed_companies holds duplicate (user_id, company_id) pairs, so the unique '
                .'membership index cannot be created without discarding data. Resolve these first: '.$sample
                .' - total duplicate pairs: '.$duplicates->count().'.'
            );
        }

        Schema::table('user_allowed_companies', function (Blueprint $table): void {
            $table->unique(['user_id', 'company_id'], 'user_allowed_companies_user_company_unique');
        });
    }

    public function down(): void
    {
        Schema::table('user_allowed_companies', function (Blueprint $table): void {
            $table->dropUnique('user_allowed_companies_user_company_unique');
        });
    }
};
