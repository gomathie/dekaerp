<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * An invoice whose creator asked for a linked sales order ("Create sales
     * order"), and the order made for it once the invoice was confirmed.
     *
     * Owned by Sales rather than a column on accounts_account_moves: Accounts
     * must not know about Sales, and uninstalling Sales drops this table.
     * order_id is null until the invoice is confirmed, and again after the
     * order is cancelled because the invoice was cancelled or reset.
     */
    public function up(): void
    {
        Schema::create('sales_invoice_order_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('move_id')->unique()->constrained('accounts_account_moves')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('sales_orders')->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('creator_id')->nullable()->constrained('users')->nullOnDelete();
            // "Customer has taken the goods": validate the delivery when the
            // order is made, or leave it for the warehouse.
            $table->boolean('deliver_now')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_invoice_order_links');
    }
};
