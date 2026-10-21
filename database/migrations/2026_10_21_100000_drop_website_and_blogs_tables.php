<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Removes the Website and Blogs plugins' storage. Their code was deleted on
     * 2026-10-21 (user decision: the product does not offer a public website or
     * a blog).
     *
     * Lives at app level rather than in either plugin because the plugins that
     * owned these tables no longer exist to carry a migration.
     *
     * Dropped children first: blogs_post_tags references blogs_tags and
     * blogs_posts, and blogs_posts references blogs_categories, so PostgreSQL
     * would refuse any other order without CASCADE.
     *
     * The plugins rows go too. Without that the Plugins page lists two plugins
     * whose packages cannot be resolved, and the uninstall action that would
     * normally have dropped these tables is gone with the code.
     */
    public function up(): void
    {
        foreach (['blogs_post_tags', 'blogs_posts', 'blogs_categories', 'blogs_tags', 'website_pages'] as $table) {
            Schema::dropIfExists($table);
        }

        // DB rather than the Eloquent model on purpose: a migration must not
        // depend on application classes, and these plugins' classes are gone.
        if (Schema::hasTable('plugins')) {
            DB::table('plugins')->whereIn('name', ['website', 'blogs'])->delete();
        }
    }

    /**
     * Deliberately irreversible.
     *
     * Recreating five empty tables would report success while every blog post
     * and page stayed gone, which is worse than refusing: it would hide the
     * fact that recovery has to come from a database backup. Restore from one
     * taken before this migration ran.
     */
    public function down(): void
    {
        throw new RuntimeException(
            'Dropping the Website and Blogs tables cannot be undone. Restore from a database backup taken before this migration ran.'
        );
    }
};
