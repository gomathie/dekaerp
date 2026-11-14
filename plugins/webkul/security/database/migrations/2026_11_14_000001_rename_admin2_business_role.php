<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Give the company administrators' business role a readable name (user,
 * 2026-11-14): production's "Admin2" becomes "Business Administrator".
 *
 * The role is found through the setting that points at it, so nothing that
 * stores its id changes. Only the exact old name is renamed: a role someone
 * already named deliberately is left alone, and so is a database where the
 * new name is taken.
 */
return new class extends Migration
{
    private const OLD = 'Admin2';

    private const NEW = 'Business Administrator';

    public function up(): void
    {
        $roleId = $this->businessRoleId();

        if ($roleId === null || DB::table('roles')->where('name', self::NEW)->exists()) {
            return;
        }

        DB::table('roles')->where('id', $roleId)->where('name', self::OLD)->update(['name' => self::NEW, 'updated_at' => now()]);
    }

    public function down(): void
    {
        $roleId = $this->businessRoleId();

        if ($roleId !== null) {
            DB::table('roles')->where('id', $roleId)->where('name', self::NEW)->update(['name' => self::OLD, 'updated_at' => now()]);
        }
    }

    private function businessRoleId(): ?int
    {
        $payload = DB::table('settings')
            ->where('group', 'general')
            ->where('name', 'company_admin_business_role_id')
            ->value('payload');

        $id = $payload !== null ? json_decode($payload, true) : null;

        return is_int($id) && $id > 0 ? $id : null;
    }
};
