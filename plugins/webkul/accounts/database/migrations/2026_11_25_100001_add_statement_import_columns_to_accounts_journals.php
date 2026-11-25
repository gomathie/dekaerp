<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Clean reconciliation (plan #9 R-4, user 2026-11-25).
 *
 * - accounts_journals.statement_import_columns: the column mapping of the
 *   last successful statement import into that bank or cash journal, so the
 *   next import is pre-filled. On the journal, not a settings table: a
 *   journal belongs to exactly one company, so the mapping cannot leak to
 *   another one, and it goes when the journal goes. Nullable: every
 *   existing journal simply has none yet.
 * - Statement balances (backfill): imports before this change left
 *   balance_start and balance_end_real at their default 0, so the new
 *   balance check would read every existing statement as closing at 0.
 *   Those statements are chained per journal, oldest first, the way new
 *   imports without a closing balance are: each starts where the previous
 *   one closed (the first at 0) and closes at start plus its lines. Only
 *   statements whose two balances are both still 0 are touched, so a
 *   statement with a real closing balance is never rewritten and a re-run
 *   changes nothing.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('accounts_journals', 'statement_import_columns')) {
            Schema::table('accounts_journals', function (Blueprint $table) {
                $table->json('statement_import_columns')->nullable()->comment('Column mapping of the last statement import');
            });
        }

        $journalIds = DB::table('accounts_bank_statements')
            ->whereNotNull('journal_id')
            ->distinct()
            ->pluck('journal_id');

        foreach ($journalIds as $journalId) {
            $sums = DB::table('accounts_bank_statement_lines')
                ->whereIn('statement_id', DB::table('accounts_bank_statements')->where('journal_id', $journalId)->select('id'))
                ->groupBy('statement_id')
                ->selectRaw('statement_id, COALESCE(SUM(amount), 0) AS total')
                ->pluck('total', 'statement_id');

            $closing = 0.0;

            $statements = DB::table('accounts_bank_statements')
                ->where('journal_id', $journalId)
                ->orderBy('date')
                ->orderBy('id')
                ->get(['id', 'balance_start', 'balance_end_real']);

            foreach ($statements as $statement) {
                if ((float) $statement->balance_start == 0.0 && (float) $statement->balance_end_real == 0.0) {
                    $end = round($closing + (float) ($sums[$statement->id] ?? 0), 4);

                    DB::table('accounts_bank_statements')->where('id', $statement->id)->update([
                        'balance_start'    => $closing,
                        'balance_end'      => $end,
                        'balance_end_real' => $end,
                    ]);

                    $closing = $end;

                    continue;
                }

                $closing = (float) $statement->balance_end_real;
            }
        }
    }

    /**
     * Drops the mapping column. The backfilled balances stay: they were
     * computed from the statement lines, and the zeros they replaced carried
     * no information to restore.
     */
    public function down(): void
    {
        if (Schema::hasColumn('accounts_journals', 'statement_import_columns')) {
            Schema::table('accounts_journals', function (Blueprint $table) {
                $table->dropColumn('statement_import_columns');
            });
        }
    }
};
