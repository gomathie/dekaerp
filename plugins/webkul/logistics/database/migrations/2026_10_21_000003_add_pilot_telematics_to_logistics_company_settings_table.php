<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('logistics_company_settings', function (Blueprint $table) {
            $table->boolean('pilot_enabled')->default(false);
            $table->string('pilot_server')->nullable();
            $table->text('pilot_username')->nullable();
            $table->text('pilot_password')->nullable();
            $table->unsignedInteger('pilot_node')->default(1);
        });
    }

    public function down(): void
    {
        Schema::table('logistics_company_settings', function (Blueprint $table) {
            $table->dropColumn([
                'pilot_enabled',
                'pilot_server',
                'pilot_username',
                'pilot_password',
                'pilot_node',
            ]);
        });
    }
};
