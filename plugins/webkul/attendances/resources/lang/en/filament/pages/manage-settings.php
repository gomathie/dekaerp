<?php

return [
    'navigation' => [
        'title' => 'Attendance settings',
    ],
    'title'      => 'Attendance settings',
    'subheading' => 'Attendance for :company. Switch it on here, then employees can clock in from the dashboard.',
    'no-company' => 'Choose a company to manage its attendance settings.',
    'sections'   => [
        'status' => 'Status',
        'rules'  => 'Rules',
    ],
    'status' => [
        'enabled'  => 'On for this company',
        'disabled' => 'Off for this company',
    ],
    'fields' => [
        'auto-checkout'         => 'Automatic check-out after',
        'auto-checkout-help'    => 'A forgotten check-out is closed after this many hours and flagged for review.',
        'capture-location'      => 'Location at clock-in',
        'capture-location-help' => 'Off: nothing is recorded. Optional: recorded when the device shares it. Required: clock-in needs it.',
        'late-tolerance'        => 'Late after',
        'overtime-tolerance'    => 'Overtime after',
    ],
    'units' => [
        'hours'   => 'hours',
        'minutes' => 'minutes',
    ],
    'actions' => [
        'save'   => 'Save',
        'enable' => [
            'label'        => 'Switch on',
            'heading'      => 'Switch attendance on?',
            'description'  => 'Employees of this company with an employee record can clock in and out from the dashboard. Managers see their team.',
            'notification' => 'Attendance is on for this company.',
        ],
        'disable' => [
            'label'        => 'Switch off',
            'heading'      => 'Switch attendance off?',
            'description'  => 'Clocking in stops and the attendance screens are hidden. Existing records are kept.',
            'notification' => 'Attendance is off for this company.',
        ],
    ],
    'notifications' => [
        'saved' => 'Attendance settings saved.',
    ],
];
