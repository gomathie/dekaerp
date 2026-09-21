<?php

return [
    'navigation' => [
        'title' => 'Drivers',
    ],

    'form' => [
        'fields' => [
            'company'            => 'Company',
            'employee'           => 'Employee',
            'partner'            => 'Carrier contact',
            'name'               => 'Name',
            'phone'              => 'Phone',
            'license-number'     => 'Licence number',
            'license-class'      => 'Licence class',
            'license-expires-at' => 'Licence expires',
            'is-active'          => 'Active',
        ],
    ],

    'table' => [
        'columns' => [
            'name'               => 'Name',
            'phone'              => 'Phone',
            'employee'           => 'Employee',
            'partner'            => 'Carrier contact',
            'license-number'     => 'Licence number',
            'license-expires-at' => 'Licence expiry',
            'company'            => 'Company',
            'is-active'          => 'Active',
        ],

        'filters' => [
            'license-attention' => 'Expired or expiring soon',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => 'Driver',
            'license' => 'Licence',
        ],
    ],

    'actions' => [
        'add-drivers-from-employees' => [
            'label' => 'Add drivers from employees',

            'fields' => [
                'employees' => 'Employees',
            ],

            'notification' => [
                'title' => 'Employee import finished',
                'body'  => ':created created, :skipped skipped.',
            ],
        ],
    ],
];
