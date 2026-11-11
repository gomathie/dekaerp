<?php

return [
    'empty' => 'Nothing to report for this period.',
    'range' => [
        'from'  => 'From',
        'until' => 'Until',
    ],
    'columns' => [
        'employee'         => 'Employee',
        'date'             => 'Date',
        'check_in'         => 'Clock in',
        'late_minutes'     => 'Late (minutes)',
        'worked_minutes'   => 'Worked (minutes)',
        'expected_minutes' => 'Scheduled (minutes)',
        'overtime_minutes' => 'Overtime (minutes)',
    ],
    'lateness' => [
        'title'      => 'Lateness',
        'subheading' => 'Clock-ins after the scheduled start, beyond the company tolerance.',
    ],
    'overtime' => [
        'title'      => 'Overtime',
        'subheading' => 'Time worked past the scheduled length, beyond the company tolerance.',
    ],
    'absence' => [
        'title'      => 'Absence',
        'subheading' => 'Scheduled working days with no attendance. Approved leave is not counted.',
    ],
];
