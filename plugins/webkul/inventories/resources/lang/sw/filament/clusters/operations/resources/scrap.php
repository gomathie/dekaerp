<?php

return [
    'navigation' => [
        'title' => 'Scraps',
        'group' => 'Adjustments',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Jumla',

                'fields' => [
                    'product'              => 'Bidhaa',
                    'package'              => 'Kifurushi',
                    'quantity'             => 'Kiasi',
                    'unit'                 => 'Kipimo',
                    'lot'                  => 'Kundi/Nambari ya mfululizo',
                    'tags'                 => 'Lebo',
                    'name'                 => 'Jina',
                    'color'                => 'Rangi',
                    'owner'                => 'Mmiliki',
                    'source-location'      => 'Chanzo Mahali',
                    'destination-location' => 'Scrap Mahali',
                    'source-document'      => 'Chanzo Hati',
                    'company'              => 'Kampuni',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'date'            => 'Tarehe',
            'reference'       => 'Kumbukumbu',
            'product'         => 'Bidhaa',
            'package'         => 'Kifurushi',
            'quantity'        => 'Kiasi',
            'uom'             => 'Kipimo',
            'source-location' => 'Chanzo Mahali',
            'scrap-location'  => 'Scrap Mahali',
            'unit'            => 'Kipimo',
            'lot'             => 'Kundi/Nambari ya mfululizo',
            'tags'            => 'Lebo',
            'state'           => 'Hali',
        ],

        'groups' => [
            'product'              => 'Bidhaa',
            'source-location'      => 'Chanzo Mahali',
            'destination-location' => 'Scrap Mahali',
        ],

        'filters' => [
            'source-location'      => 'Chanzo Mahali',
            'destination-location' => 'Scrap Mahali',
            'product'              => 'Bidhaa',
            'state'                => 'Hali',
            'product-category'     => 'Bidhaa Kategoria',
            'uom'                  => 'Kipimo',
            'lot'                  => 'Kundi/Nambari ya mfululizo',
            'package'              => 'Kifurushi',
            'tags'                 => 'Lebo',
            'company'              => 'Kampuni',
            'quantity'             => 'Kiasi',
            'creator'              => 'Creator',
            'closed-at'            => 'Imefungwa Saa',
            'created-at'           => 'Imeundwa',
            'updated-at'           => 'Imesasishwa',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Scrap imefutwa',
                        'body'  => 'scrap ina imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Scrap could si kuwa imefutwa',
                        'body'  => 'scrap haiwezekani kuwa imefutwa kwa sababu hii ni currently katika tumia.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Scraps imefutwa',
                        'body'  => 'imechaguliwa scraps ina imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Scraps could si kuwa imefutwa',
                        'body'  => 'scraps haiwezekani kuwa imefutwa kwa sababu wao ni currently katika tumia.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Scrap Maelezo',

                'entries' => [
                    'product'              => 'Bidhaa',
                    'quantity'             => 'Kiasi',
                    'lot'                  => 'Kundi',
                    'tags'                 => 'Lebo',
                    'package'              => 'Kifurushi',
                    'owner'                => 'Mmiliki',
                    'source-location'      => 'Chanzo Mahali',
                    'destination-location' => 'Scrap Mahali',
                    'source-document'      => 'Chanzo Hati',
                    'company'              => 'Kampuni',
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
