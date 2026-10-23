<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Provider-agnostic telematics connection settings.
     *
     * Replaces the PILOT-specific columns of the superseded
     * add_pilot_telematics_* migration, which never completed on any
     * environment because an earlier migration in the same batch failed. The
     * drop below is for environments that did run it.
     */
    public function up(): void
    {
        $legacy = collect(['pilot_enabled', 'pilot_server', 'pilot_username', 'pilot_password', 'pilot_node'])
            ->filter(fn (string $column): bool => Schema::hasColumn('logistics_company_settings', $column))
            ->values()
            ->all();

        if ($legacy !== []) {
            Schema::table('logistics_company_settings', function (Blueprint $table) use ($legacy) {
                $table->dropColumn($legacy);
            });
        }

        Schema::table('logistics_company_settings', function (Blueprint $table) {
            $table->string('telematics_provider')->nullable();
            $table->string('telematics_server')->nullable();
            $table->text('telematics_username')->nullable();
            $table->text('telematics_password')->nullable();
            $table->json('telematics_options')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('logistics_company_settings', function (Blueprint $table) {
            $table->dropColumn([
                'telematics_provider',
                'telematics_server',
                'telematics_username',
                'telematics_password',
                'telematics_options',
            ]);
        });
    }
};
