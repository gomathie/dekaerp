<?php

namespace Webkul\Logistics\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Webkul\Logistics\Exceptions\UninstallBlockedException;

/**
 * Runs before uninstall drops the tables (UninstallCommand::startWith, which both
 * the console command and the Plugins page call first). Throwing here stops the
 * uninstall before anything is dropped.
 */
class UninstallGuard
{
    public static function ensureSafe(): void
    {
        if (config('logistics.allow_uninstall_with_data')) {
            return;
        }

        if (! Schema::hasTable('logistics_shipments')) {
            return;
        }

        // Counted with the query builder so no company scope or soft-delete filter
        // hides rows from other companies or in the trash.
        $count = DB::table('logistics_shipments')->count();

        if ($count > 0) {
            throw UninstallBlockedException::shipmentsExist($count);
        }
    }
}
