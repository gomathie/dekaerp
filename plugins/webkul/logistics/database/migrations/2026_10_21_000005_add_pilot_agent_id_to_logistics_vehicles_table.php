<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('logistics_vehicles', function (Blueprint $table) {
            $table->string('pilot_agent_id')->nullable()->after('telematics_device_ref');
        });
    }

    public function down(): void
    {
        Schema::table('logistics_vehicles', function (Blueprint $table) {
            $table->dropColumn('pilot_agent_id');
        });
    }
};
