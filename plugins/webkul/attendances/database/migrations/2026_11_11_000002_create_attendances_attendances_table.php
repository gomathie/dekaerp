<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances_attendances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();

            $table->foreignId('employee_id')
                ->constrained('employees_employees')
                ->cascadeOnDelete();

            $table->timestamp('check_in_at');
            $table->timestamp('check_out_at')->nullable();
            $table->unsignedInteger('worked_minutes')->nullable();

            $table->string('source_in');
            $table->string('source_out')->nullable();

            $table->decimal('latitude_in', 10, 7)->nullable();
            $table->decimal('longitude_in', 10, 7)->nullable();
            $table->decimal('accuracy_in', 8, 2)->nullable();
            $table->decimal('latitude_out', 10, 7)->nullable();
            $table->decimal('longitude_out', 10, 7)->nullable();
            $table->decimal('accuracy_out', 8, 2)->nullable();
            $table->string('ip_in', 45)->nullable();
            $table->string('ip_out', 45)->nullable();

            $table->text('note')->nullable();
            $table->boolean('is_corrected')->default(false);
            $table->boolean('needs_review')->default(false);
            $table->boolean('on_leave')->default(false);

            $table->foreignId('corrected_by_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('creator_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['company_id', 'check_in_at']);
            $table->index(['employee_id', 'check_in_at']);
        });

        if (DB::getDriverName() === 'pgsql') {
            // One open row per employee. The service also returns the existing
            // row on a double click; this is what stops a second insert when
            // two requests pass that check together.
            DB::statement('CREATE UNIQUE INDEX attendances_one_open_per_employee ON attendances_attendances (employee_id) WHERE check_out_at IS NULL');

            DB::statement('ALTER TABLE attendances_attendances ADD CONSTRAINT attendances_check_out_after_check_in CHECK (check_out_at IS NULL OR check_out_at > check_in_at)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances_attendances');
    }
};
