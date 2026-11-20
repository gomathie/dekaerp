<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logistics_shipment_invoices', function (Blueprint $table) {
            $table->foreignId('shipment_id')
                ->constrained('logistics_shipments')
                ->cascadeOnDelete();

            $table->foreignId('move_id')
                ->constrained('accounts_account_moves')
                ->cascadeOnDelete();

            $table->primary(['shipment_id', 'move_id']);
            $table->index('move_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logistics_shipment_invoices');
    }
};
