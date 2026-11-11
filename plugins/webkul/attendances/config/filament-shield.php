<?php

use Webkul\Attendance\Filament\Clusters\Reporting;
use Webkul\Attendance\Filament\Resources\AttendanceResource;

/*
 * A cluster is a Page subclass, and Shield rejects a page that is some page's
 * cluster, so the reporting cluster is excluded and its pages carry their own
 * permissions (page_attendance_lateness, page_attendance_absence,
 * page_attendance_overtime).
 */

return [
    'resources' => [
        'manage' => [
            AttendanceResource::class => [
                'view_any',
                'view',
                'view_team',
                'correct',
                'export',
                'view_location',
            ],
        ],
        'exclude' => [],
    ],
    'pages' => [
        'exclude' => [
            Reporting::class,
        ],
    ],
];
