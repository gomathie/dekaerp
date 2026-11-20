<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logistics_shipments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('customer_reference')->nullable()->index();
            $table->string('transport_mode')->default('road');
            $table->string('priority')->default('normal');
            $table->string('state')->default('draft');
            $table->string('origin_label')->nullable();
            $table->string('destination_label')->nullable();
            $table->timestamp('planned_pickup_at')->nullable();
            $table->timestamp('actual_pickup_at')->nullable();
            $table->timestamp('expected_delivery_at')->nullable();
            $table->timestamp('actual_delivery_at')->nullable();
            $table->decimal('declared_value', 15, 4)->nullable();
            $table->boolean('is_fragile')->default(false);
            $table->boolean('is_hazardous')->default(false);
            $table->text('instructions')->nullable();
            $table->string('carrier_reference')->nullable();
            $table->string('waybill_no')->nullable()->index();
            $table->boolean('is_opening')->default(false);
            $table->unsignedInteger('total_packages')->default(0);
            $table->decimal('total_weight_kg', 15, 3)->default(0);
            $table->decimal('total_volume_m3', 15, 3)->default(0);
            $table->decimal('total_charges', 15, 4)->default(0);
            $table->decimal('total_costs', 15, 4)->default(0);

            // Sales is optional, so this is a plain indexed column, not a foreign key.
            $table->unsignedBigInteger('sale_order_id')->nullable()->index();

            $table->foreignId('customer_id')
                ->constrained('partners_partners')
                ->restrictOnDelete();

            $table->foreignId('pickup_address_id')
                ->nullable()
                ->constrained('partners_partners')
                ->nullOnDelete();

            $table->foreignId('delivery_address_id')
                ->nullable()
                ->constrained('partners_partners')
                ->nullOnDelete();

            $table->foreignId('carrier_id')
                ->nullable()
                ->constrained('partners_partners')
                ->nullOnDelete();

            $table->foreignId('service_type_id')
                ->nullable()
                ->constrained('logistics_service_types')
                ->nullOnDelete();

            $table->foreignId('currency_id')
                ->nullable()
                ->constrained('currencies')
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
            $table->index(['company_id', 'planned_pickup_at']);
            $table->index(['company_id', 'expected_delivery_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logistics_shipments');
    }
};
