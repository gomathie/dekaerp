<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logistics_shipment_charge_taxes', function (Blueprint $table) {
            $table->foreignId('charge_id')
                ->constrained('logistics_shipment_charges')
                ->cascadeOnDelete();

            $table->foreignId('tax_id')
                ->constrained('accounts_taxes')
                ->cascadeOnDelete();

            $table->primary(['charge_id', 'tax_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logistics_shipment_charge_taxes');
    }
};
