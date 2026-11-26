<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One Zoho Books import started from the Super Admin page: who started
     * it, for which company, the mapping shown and approved, what was
     * recorded and how it compares with Zoho. It is the audit of every run
     * and what lets the page follow a queued import.
     *
     * The uploaded export is kept in the row, not as a file: the queue worker
     * may run in another container than the web request that received the
     * upload, and a container's disk does not outlive a deploy. It is
     * cleared when the run ends, whatever the outcome.
     */
    public function up(): void
    {
        Schema::create('sales_zoho_import_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            // "import" (the one-time initial load) or "compare" (a parallel-run
            // check that writes nothing).
            $table->string('mode', 20)->default('import');
            $table->string('status', 20);
            // When the Zoho export of a finished import was taken. From then
            // on new records are entered in both systems, so a later export
            // is never imported again (ZohoImporter::initialLoadDone()).
            $table->timestamp('cutoff_at')->nullable();
            $table->longText('export')->nullable();
            $table->json('mapping')->nullable();
            $table->json('counts')->nullable();
            $table->json('comparison')->nullable();
            $table->text('message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'mode', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_zoho_import_runs');
    }
};
