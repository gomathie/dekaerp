<?php

return [
    'model' => [
        'label'  => 'Attendance record',
        'plural' => 'Attendance',
    ],
    'navigation' => [
        'title' => 'Attendance',
    ],
    'table' => [
        'columns' => [
            'employee'   => 'Employee',
            'check-in'   => 'Clock in',
            'check-out'  => 'Clock out',
            'worked'     => 'Worked',
            'review'     => 'Needs review',
            'on-leave'   => 'On leave',
            'source'     => 'Source in',
            'source-out' => 'Source out',
            'company'    => 'Company',
            'note'       => 'Note',
            'latitude'   => 'Latitude (in)',
            'longitude'  => 'Longitude (in)',
            'ip'         => 'IP address (in)',
        ],
        'filters' => [
            'open'         => 'Clocked in now',
            'needs-review' => 'Needs review',
            'employee'     => 'Employee',
            'day'          => 'Day',
            'on'           => 'On',
        ],
    ],
    'actions' => [
        'export'  => 'Export',
        'correct' => [
            'label'        => 'Correct attendance',
            'heading'      => 'Correct this attendance',
            'description'  => 'The change is recorded with your name and the reason you give.',
            'fields'       => [
                'check-in'    => 'Clock in',
                'check-out'   => 'Clock out',
                'note'        => 'Note',
                'reason'      => 'Reason',
                'reason-help' => 'Required. Say what was wrong and what you changed.',
            ],
            'notification' => 'Attendance corrected.',
        ],
    ],
    'export-complete' => ':count attendance row(s) exported.',
];
