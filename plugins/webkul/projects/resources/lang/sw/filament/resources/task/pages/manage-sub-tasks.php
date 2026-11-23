<?php

return [
    'title' => 'Sub Kazi',

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Ongeza Sub Kazi',

                'notification' => [
                    'title' => 'Kazi imeundwa',
                    'body'  => 'kazi ina imeundwa successfully.',
                ],
            ],
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Kazi restored',
                    'body'  => 'kazi ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Kazi imefutwa',
                    'body'  => 'kazi ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Kazi force imefutwa',
                    'body'  => 'kazi ina force imefutwa successfully.',
                ],
            ],
        ],
    ],
];
