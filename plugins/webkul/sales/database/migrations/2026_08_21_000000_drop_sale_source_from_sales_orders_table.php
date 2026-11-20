<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Drops the column added for the removed Direct Sale feature. Guarded
     * because the migration that created it has been deleted, so a fresh
     * install never has the column to begin with, while an environment that
     * ran the old migration still needs it removed.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('sales_orders', 'sale_source')) {
            return;
        }

        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropColumn('sale_source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('sales_orders', 'sale_source')) {
            return;
        }

        Schema::table('sales_orders', function (Blueprint $table) {
            $table->string('sale_source')->nullable()->index()->after('reference');
        });
    }
};
