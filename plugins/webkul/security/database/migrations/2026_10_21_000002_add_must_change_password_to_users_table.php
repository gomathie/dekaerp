<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lets an administrator who sets someone's password by hand require that
     * person to replace it on first sign-in, so a password shared over chat or
     * read out on a call does not stay valid indefinitely.
     *
     * Enforced by RequirePasswordChange on the panel's auth middleware, and
     * cleared automatically by the User model when the password changes.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('must_change_password')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('must_change_password');
        });
    }
};
