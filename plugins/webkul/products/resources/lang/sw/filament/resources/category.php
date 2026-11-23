<?php

return [
    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Jumla',

                'fields' => [
                    'name'             => 'Jina',
                    'name-placeholder' => 'eg. Lamps',
                    'parent'           => 'Parent',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'        => 'Jina',
            'full-name'   => 'Kamili Jina',
            'parent-path' => 'Parent Path',
            'parent'      => 'Parent',
            'creator'     => 'Creator',
            'created-at'  => 'Imeundwa',
            'created-at'  => 'Imeundwa',
            'updated-at'  => 'Imesasishwa',
        ],

        'groups' => [
            'parent'     => 'Parent',
            'creator'    => 'Creator',
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'filters' => [
            'parent'  => 'Parent',
            'creator' => 'Creator',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Kategoria imefutwa',
                        'body'  => 'Kategoria ina imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Kategoria could si kuwa imefutwa',
                        'body'  => 'kategoria haiwezekani kuwa imefutwa kwa sababu hii ni currently katika tumia.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Kategoria imefutwa',
                        'body'  => 'kategoria ina imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Kategoria could si kuwa imefutwa',
                        'body'  => 'kategoria haiwezekani kuwa imefutwa kwa sababu wao ni currently katika tumia.',
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
                    'name'        => 'Jina',
                    'parent'      => 'Parent Kategoria',
                    'full_name'   => 'Kamili Kategoria Jina',
                    'parent_path' => 'Kategoria Path',
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
