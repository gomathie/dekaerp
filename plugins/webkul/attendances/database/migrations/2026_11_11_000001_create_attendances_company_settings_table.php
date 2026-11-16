<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One row per company. Not a Spatie settings group: that repository
        // falls back to the default company's values, which would switch
        // attendance on for every company once the default company enabled it.
        Schema::create('attendances_company_settings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->unique()
                ->constrained('companies')
                ->cascadeOnDelete();

            $table->boolean('is_enabled')->default(false)->index();
            $table->timestamp('enabled_at')->nullable();
            $table->timestamp('disabled_at')->nullable();

            $table->foreignId('enabled_by_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->unsignedInteger('late_tolerance_minutes')->default(0);
            $table->unsignedInteger('overtime_tolerance_minutes')->default(0);
            $table->unsignedInteger('auto_checkout_after_hours')->default(12);
            $table->string('capture_location')->default('off');
            $table->boolean('kiosk_enabled')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances_company_settings');
    }
};
