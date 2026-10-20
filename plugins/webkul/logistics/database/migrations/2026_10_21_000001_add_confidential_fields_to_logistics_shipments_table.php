<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('logistics_shipments', function (Blueprint $table) {
            $table->boolean('is_confidential')->default(false);
            $table->text('confidential_description')->nullable();

            $table->foreignId('consignor_partner_id')
                ->nullable()
                ->constrained('partners_partners')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('logistics_shipments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('consignor_partner_id');
            $table->dropColumn(['is_confidential', 'confidential_description']);
        });
    }
};
