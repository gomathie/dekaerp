<?php

return [
    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Jumla',

                'fields' => [
                    'name' => 'Jina',
                    'type' => 'Aina',
                ],
            ],

            'options' => [
                'title'  => 'Options',

                'fields' => [
                    'name'        => 'Jina',
                    'color'       => 'Rangi',
                    'extra-price' => 'Extra Bei',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'        => 'Jina',
            'type'        => 'Aina',
            'deleted-at'  => 'Imefutwa',
            'created-at'  => 'Imeundwa',
            'updated-at'  => 'Imesasishwa',
        ],

        'groups' => [
            'type'       => 'Aina',
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'filters' => [
            'type' => 'Aina',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Attribute restored',
                    'body'  => 'attribute ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Attribute imefutwa',
                    'body'  => 'attribute ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Attribute force imefutwa',
                        'body'  => 'attribute ina force imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Attribute could si kuwa imefutwa',
                        'body'  => 'attribute haiwezekani kuwa imefutwa kwa sababu hii ni currently katika tumia.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Attributes restored',
                    'body'  => 'attributes ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Attributes imefutwa',
                    'body'  => 'attributes ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Attributes force imefutwa',
                        'body'  => 'attributes ina force imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Attributes could si kuwa imefutwa',
                        'body'  => 'attributes haiwezekani kuwa imefutwa kwa sababu wao ni currently katika tumia.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Jumla Taarifa',

                'entries' => [
                    'name' => 'Jina',
                    'type' => 'Aina',
                ],
            ],

            'record-information' => [
                'title' => 'Rekodi Taarifa',

                'entries' => [
                    'creator'    => 'Imeundwa Na',
                    'created_at' => 'Imeundwa',
                    'updated_at' => 'Mwisho Imesasishwa',
                ],
            ],
        ],
    ],
];
