<?php

return [
    'title'      => 'Accrual Plan',
    'navigation' => [
        'title' => 'Accrual Plan',
    ],

    'form' => [
        'fields' => [
            'name'                    => 'Kichwa',
            'is-based-on-worked-time' => 'Ni Based washa Worked Time',
            'accrued-gain-time'       => 'Accrued Gain Time',
            'carry-over-time'         => 'Carry Zaidi ya Time',
            'carry-over-date'         => 'Carry Zaidi ya Tarehe',
            'status'                  => 'Hali',
        ],
    ],

    'table' => [
        'columns' => [
            'name'   => 'Jina',
            'levels' => 'Levels',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Accrual Plan imefutwa',
                    'body'  => 'Accrual Plan ina imefutwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Accrual Plan imefutwa',
                    'body'  => 'Accrual Plan ina imefutwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'basic-information' => 'Basic Taarifa',
        ],

        'entries' => [
            'name'                    => 'Jina',
            'is-based-on-worked-time' => 'Ni Based washa Worked Time',
            'accrued-gain-time'       => 'Accrued Gain Time',
            'carry-over-time'         => 'Carry Zaidi ya Time',
            'carry-over-day'          => 'Carry Zaidi ya Siku',
            'carry-over-month'        => 'Carry Zaidi ya Mwezi',
        ],
    ],
];
