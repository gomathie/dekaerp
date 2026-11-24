<?php

return [
    'title' => 'Sikukuu za umma',

    'model-label' => 'Sikukuu ya umma',

    'navigation' => [
        'title' => 'Sikukuu za umma',
    ],

    'form' => [
        'fields' => [
            'name'             => 'Jina',
            'name-placeholder' => 'Weka jina la sikukuu ya umma',
            'date-from'        => 'Tarehe ya kuanza',
            'date-to'          => 'Tarehe ya kumalizika',
            'color'            => 'Rangi',
            'calendar'         => 'Kalenda',
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Jina',
            'company-name' => 'Kampuni',
            'calendar'     => 'Kalenda',
            'created-by'   => 'Imeundwa na',
            'date-from'    => 'Tarehe ya kuanza',
            'date-to'      => 'Tarehe ya kumalizika',
        ],

        'filters' => [
            'name'         => 'Jina',
            'company-name' => 'Kampuni',
            'created-by'   => 'Imeundwa na',
            'date-from'    => 'Tarehe ya kuanza',
            'date-to'      => 'Tarehe ya kumalizika',
            'created-at'   => 'Iliundwa',
            'updated-at'   => 'Ilisasishwa',
        ],

        'groups' => [
            'name'         => 'Jina',
            'company-name' => 'Kampuni',
            'created-by'   => 'Imeundwa na',
            'date-from'    => 'Tarehe ya kuanza',
            'date-to'      => 'Tarehe ya kumalizika',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Sikukuu ya umma imesasishwa',
                    'body'  => 'Sikukuu ya umma imesasishwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Sikukuu ya umma imefutwa',
                    'body'  => 'Sikukuu ya umma imefutwa kwa mafanikio.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Sikukuu za umma zimefutwa',
                    'body'  => 'Sikukuu za umma zimefutwa kwa mafanikio.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'      => 'Jina',
            'date-from' => 'Tarehe ya kuanza',
            'date-to'   => 'Tarehe ya kumalizika',
            'color'     => 'Rangi',
        ],
    ],
];
