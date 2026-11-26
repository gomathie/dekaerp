<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Which DEKA record each imported Zoho Books record became (zoho:import).
     *
     * It is what makes the import safe to run again: a Zoho id already listed
     * here for the company is skipped, so a run that stopped half way picks up
     * where it left off and never records a document twice. It is also how the
     * importer tells its own documents from ones the company made itself.
     *
     * Its own table rather than the Zoho id in a reference field: documents,
     * contacts, products and taxes have no common field for it, and a
     * reference a user can edit must not decide whether money is booked twice.
     */
    public function up(): void
    {
        Schema::create('sales_zoho_import_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('zoho_type', 40);
            $table->string('zoho_id', 64);
            $table->string('record_type');
            $table->unsignedBigInteger('record_id');
            $table->timestamps();

            $table->unique(['company_id', 'zoho_type', 'zoho_id']);
            $table->index(['record_type', 'record_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_zoho_import_records');
    }
};
