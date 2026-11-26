<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * An invoice line's label holds the product name plus its full sales
     * description, as Zoho Books and the invoice form both write it. 255
     * characters was not enough: a packaged item with its list of contents
     * (HiTrace's Zoho history, 2026-11-25) failed to save, so importing it
     * would have meant cutting the customer's description short. Text keeps
     * it whole. Widening only: no existing value changes.
     */
    public function up(): void
    {
        Schema::table('accounts_account_move_lines', function (Blueprint $table) {
            $table->text('name')->nullable()->comment('Label')->change();
        });
    }

    /**
     * Back to 255 characters. Fails on purpose if a longer label was saved
     * since, rather than cutting it.
     */
    public function down(): void
    {
        Schema::table('accounts_account_move_lines', function (Blueprint $table) {
            $table->string('name')->nullable()->comment('Label')->change();
        });
    }
};
