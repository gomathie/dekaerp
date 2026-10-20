<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The telematics platform's own identifier for the vehicle, which is not
     * the device IMEI held by telematics_device_ref. PILOT calls this an
     * "agent ID"; other platforms use their own term for the same record.
     */
    public function up(): void
    {
        Schema::table('logistics_vehicles', function (Blueprint $table) {
            $table->string('telematics_agent_id')->nullable()->after('telematics_device_ref');
        });
    }

    public function down(): void
    {
        Schema::table('logistics_vehicles', function (Blueprint $table) {
            $table->dropColumn('telematics_agent_id');
        });
    }
};
