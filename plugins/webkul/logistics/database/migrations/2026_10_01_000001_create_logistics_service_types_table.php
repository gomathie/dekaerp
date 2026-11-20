<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logistics_service_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('transport_mode')->default('road');

            // A product *reference* (e.g. LOG-FREIGHT), not a product id: service types
            // are shared across companies while service products belong to one company,
            // so the product is resolved per company at the point of use.
            $table->string('product_reference')->nullable();

            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);

            $table->foreignId('company_id')
                ->nullable()
                ->constrained('companies')
                ->cascadeOnDelete();

            $table->foreignId('creator_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();

            $table->unique(['company_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logistics_service_types');
    }
};
