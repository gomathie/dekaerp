<?php

return [
    'navigation' => [
        'title' => 'Hatua za kazi',
    ],

    'form' => [
        'name'    => 'Jina',
        'project' => 'Mradi',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Jina',
            'project'    => 'Mradi',
            'created-at' => 'Tarehe ya kuundwa',
            'updated-at' => 'Tarehe ya kusasishwa',
        ],

        'groups' => [
            'project'    => 'Mradi',
            'created-at' => 'Tarehe ya kuundwa',
        ],

        'filters' => [
            'project' => 'Mradi',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Hatua ya kazi imesasishwa',
                    'body'  => 'Hatua ya kazi imesasishwa kwa mafanikio.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Hatua ya kazi imerejeshwa',
                    'body'  => 'Hatua ya kazi imerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Hatua ya kazi imefutwa',
                    'body'  => 'Hatua ya kazi imefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Hatua ya kazi imefutwa kabisa',
                        'body'  => 'Hatua ya kazi imefutwa kabisa.',
                    ],
                    'error' => [
                        'title' => 'Hatua ya kazi haikuweza kufutwa',
                        'body'  => 'Hatua ya kazi haiwezi kufutwa kwa sababu inatumika.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Hatua za kazi zimerejeshwa',
                    'body'  => 'Hatua za kazi zimerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Hatua za kazi zimefutwa',
                    'body'  => 'Hatua za kazi zimefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Hatua za kazi zimefutwa kabisa',
                    'body'  => 'Hatua za kazi zimefutwa kabisa.',
                ],
            ],
        ],
    ],
];
