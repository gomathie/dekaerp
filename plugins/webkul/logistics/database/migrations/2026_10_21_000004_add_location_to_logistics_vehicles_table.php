<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('logistics_vehicles', function (Blueprint $table) {
            $table->decimal('last_latitude', 10, 7)->nullable();
            $table->decimal('last_longitude', 10, 7)->nullable();
            $table->timestamp('last_located_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('logistics_vehicles', function (Blueprint $table) {
            $table->dropColumn(['last_latitude', 'last_longitude', 'last_located_at']);
        });
    }
};
