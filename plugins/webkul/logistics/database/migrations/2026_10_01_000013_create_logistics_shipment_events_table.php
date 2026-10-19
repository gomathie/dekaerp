<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Append-only tracking timeline. Telematics adapters write here too
        // (source = telematics), so it stays typed and queryable.
        Schema::create('logistics_shipment_events', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('source')->default('user');
            $table->timestamp('occurred_at');
            $table->string('location_label')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();

            $table->foreignId('shipment_id')
                ->constrained('logistics_shipments')
                ->cascadeOnDelete();

            $table->foreignId('trip_id')
                ->nullable()
                ->constrained('logistics_trips')
                ->nullOnDelete();

            $table->foreignId('stop_id')
                ->nullable()
                ->constrained('logistics_stops')
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('company_id')
                ->constrained('companies')
                ->restrictOnDelete();

            $table->timestamps();

            $table->index(['shipment_id', 'occurred_at']);
            $table->index(['company_id', 'type', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logistics_shipment_events');
    }
};
