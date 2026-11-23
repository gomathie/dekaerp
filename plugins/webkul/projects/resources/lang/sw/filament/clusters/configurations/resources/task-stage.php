<?php

return [
    'navigation' => [
        'title' => 'Kazi Hatua',
    ],

    'form' => [
        'name'    => 'Jina',
        'project' => 'Mradi',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Jina',
            'project'    => 'Mradi',
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'groups' => [
            'project'    => 'Mradi',
            'created-at' => 'Imeundwa',
        ],

        'filters' => [
            'project' => 'Mradi',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Kazi hatua imesasishwa',
                    'body'  => 'kazi hatua ina imesasishwa successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Kazi hatua restored',
                    'body'  => 'kazi hatua ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Kazi hatua imefutwa',
                    'body'  => 'kazi hatua ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Kazi hatua force imefutwa',
                        'body'  => 'Kazi hatua ina force imefutwa successfully.',
                    ],
                    'error' => [
                        'title' => 'Kazi hatua could si kuwa imefutwa',
                        'body'  => 'Kazi hatua haiwezekani kuwa imefutwa kwa sababu hii ni currently katika tumia.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Kazi hatua restored',
                    'body'  => 'kazi hatua ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Kazi hatua imefutwa',
                    'body'  => 'kazi hatua ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Kazi hatua force imefutwa',
                    'body'  => 'kazi hatua ina force imefutwa successfully.',
                ],
            ],
        ],
    ],
];
