<?php

return [
    'navigation' => [
        'title' => 'Njia',
        'group' => 'Ghala Management',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Jumla',

                'fields' => [
                    'route'             => 'Njia',
                    'route-placeholder' => 'eg. Two Step Reception',
                    'company'           => 'Kampuni',
                ],
            ],

            'applicable-on' => [
                'title'       => 'Applicable Washa',
                'description' => 'Chagua maeneo wapi hii njia unaweza kuwa imetumika.',

                'fields' => [
                    'products'                        => 'Bidhaa',
                    'products-hint-tooltip'           => 'Ikiwa imechaguliwa, hii njia ita kuwa inapatikana kwa selection washa bidhaa.',
                    'product-categories'              => 'Bidhaa Kategoria',
                    'product-categories-hint-tooltip' => 'Ikiwa imechaguliwa, hii njia ita kuwa inapatikana kwa selection washa bidhaa kategoria.',
                    'warehouses'                      => 'Maghala',
                    'warehouses-hint-tooltip'         => 'Wakati ghala ni amekabidhiwa hadi hii njia, hii ita kuwa considered chaguo-msingi njia kwa bidhaa moving kupitia hiyo ghala.',
                    'packaging'                       => 'Kifungashio',
                    'packaging-hint-tooltip'          => 'Ikiwa imechaguliwa, hii njia ita kuwa inapatikana kwa selection washa kifungashio.',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'route'      => 'Njia',
            'company'    => 'Kampuni',
            'deleted-at' => 'Imefutwa',
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'groups' => [
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'filters' => [
            'company'  => 'Kampuni',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Njia imesasishwa',
                    'body'  => 'njia ina imesasishwa successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Njia restored',
                    'body'  => 'njia ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Njia imefutwa',
                    'body'  => 'njia ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Njia force imefutwa',
                        'body'  => 'njia ina force imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Njia could si kuwa imefutwa',
                        'body'  => 'njia haiwezekani kuwa imefutwa kwa sababu hii ni currently katika tumia.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Njia restored',
                    'body'  => 'njia ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Njia imefutwa',
                    'body'  => 'njia ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Njia force imefutwa',
                        'body'  => 'njia ina force imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Njia could si kuwa imefutwa',
                        'body'  => 'njia haiwezekani kuwa imefutwa kwa sababu wao ni currently katika tumia.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'  => 'Jumla',

                'entries' => [
                    'route'             => 'Njia',
                    'route-placeholder' => 'eg. Two Step Reception',
                    'company'           => 'Kampuni',
                ],
            ],

            'applicable-on' => [
                'title'       => 'Applicable Washa',
                'description' => 'Chagua places wapi hii njia unaweza kuwa imechaguliwa.',

                'entries' => [
                    'products'                        => 'Bidhaa',
                    'products-hint-tooltip'           => 'Ikiwa imechaguliwa, hii njia ita kuwa inapatikana kwa selection washa bidhaa.',
                    'product-categories'              => 'Bidhaa Kategoria',
                    'product-categories-hint-tooltip' => 'Ikiwa imechaguliwa, hii njia ita kuwa inapatikana kwa selection washa bidhaa kategoria.',
                    'warehouses'                      => 'Maghala',
                    'warehouses-hint-tooltip'         => 'Wakati ghala ni amekabidhiwa hadi hii njia, hii ita kuwa considered chaguo-msingi njia kwa bidhaa moving kupitia hiyo ghala.',
                    'packaging'                       => 'Kifungashio',
                    'packaging-hint-tooltip'          => 'Ikiwa imechaguliwa, hii njia ita kuwa inapatikana kwa selection washa kifungashio.',
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
