<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The currencies a company may transact in, besides its base currency
     * (comprehensive review CR-6, 2026-11-13).
     *
     * Deliberately not backfilled. No rows means "every active currency", the
     * behaviour every company has today; a backfill of the base currency alone
     * would have stopped companies that already invoice in USD or EUR. The
     * list only narrows the pickers once installation staff fill it in.
     */
    public function up(): void
    {
        Schema::create('company_currencies', function (Blueprint $table): void {
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('currency_id')->constrained('currencies')->cascadeOnDelete();
            $table->primary(['company_id', 'currency_id']);
            $table->index('currency_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_currencies');
    }
};
