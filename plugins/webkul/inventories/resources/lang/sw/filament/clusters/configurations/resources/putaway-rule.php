<?php

return [
    'navigation' => [
        'title' => 'Putaway Rules',
        'group' => 'Ghala Management',
    ],

    'form' => [
        'fields' => [
            'in-location'          => 'Wakati Bidhaa Arrives Katika',
            'product'              => 'Bidhaa',
            'product-placeholder'  => 'Zote Bidhaa',
            'category'             => 'Bidhaa Kategoria',
            'category-placeholder' => 'Zote Kategoria',
            'storage-category'     => 'Storage Kategoria',
            'out-location'         => 'Store Hadi',
            'sub-location'         => 'Sub Mahali',
            'company'              => 'Kampuni',
        ],
    ],

    'table' => [
        'columns' => [
            'in-location'      => 'Wakati Bidhaa Arrives Katika',
            'product'          => 'Bidhaa',
            'category'         => 'Bidhaa Kategoria',
            'storage-category' => 'Storage Kategoria',
            'out-location'     => 'Store Hadi',
            'sub-location'     => 'Sub Mahali',
            'company'          => 'Kampuni',
            'deleted-at'       => 'Imefutwa',
            'created-at'       => 'Imeundwa',
            'updated-at'       => 'Imesasishwa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Putaway rule imesasishwa',
                    'body'  => 'putaway rule ina imesasishwa successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Putaway rule restored',
                    'body'  => 'putaway rule ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Putaway rule imefutwa',
                    'body'  => 'putaway rule ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'error' => [
                        'title' => 'Putaway rule could si kuwa imefutwa',
                        'body'  => 'putaway rule haiwezekani kuwa permanently imefutwa kwa sababu hii ni referenced na nyingine rekodi.',
                    ],

                    'success' => [
                        'title' => 'Putaway rule permanently imefutwa',
                        'body'  => 'putaway rule ina permanently imefutwa successfully.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Putaway rules restored',
                    'body'  => 'putaway rules ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Putaway rules imefutwa',
                    'body'  => 'putaway rules ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'error' => [
                        'title' => 'Putaway rules could si kuwa imefutwa',
                        'body'  => 'Baadhi putaway rules haiwezekani kuwa permanently imefutwa kwa sababu wao ni referenced na nyingine rekodi.',
                    ],

                    'success' => [
                        'title' => 'Putaway rules permanently imefutwa',
                        'body'  => 'putaway rules ina permanently imefutwa successfully.',
                    ],
                ],
            ],
        ],
    ],
];
