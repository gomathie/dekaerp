<?php

return [
    'navigation' => [
        'title' => 'Madereva',
    ],

    'form' => [
        'fields' => [
            'company'            => 'Kampuni',
            'employee'           => 'Mfanyakazi',
            'partner'            => 'Mhusika wa msafirishaji',
            'name'               => 'Jina',
            'phone'              => 'Simu',
            'license-number'     => 'Namba ya leseni',
            'license-class'      => 'Daraja la leseni',
            'license-expires-at' => 'Leseni inaisha',
            'is-active'          => 'Hai',
        ],
    ],

    'table' => [
        'columns' => [
            'name'               => 'Jina',
            'phone'              => 'Simu',
            'employee'           => 'Mfanyakazi',
            'partner'            => 'Mhusika wa msafirishaji',
            'license-number'     => 'Namba ya leseni',
            'license-expires-at' => 'Kuisha kwa leseni',
            'company'            => 'Kampuni',
            'is-active'          => 'Hai',
        ],

        'filters' => [
            'license-attention' => 'Imeisha au inaisha karibuni',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => 'Dereva',
            'license' => 'Leseni',
        ],
    ],

    'actions' => [
        'add-drivers-from-employees' => [
            'label' => 'Ongeza madereva kutoka kwa wafanyakazi',

            'fields' => [
                'employees' => 'Wafanyakazi',
            ],

            'notification' => [
                'title' => 'Uingizaji wa wafanyakazi umekamilika',
                'body'  => 'Wameundwa :created, wamerukwa :skipped.',
            ],
        ],
    ],
];
