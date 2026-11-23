<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'company'            => 'Kampuni',
                'country'            => 'Nchi',
                'name'               => 'Jina',
                'preceding-subtotal' => 'Preceding Jumla ndogo',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'company'            => 'Kampuni',
            'country'            => 'Nchi',
            'created-by'         => 'Imeundwa Na',
            'name'               => 'Jina',
            'preceding-subtotal' => 'Preceding Jumla ndogo',
            'created-at'         => 'Imeundwa',
            'updated-at'         => 'Imesasishwa',
        ],

        'groups' => [
            'name'       => 'Jina',
            'company'    => 'Kampuni',
            'country'    => 'Nchi',
            'created-by' => 'Imeundwa Na',
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Kodi Kundi imefutwa',
                        'body'  => 'kodi kundi ina imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Kodi Kundi could si kuwa imefutwa',
                        'body'  => 'kodi kundi haiwezekani kuwa imefutwa kwa sababu hii ni currently katika tumia.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Kodi Groups imefutwa',
                        'body'  => 'kodi groups ina imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Kodi Groups could si kuwa imefutwa',
                        'body'  => 'kodi groups haiwezekani kuwa imefutwa kwa sababu wao ni currently katika tumia.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'company'            => 'Kampuni',
                'country'            => 'Nchi',
                'name'               => 'Jina',
                'preceding-subtotal' => 'Preceding Jumla ndogo',
            ],
        ],
    ],
];
