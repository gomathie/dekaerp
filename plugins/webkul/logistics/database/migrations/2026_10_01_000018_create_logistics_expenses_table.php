<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logistics_expenses', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->decimal('amount', 15, 4);
            $table->string('paid_by')->default('company');
            $table->string('vendor_reference')->nullable();
            $table->text('description')->nullable();
            $table->string('receipt_path')->nullable();
            $table->string('state')->default('draft');
            $table->timestamp('approved_at')->nullable();

            $table->foreignId('category_id')
                ->constrained('logistics_expense_categories')
                ->restrictOnDelete();

            $table->foreignId('shipment_id')
                ->nullable()
                ->constrained('logistics_shipments')
                ->nullOnDelete();

            $table->foreignId('trip_id')
                ->nullable()
                ->constrained('logistics_trips')
                ->nullOnDelete();

            $table->foreignId('vehicle_id')
                ->nullable()
                ->constrained('logistics_vehicles')
                ->nullOnDelete();

            $table->foreignId('employee_id')
                ->nullable()
                ->constrained('employees_employees')
                ->nullOnDelete();

            $table->foreignId('payee_id')
                ->nullable()
                ->constrained('partners_partners')
                ->nullOnDelete();

            $table->foreignId('currency_id')
                ->nullable()
                ->constrained('currencies')
                ->nullOnDelete();

            $table->foreignId('approved_by_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('bill_move_id')
                ->nullable()
                ->constrained('accounts_account_moves')
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

            $table->index(['company_id', 'state']);
            $table->index('shipment_id');
            $table->index('trip_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logistics_expenses');
    }
};
