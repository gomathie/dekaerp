<?php

return [
    'navigation' => [
        'title' => 'Makundi / Nambari ya mfululizo Numbers',
        'group' => 'Ghala',
    ],

    'global-search' => [
        'ref'     => 'Kumbukumbu',
        'product' => 'Bidhaa',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Jumla',

                'fields' => [
                    'name'                   => 'Jina',
                    'name-placeholder'       => 'e.g. Kundi/0001/20121',
                    'product'                => 'Bidhaa',
                    'product-hint-tooltip'   => 'bidhaa associated na hii kundi/nambari ya mfululizo nambari. Hii haiwezekani kuwa imebadilishwa ikiwa hii ina tayari moved.',
                    'company'                => 'Kampuni',
                    'reference'              => 'Kumbukumbu',
                    'reference-hint-tooltip' => 'ndani kumbukumbu nambari, ikiwa different kutoka manufacturer\'s kundi/nambari ya mfululizo nambari.',
                    'description'            => 'Maelezo',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Jina',
            'product'      => 'Bidhaa',
            'on-hand-qty'  => 'Washa Mkono Kiasi',
            'reference'    => 'Ndani Kumbukumbu',
            'created-at'   => 'Imeundwa',
            'updated-at'   => 'Imesasishwa',
        ],

        'groups' => [
            'product'        => 'Bidhaa',
            'location'       => 'Mahali',
            'created-at'     => 'Imeundwa',
        ],

        'filters' => [
            'product'  => 'Bidhaa',
            'location' => 'Mahali',
            'creator'  => 'Creator',
            'company'  => 'Kampuni',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Kundi imefutwa',
                        'body'  => 'kundi ina imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Kundi could si kuwa imefutwa',
                        'body'  => 'kundi haiwezekani kuwa imefutwa kwa sababu hii ni currently katika tumia.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print' => [
                'label' => 'Chapisha Msimbo pau',
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Makundi imefutwa',
                        'body'  => 'makundi ina imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Makundi could si kuwa imefutwa',
                        'body'  => 'makundi haiwezekani kuwa imefutwa kwa sababu wao ni currently katika tumia.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Kundi/Nambari ya mfululizo Nambari Maelezo',

                'entries' => [
                    'name'        => 'Kundi Jina',
                    'product'     => 'Bidhaa',
                    'reference'   => 'Kumbukumbu',
                    'description' => 'Maelezo',
                    'on-hand-qty' => 'Washa-Mkono Kiasi',
                    'company'     => 'Kampuni',
                    'created-at'  => 'Imeundwa',
                    'updated-at'  => 'Mwisho Imesasishwa',
                ],
            ],

            'record-information' => [
                'title' => 'Rekodi Taarifa',

                'entries' => [
                    'created-by'   => 'Imeundwa Na',
                    'created-at'   => 'Imeundwa',
                    'last-updated' => 'Mwisho Imesasishwa',
                ],
            ],
        ],
    ],
];
