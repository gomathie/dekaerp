<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Per-company plugin availability.
     *
     * Installing a plugin is global - the `plugins` table has no company column
     * - so until now enabling one for a single customer enabled it for every
     * customer. Asked for by the user on 2026-10-21: plugin availability should
     * follow each company's subscription.
     *
     * Semantics are deliberately simple: a row means that company may use that
     * plugin, and no row means it may not. A boolean would add a third state
     * ("row saying false" versus "no row") that nothing needs.
     *
     * The backfill below is what makes this safe to deploy. Without it every
     * company would lose every plugin the moment this ran. With it, today's
     * behaviour is preserved exactly and access is taken away deliberately,
     * one company at a time, rather than granted from nothing.
     */
    public function up(): void
    {
        Schema::create('company_plugin', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();

            $table->foreignId('plugin_id')
                ->constrained('plugins')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['company_id', 'plugin_id']);
        });

        $this->grantEveryInstalledPluginToEveryCompany();
    }

    public function down(): void
    {
        Schema::dropIfExists('company_plugin');
    }

    /**
     * Preserve the current state of the world: every company keeps exactly the
     * plugins it can already use.
     */
    protected function grantEveryInstalledPluginToEveryCompany(): void
    {
        if (! Schema::hasTable('companies') || ! Schema::hasTable('plugins')) {
            return;
        }

        $companyIds = DB::table('companies')->pluck('id');
        $pluginIds = DB::table('plugins')->where('is_installed', true)->pluck('id');

        if ($companyIds->isEmpty() || $pluginIds->isEmpty()) {
            return;
        }

        $now = now();
        $rows = [];

        foreach ($companyIds as $companyId) {
            foreach ($pluginIds as $pluginId) {
                $rows[] = [
                    'company_id' => $companyId,
                    'plugin_id'  => $pluginId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('company_plugin')->insert($chunk);
        }
    }
};
