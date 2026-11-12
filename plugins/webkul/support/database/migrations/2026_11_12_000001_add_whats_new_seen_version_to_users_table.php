<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The newest release each user has opened on What's New, for the top-bar
 * "new" dot (D-WN-2: per user, so it follows them across devices). Nullable
 * with no default: existing users see the dot once, then it clears.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('users', 'whats_new_seen_version')) {
            return;
        }

        Schema::table('users', function (Blueprint $table): void {
            $table->string('whats_new_seen_version', 32)->nullable();
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'whats_new_seen_version')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropColumn('whats_new_seen_version');
            });
        }
    }
};
