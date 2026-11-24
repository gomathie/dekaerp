<?php

return [
    'actions' => [
        'clock-in'  => 'Ingia kazini',
        'clock-out' => 'Toka kazini',
        'correct'   => 'Sahihisha mahudhurio',
        'export'    => 'Hamisha mahudhurio',
    ],
    'capture' => [
        'off'      => 'Imezimwa',
        'optional' => 'Si lazima',
        'required' => 'Lazima',
    ],
    'exceptions' => [
        'not-enabled' => 'Mahudhurio hayajawashwa kwa kampuni :company.',
    ],
    'notifications' => [
        'failed' => 'Haikufanikiwa. Badiliko halijahifadhiwa.',
    ],
    'statuses' => [
        'clocked-in'     => 'Uko kazini tangu :time',
        'not-clocked-in' => 'Hujaingia kazini',
        'on-leave'       => 'Yuko likizoni',
    ],
    'validation' => [
        'check-out-order'     => 'Muda wa kutoka lazima uwe baada ya muda wa kuingia.',
        'location-required'   => 'Eneo linahitajika ili kuingia au kutoka kazini.',
        'no-employee'         => 'Huna rekodi ya mfanyakazi katika kampuni hii.',
        'not-allowed'         => 'Unaweza kusahihisha mahudhurio ya timu yako tu.',
        'not-clocked-in'      => 'Hujaingia kazini.',
        'not-your-attendance' => 'Unaweza kujiandikisha kuingia au kutoka wewe mwenyewe tu.',
        'reason-required'     => 'Usahihishaji unahitaji sababu.',
    ],
];
