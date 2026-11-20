<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logistics_vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('registration_no');
            $table->string('name')->nullable();
            $table->string('ownership')->default('owned');
            $table->decimal('capacity_kg', 12, 3)->nullable();
            $table->decimal('capacity_m3', 12, 3)->nullable();

            // Optional integrations, so no foreign keys: Maintenance may not be
            // installed, and the telematics reference points outside the database.
            $table->unsignedBigInteger('equipment_id')->nullable()->index();
            $table->string('telematics_device_ref')->nullable()->index();

            $table->boolean('is_active')->default(true);

            $table->foreignId('vehicle_type_id')
                ->nullable()
                ->constrained('logistics_vehicle_types')
                ->nullOnDelete();

            $table->foreignId('carrier_id')
                ->nullable()
                ->constrained('partners_partners')
                ->nullOnDelete();

            $table->foreignId('default_driver_id')
                ->nullable()
                ->constrained('logistics_drivers')
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

            $table->unique(['company_id', 'registration_no']);
            $table->index(['company_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logistics_vehicles');
    }
};
