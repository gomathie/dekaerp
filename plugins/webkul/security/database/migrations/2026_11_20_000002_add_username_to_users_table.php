<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Sign in with email or username (user, 2026-11-19). A username is optional,
 * unique across the whole installation and not case-sensitive, so the index
 * is on LOWER(username): "Bennet" and "bennet" cannot both exist. Existing
 * users get none and keep signing in with their email.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('username', 50)->nullable();
            });
        }

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS users_username_lower_unique ON users (LOWER(username))');
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->unique('username', 'users_username_lower_unique');
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS users_username_lower_unique');
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique('users_username_lower_unique');
            });
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
        });
    }
};
