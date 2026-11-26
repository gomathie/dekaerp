<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('sales_orders', 'outcome')) {
            return;
        }

        Schema::table('sales_orders', function (Blueprint $table): void {
            // Won or lost. Null until someone marks it. Not the order state.
            $table->string('outcome', 16)->nullable();
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('sales_orders', 'outcome')) {
            return;
        }

        Schema::table('sales_orders', function (Blueprint $table): void {
            $table->dropColumn('outcome');
        });
    }
};
