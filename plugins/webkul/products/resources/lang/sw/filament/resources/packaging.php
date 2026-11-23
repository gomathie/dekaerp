<?php

return [
    'form' => [
        'name'    => 'Jina',
        'barcode' => 'Msimbo pau',
        'product' => 'Bidhaa',
        'routes'  => 'Njia',
        'qty'     => 'Qty',
        'company' => 'Kampuni',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Jina',
            'product'    => 'Bidhaa',
            'routes'     => 'Njia',
            'qty'        => 'Qty',
            'company'    => 'Kampuni',
            'barcode'    => 'Msimbo pau',
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'groups' => [
            'product'    => 'Bidhaa',
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'filters' => [
            'product' => 'Bidhaa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Kifungashio sasisha',
                    'body'  => 'kifungashio ina sasisha successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Kifungashio imefutwa',
                        'body'  => 'kifungashio ina imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Kifungashio could si kuwa imefutwa',
                        'body'  => 'kifungashio haiwezekani kuwa imefutwa kwa sababu hii ni currently katika tumia.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print' => [
                'label' => 'Chapisha',
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Packagings imefutwa',
                        'body'  => 'packagings ina imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Packagings could si kuwa imefutwa',
                        'body'  => 'packagings haiwezekani kuwa imefutwa kwa sababu wao ni currently katika tumia.',
                    ],
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'label' => 'Mpya Kifungashio',

                'notification' => [
                    'title' => 'Kifungashio imeundwa',
                    'body'  => 'kifungashio ina imeundwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Jumla Taarifa',

                'entries' => [
                    'name'    => 'Kifurushi Jina',
                    'barcode' => 'Msimbo pau',
                    'product' => 'Bidhaa',
                    'qty'     => 'Kiasi',
                ],
            ],

            'organization' => [
                'title' => 'Organization Maelezo',

                'entries' => [
                    'company'    => 'Kampuni',
                    'creator'    => 'Imeundwa Na',
                    'created_at' => 'Imeundwa',
                    'updated_at' => 'Mwisho Imesasishwa',
                ],
            ],
        ],
    ],
];
