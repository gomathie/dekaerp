<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('logistics_shipment_lines', function (Blueprint $table) {
            $table->string('line_type')->default('external');

            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products_products')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('logistics_shipment_lines', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_id');
            $table->dropColumn('line_type');
        });
    }
};
