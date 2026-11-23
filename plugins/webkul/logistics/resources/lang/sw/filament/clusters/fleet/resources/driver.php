<?php

return [
    'navigation' => [
        'title' => 'Madereva',
    ],

    'form' => [
        'fields' => [
            'company'            => 'Kampuni',
            'employee'           => 'Mfanyakazi',
            'partner'            => 'Carrier mwasiliani',
            'name'               => 'Jina',
            'phone'              => 'Simu',
            'license-number'     => 'Licence nambari',
            'license-class'      => 'Licence class',
            'license-expires-at' => 'Licence expires',
            'is-active'          => 'Inatumika',
        ],
    ],

    'table' => [
        'columns' => [
            'name'               => 'Jina',
            'phone'              => 'Simu',
            'employee'           => 'Mfanyakazi',
            'partner'            => 'Carrier mwasiliani',
            'license-number'     => 'Licence nambari',
            'license-expires-at' => 'Licence expiry',
            'company'            => 'Kampuni',
            'is-active'          => 'Inatumika',
        ],

        'filters' => [
            'license-attention' => 'Expired au expiring hivi karibuni',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => 'Dereva',
            'license' => 'Licence',
        ],
    ],

    'actions' => [
        'add-drivers-from-employees' => [
            'label' => 'Ongeza madereva kutoka wafanyakazi',

            'fields' => [
                'employees' => 'Wafanyakazi',
            ],

            'notification' => [
                'title' => 'Mfanyakazi ingiza finished',
                'body'  => ':created imeundwa, :skipped skipped.',
            ],
        ],
    ],
];
