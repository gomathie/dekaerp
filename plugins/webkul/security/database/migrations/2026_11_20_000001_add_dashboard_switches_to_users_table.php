<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Two dashboard switches per user (user, 2026-11-19/20): "See dashboard"
 * and "See money figures". Both default to true, so every existing user
 * keeps exactly what they see today until an administrator turns one off.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'can_see_dashboard')) {
                $table->boolean('can_see_dashboard')->default(true);
            }

            if (! Schema::hasColumn('users', 'can_see_money_figures')) {
                $table->boolean('can_see_money_figures')->default(true);
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['can_see_dashboard', 'can_see_money_figures']);
        });
    }
};
