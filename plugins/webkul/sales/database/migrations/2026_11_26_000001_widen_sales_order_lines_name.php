<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A quotation line's description holds the product name plus its full
     * sales description. 255 characters was not enough for a packaged item
     * with its list of contents (HiTrace's Zoho history, 2026-11-25): saving
     * failed, and importing it would have cut the description short. The
     * invoice line it becomes is widened the same way (accounts migration
     * 2026_11_26_000001). Widening only: no existing value changes.
     */
    public function up(): void
    {
        Schema::table('sales_order_lines', function (Blueprint $table) {
            $table->text('name')->comment('Name')->change();
        });
    }

    /**
     * Back to 255 characters. Fails on purpose if a longer description was
     * saved since, rather than cutting it.
     */
    public function down(): void
    {
        Schema::table('sales_order_lines', function (Blueprint $table) {
            $table->string('name')->comment('Name')->change();
        });
    }
};
