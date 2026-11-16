<?php

namespace Webkul\Attendance\Console\Commands;

use Illuminate\Console\Command;
use Webkul\Attendance\Services\AttendanceService;

class AutoCheckOutAttendances extends Command
{
    protected $signature = 'attendances:auto-check-out';

    protected $description = 'Close attendance rows left open longer than the company limit';

    public function handle(AttendanceService $service): int
    {
        $closed = $service->autoCheckOut();

        $this->info("Closed {$closed} attendance row(s).");

        return self::SUCCESS;
    }
}
