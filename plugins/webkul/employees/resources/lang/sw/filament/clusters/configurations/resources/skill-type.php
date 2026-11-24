<?php

return [
    'title' => 'Aina za ujuzi',

    'navigation' => [
        'title' => 'Aina za ujuzi',
        'group' => 'Mfanyakazi',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'name'             => 'Jina',
                'name-placeholder' => 'Weka jina la aina ya ujuzi',
                'color'            => 'Rangi',
                'status'           => 'Hali',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'ID',
            'name'       => 'Aina ya ujuzi',
            'status'     => 'Hali',
            'color'      => 'Rangi',
            'skills'     => 'Ujuzi',
            'levels'     => 'Viwango',
            'created-by' => 'Imeundwa na',
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'filters' => [
            'skill-levels' => 'Viwango vya ujuzi',
            'skills'       => 'Ujuzi',
            'created-by'   => 'Imeundwa na',
            'status'       => 'Hali',
            'updated-at'   => 'Imesasishwa',
            'created-at'   => 'Imeundwa',
        ],

        'groups' => [
            'name'       => 'Aina ya ujuzi',
            'color'      => 'Rangi',
            'status'     => 'Hali',
            'created-by' => 'Imeundwa na',
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Aina ya ujuzi imerejeshwa',
                    'body'  => 'Aina ya ujuzi imerejeshwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Aina ya ujuzi imefutwa',
                    'body'  => 'Aina ya ujuzi imefutwa kikamilifu.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Aina za ujuzi zimerejeshwa',
                    'body'  => 'Aina za ujuzi zimerejeshwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Aina za ujuzi zimefutwa',
                    'body'  => 'Aina za ujuzi zimefutwa kikamilifu.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Aina za ujuzi zimefutwa kabisa',
                    'body'  => 'Aina za ujuzi zimefutwa kabisa.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Aina ya ujuzi imeundwa',
                    'body'  => 'Aina ya ujuzi imeundwa kwa mafanikio.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'name'   => 'Aina ya ujuzi',
                'color'  => 'Rangi',
                'status' => 'Hali',
            ],
        ],
    ],
];
