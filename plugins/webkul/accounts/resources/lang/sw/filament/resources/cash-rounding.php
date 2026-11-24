<?php

return [
    'form' => [
        'fields' => [
            'name'               => 'Jina',
            'rounding-precision' => 'Usahihi wa ukadiriaji',
            'rounding-strategy'  => 'Mkakati wa ukadiriaji',
            'profit-account'     => 'Akaunti ya faida',
            'loss-account'       => 'Akaunti ya hasara',
            'rounding-method'    => 'Njia ya ukadiriaji',
        ],
    ],

    'table' => [
        'columns' => [
            'name'                 => 'Jina',
            'rounding-strategy'    => 'Mkakati wa ukadiriaji',
            'rounding-method'      => 'Njia ya ukadiriaji',
            'created-by'           => 'Imeundwa na',
            'profit-account'       => 'Akaunti ya faida',
            'loss-account'         => 'Akaunti ya hasara',
        ],

        'groups' => [
            'name'              => 'Jina',
            'rounding-strategy' => 'Mkakati wa ukadiriaji',
            'rounding-method'   => 'Njia ya ukadiriaji',
            'created-by'        => 'Imeundwa na',
            'profit-account'    => 'Akaunti ya faida',
            'loss-account'      => 'Akaunti ya hasara',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Ukadiriaji wa fedha taslimu umefutwa',
                    'body'  => 'Ukadiriaji wa fedha taslimu umefutwa kikamilifu.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Ukadiriaji wa fedha taslimu umefutwa',
                    'body'  => 'Ukadiriaji wa fedha taslimu umefutwa kikamilifu.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'               => 'Jina',
            'rounding-precision' => 'Usahihi wa ukadiriaji',
            'rounding-strategy'  => 'Mkakati wa ukadiriaji',
            'profit-account'     => 'Akaunti ya faida',
            'loss-account'       => 'Akaunti ya hasara',
            'rounding-method'    => 'Njia ya ukadiriaji',
        ],
    ],
];
