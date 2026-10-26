<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Zambian kwacha shipped inactive in every installation, and `active` is what
 * every currency picker filters on, so ZMW could not be selected for a company
 * or a document. The seed data now marks it active for fresh installs; this
 * reaches installations seeded before that change.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('currencies')
            ->where('name', 'ZMW')
            ->where('active', false)
            ->update(['active' => true, 'updated_at' => now()]);
    }

    public function down(): void
    {
        // Deliberately empty: deactivating a currency on rollback would strand
        // companies and documents that were created against it after deploy.
    }
};
