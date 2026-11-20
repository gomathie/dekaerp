<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logistics_stops', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('sequence')->default(1);
            $table->string('type');
            $table->string('state')->default('pending');
            $table->string('contact_name')->nullable();
            $table->string('contact_phone')->nullable();
            $table->text('instructions')->nullable();
            $table->timestamp('planned_arrival_at')->nullable();
            $table->timestamp('actual_arrival_at')->nullable();
            $table->timestamp('planned_departure_at')->nullable();
            $table->timestamp('actual_departure_at')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->foreignId('shipment_id')
                ->constrained('logistics_shipments')
                ->cascadeOnDelete();

            $table->foreignId('trip_id')
                ->nullable()
                ->constrained('logistics_trips')
                ->nullOnDelete();

            $table->foreignId('address_id')
                ->nullable()
                ->constrained('partners_partners')
                ->nullOnDelete();

            $table->foreignId('company_id')
                ->constrained('companies')
                ->restrictOnDelete();

            $table->timestamps();

            $table->index(['shipment_id', 'sequence']);
            $table->index('trip_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logistics_stops');
    }
};
