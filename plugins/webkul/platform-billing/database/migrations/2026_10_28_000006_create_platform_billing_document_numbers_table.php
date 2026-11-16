<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One row per document type per year: the sequence platform documents draw
     * their numbers from (D-SAAS-05).
     *
     * A counter table rather than a `sequences` row: platform numbering must be
     * gap-free and strictly ordered across the whole installation, while the
     * tenant `sequences` table is company-scoped and its numbers are cosmetic.
     * Keeping them apart also means a company resetting its own sequence can
     * never disturb an invoice number DEKA has already issued.
     *
     * `unique(document_type, year)` is the invariant, not a convention: two rows
     * for the same pair would allow the same number to be issued twice, and the
     * generator relies on the unique key to make a concurrent insert fail loudly
     * instead of silently forking the sequence.
     */
    public function up(): void
    {
        Schema::create('platform_billing_document_numbers', function (Blueprint $table): void {
            $table->id();

            $table->string('document_type', 32);
            $table->unsignedSmallInteger('year');

            $table->unsignedBigInteger('last_number')->default(0);

            $table->timestamps();

            $table->unique(['document_type', 'year'], 'platform_billing_document_numbers_type_year_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_billing_document_numbers');
    }
};
