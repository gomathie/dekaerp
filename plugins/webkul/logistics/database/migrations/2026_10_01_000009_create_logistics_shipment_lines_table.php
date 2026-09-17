<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logistics_shipment_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('sort')->default(0);
            $table->string('description');
            $table->decimal('quantity', 15, 3)->default(1);
            $table->decimal('weight_kg', 15, 3)->nullable();
            $table->decimal('length_cm', 10, 2)->nullable();
            $table->decimal('width_cm', 10, 2)->nullable();
            $table->decimal('height_cm', 10, 2)->nullable();
            $table->decimal('volume_m3', 15, 3)->nullable();
            $table->decimal('declared_value', 15, 4)->nullable();
            $table->text('handling_instructions')->nullable();

            $table->foreignId('shipment_id')
                ->constrained('logistics_shipments')
                ->cascadeOnDelete();

            $table->foreignId('package_type_id')
                ->nullable()
                ->constrained('logistics_package_types')
                ->nullOnDelete();

            $table->foreignId('company_id')
                ->constrained('companies')
                ->restrictOnDelete();

            $table->timestamps();

            $table->index(['shipment_id', 'sort']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logistics_shipment_lines');
    }
};
