<?php

return [
    'navigation' => [
        'title' => 'Mipango ya shughuli',
    ],

    'form' => [
        'name'   => 'Jina',
        'status' => 'Hali',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Jina',
            'status'     => 'Hali',
            'created-at' => 'Tarehe ya kuundwa',
            'updated-at' => 'Tarehe ya kusasishwa',
        ],

        'groups' => [
            'name'       => 'Jina',
            'status'     => 'Hali',
            'created-at' => 'Tarehe ya kuundwa',
            'updated-at' => 'Tarehe ya kusasishwa',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Mpango wa shughuli umerejeshwa',
                    'body'  => 'Mpango wa shughuli umerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Mpango wa shughuli umefutwa',
                    'body'  => 'Mpango wa shughuli umefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Mpango wa shughuli umefutwa kabisa',
                    'body'  => 'Mpango wa shughuli umefutwa kabisa.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Mipango ya shughuli imerejeshwa',
                    'body'  => 'Mipango ya shughuli imerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Mipango ya shughuli imefutwa',
                    'body'  => 'Mipango ya shughuli imefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Mipango ya shughuli imefutwa kabisa',
                    'body'  => 'Mipango ya shughuli imefutwa kabisa.',
                ],
            ],
        ],

        'empty-state' => [
            'create' => [
                'notification' => [
                    'title' => 'Mpango wa shughuli umeundwa',
                    'body'  => 'Mpango wa shughuli umeundwa kwa mafanikio.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name'   => 'Jina',
        'status' => 'Hali',
    ],
];
