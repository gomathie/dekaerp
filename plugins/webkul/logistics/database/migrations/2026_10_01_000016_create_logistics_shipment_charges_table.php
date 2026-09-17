<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logistics_shipment_charges', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('sort')->default(0);
            $table->string('description');
            $table->decimal('quantity', 15, 4)->default(1);
            $table->decimal('price_unit', 15, 4)->default(0);
            $table->decimal('discount', 8, 4)->default(0);
            $table->decimal('subtotal', 15, 4)->default(0);
            $table->decimal('total', 15, 4)->default(0);
            $table->boolean('is_billable')->default(true);

            $table->foreignId('shipment_id')
                ->constrained('logistics_shipments')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products_products')
                ->nullOnDelete();

            $table->foreignId('uom_id')
                ->nullable()
                ->constrained('unit_of_measures')
                ->nullOnDelete();

            $table->foreignId('currency_id')
                ->nullable()
                ->constrained('currencies')
                ->nullOnDelete();

            // Set once the charge is on an invoice; guards against double invoicing.
            $table->foreignId('move_line_id')
                ->nullable()
                ->constrained('accounts_account_move_lines')
                ->nullOnDelete();

            $table->foreignId('company_id')
                ->constrained('companies')
                ->restrictOnDelete();

            $table->foreignId('creator_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['shipment_id', 'sort']);
            $table->index(['company_id', 'is_billable', 'move_line_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logistics_shipment_charges');
    }
};
