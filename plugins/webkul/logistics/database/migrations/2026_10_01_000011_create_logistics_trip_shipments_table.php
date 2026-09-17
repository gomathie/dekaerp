<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logistics_trip_shipments', function (Blueprint $table) {
            $table->foreignId('trip_id')
                ->constrained('logistics_trips')
                ->cascadeOnDelete();

            $table->foreignId('shipment_id')
                ->constrained('logistics_shipments')
                ->cascadeOnDelete();

            $table->unsignedInteger('leg_sequence')->default(1);

            $table->primary(['trip_id', 'shipment_id']);
            $table->index('shipment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logistics_trip_shipments');
    }
};
