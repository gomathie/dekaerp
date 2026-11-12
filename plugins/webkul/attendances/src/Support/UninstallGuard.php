<?php

namespace Webkul\Attendance\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Webkul\Attendance\Exceptions\UninstallBlockedException;

class UninstallGuard
{
    public static function ensureSafe(): void
    {
        if (config('attendances.allow_uninstall_with_data')) {
            return;
        }

        if (! Schema::hasTable('attendances_attendances')) {
            return;
        }

        $count = DB::table('attendances_attendances')->count();

        if ($count > 0) {
            throw UninstallBlockedException::rowsExist($count);
        }
    }
}
