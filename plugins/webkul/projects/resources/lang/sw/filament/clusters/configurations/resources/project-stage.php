<?php

return [
    'navigation' => [
        'title' => 'Hatua za mradi',
    ],

    'form' => [
        'name' => 'Jina',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Jina',
            'created-at' => 'Tarehe ya kuundwa',
            'updated-at' => 'Tarehe ya kusasishwa',
        ],

        'groups' => [
            'name'         => 'Jina',
            'is-completed' => 'Imekamilika',
            'project'      => 'Mradi',
            'created-at'   => 'Tarehe ya kuundwa',
        ],

        'filters' => [
            'is-completed' => 'Imekamilika',
            'project'      => 'Mradi',
            'creator'      => 'Aliyeunda',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Hatua ya mradi imesasishwa',
                    'body'  => 'Hatua ya mradi imesasishwa kwa mafanikio.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Hatua ya mradi imerejeshwa',
                    'body'  => 'Hatua ya mradi imerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Hatua ya mradi imefutwa',
                    'body'  => 'Hatua ya mradi imefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Hatua ya mradi imefutwa kabisa',
                        'body'  => 'Hatua ya mradi imefutwa kabisa.',
                    ],
                    'error' => [
                        'title' => 'Hatua ya mradi haikuweza kufutwa',
                        'body'  => 'Hatua ya mradi haiwezi kufutwa kwa sababu inatumika.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Hatua za mradi zimerejeshwa',
                    'body'  => 'Hatua za mradi zimerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Hatua za mradi zimefutwa',
                    'body'  => 'Hatua za mradi zimefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Hatua za mradi zimefutwa kabisa',
                    'body'  => 'Hatua za mradi zimefutwa kabisa.',
                ],
            ],
        ],
    ],
];
