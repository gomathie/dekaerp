<?php

return [
    'actions' => [
        'clock-in'  => 'Clock katika',
        'clock-out' => 'Clock nje',
        'correct'   => 'Correct mahudhurio',
        'export'    => 'Hamisha mahudhurio',
    ],
    'capture' => [
        'off'      => 'Zima',
        'optional' => 'Si lazima',
        'required' => 'Inahitajika',
    ],
    'exceptions' => [
        'not-enabled' => 'Mahudhurio ni si imewashwa kwa kampuni :company.',
    ],
    'notifications' => [
        'failed' => 'Hiyo si work. badilisha ilikuwa si saved.',
    ],
    'statuses' => [
        'clocked-in'     => 'Clocked katika since :time',
        'not-clocked-in' => 'Si clocked katika',
        'on-leave'       => 'Washa likizo',
    ],
    'validation' => [
        'check-out-order'     => 'Hundi-nje ina hadi kuwa baada ya hundi-katika.',
        'location-required'   => 'mahali ni inahitajika hadi clock katika au nje.',
        'no-employee'         => 'Wewe si ina mfanyakazi rekodi katika hii kampuni.',
        'not-allowed'         => 'Wewe unaweza tu correct mahudhurio kwa yako yake timu.',
        'not-clocked-in'      => 'Wewe ni si clocked katika.',
        'not-your-attendance' => 'Wewe unaweza tu clock yourself katika au nje.',
        'reason-required'     => 'correction needs sababu.',
    ],
];
