<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * The grant a Super Admin gives a Multi-Company Admin (SAAS-15,
     * 2026-11-10). Nobody holds it after this migration: Super Admins
     * impersonate without it, and everyone else needs it given explicitly.
     * Shield generates resource, page and widget permissions only, so this
     * one is created here.
     */
    public function up(): void
    {
        $exists = DB::table('permissions')
            ->where('name', 'tenant.impersonate')
            ->where('guard_name', 'web')
            ->exists();

        if (! $exists) {
            DB::table('permissions')->insert([
                'name'       => 'tenant.impersonate',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Removing the row also removes every grant of it, through the
     * permission pivots' cascading foreign keys.
     */
    public function down(): void
    {
        DB::table('permissions')
            ->where('name', 'tenant.impersonate')
            ->where('guard_name', 'web')
            ->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
