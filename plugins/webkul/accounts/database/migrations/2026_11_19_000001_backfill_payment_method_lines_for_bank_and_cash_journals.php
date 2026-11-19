<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Companies whose accounting was set up after installation got their bank and
 * cash journals copied without payment method lines, so no invoice could be
 * marked paid there (TRACE365, 2026-11-19). Give every bank or cash journal
 * that has none the standard "Manual Payment" in and out, as the installer
 * gives the first company. Journals that already have lines are untouched.
 */
return new class extends Migration
{
    public function up(): void
    {
        $methods = DB::table('accounts_payment_methods')
            ->where('code', 'manual')
            ->whereIn('payment_type', ['inbound', 'outbound'])
            ->orderBy('id')
            ->get()
            ->unique('payment_type');

        if ($methods->isEmpty()) {
            return;
        }

        $journals = DB::table('accounts_journals')
            ->whereIn('type', ['bank', 'cash'])
            ->whereNotExists(function ($query): void {
                $query->select(DB::raw(1))
                    ->from('accounts_payment_method_lines')
                    ->whereColumn('accounts_payment_method_lines.journal_id', 'accounts_journals.id');
            })
            ->pluck('id');

        $now = now();

        foreach ($journals as $journalId) {
            foreach ($methods->values() as $index => $method) {
                DB::table('accounts_payment_method_lines')->insert([
                    'sort'               => $index + 1,
                    'payment_method_id'  => $method->id,
                    'payment_account_id' => null,
                    'journal_id'         => $journalId,
                    'creator_id'         => null,
                    'name'               => $method->name,
                    'created_at'         => $now,
                    'updated_at'         => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        // Lines may already be used by payments; removing them would break
        // those records. Nothing to undo safely.
    }
};
