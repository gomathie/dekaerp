<?php

return [
    'actions' => [
        'clock-in'  => 'Clock in',
        'clock-out' => 'Clock out',
        'correct'   => 'Correct attendance',
        'export'    => 'Export attendance',
    ],
    'capture' => [
        'off'      => 'Off',
        'optional' => 'Optional',
        'required' => 'Required',
    ],
    'exceptions' => [
        'not-enabled' => 'Attendance is not enabled for company :company.',
    ],
    'notifications' => [
        'failed' => 'That did not work. The change was not saved.',
    ],
    'statuses' => [
        'clocked-in'     => 'Clocked in since :time',
        'not-clocked-in' => 'Not clocked in',
        'on-leave'       => 'On leave',
    ],
    'validation' => [
        'check-out-order'     => 'Check-out has to be after check-in.',
        'location-required'   => 'A location is required to clock in or out.',
        'no-employee'         => 'You do not have an employee record in this company.',
        'not-allowed'         => 'You can only correct attendance for your own team.',
        'not-clocked-in'      => 'You are not clocked in.',
        'not-your-attendance' => 'You can only clock yourself in or out.',
        'reason-required'     => 'A correction needs a reason.',
    ],
];
