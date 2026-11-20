<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logistics_trips', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('state')->default('planned');
            $table->timestamp('planned_start_at')->nullable();
            $table->timestamp('actual_start_at')->nullable();
            $table->timestamp('planned_end_at')->nullable();
            $table->timestamp('actual_end_at')->nullable();
            $table->decimal('odometer_start', 12, 1)->nullable();
            $table->decimal('odometer_end', 12, 1)->nullable();
            $table->text('notes')->nullable();

            $table->foreignId('vehicle_id')
                ->nullable()
                ->constrained('logistics_vehicles')
                ->nullOnDelete();

            $table->foreignId('driver_id')
                ->nullable()
                ->constrained('logistics_drivers')
                ->nullOnDelete();

            $table->foreignId('dispatcher_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('company_id')
                ->constrained('companies')
                ->restrictOnDelete();

            $table->foreignId('creator_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();

            $table->unique(['company_id', 'name']);
            $table->index(['company_id', 'state']);
            $table->index(['company_id', 'planned_start_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logistics_trips');
    }
};
