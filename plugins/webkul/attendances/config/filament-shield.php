<?php

use Webkul\Attendance\Filament\Resources\AttendanceResource;

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
        'exclude' => [],
    ],
];
