<?php

return [
    'navigation' => [
        'title' => 'Mradi Hatua',
    ],

    'form' => [
        'name' => 'Jina',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Jina',
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'groups' => [
            'name'         => 'Jina',
            'is-completed' => 'Ni Imekamilika',
            'project'      => 'Mradi',
            'created-at'   => 'Imeundwa',
        ],

        'filters' => [
            'is-completed' => 'Ni Imekamilika',
            'project'      => 'Mradi',
            'creator'      => 'Creator',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Mradi hatua imesasishwa',
                    'body'  => 'mradi hatua ina imesasishwa successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Mradi hatua restored',
                    'body'  => 'mradi hatua ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Mradi hatua imefutwa',
                    'body'  => 'mradi hatua ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Mradi hatua force imefutwa',
                        'body'  => 'mradi hatua ina force imefutwa successfully.',
                    ],
                    'error' => [
                        'title' => 'Mradi Hatua could si kuwa imefutwa',
                        'body'  => 'Mradi Hatua haiwezekani kuwa imefutwa kwa sababu hii ni currently katika tumia.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Mradi hatua restored',
                    'body'  => 'mradi hatua ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Mradi hatua imefutwa',
                    'body'  => 'mradi hatua ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Mradi hatua force imefutwa',
                    'body'  => 'mradi hatua ina force imefutwa successfully.',
                ],
            ],
        ],
    ],
];
