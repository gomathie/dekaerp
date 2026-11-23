<?php

return [
    'navigation' => [
        'title' => 'Msambazaji Bei Orodha',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Jumla',

                'fields' => [
                    'vendor'                      => 'Msambazaji',
                    'vendor-product-name'         => 'Msambazaji Bidhaa Jina',
                    'vendor-product-name-tooltip' => 'vendor\'s bidhaa jina ita appear washa request kwa nukuu. Likizo hii blank hadi tumia ndani bidhaa jina.',
                    'vendor-product-code'         => 'Msambazaji Bidhaa Msimbo',
                    'vendor-product-code-tooltip' => 'vendor\'s bidhaa msimbo ita appear washa request kwa nukuu. Likizo hii blank hadi tumia ndani msimbo.',
                    'delay'                       => 'Usafirishaji Lead Time (Siku)',
                    'delay-tooltip'               => 'lead time (katika siku) kutoka agizo la ununuzi confirmation hadi bidhaa stakabadhi katika ghala. Inatumika na scheduler kwa kiotomatiki ununuzi agizo planning.',
                ],
            ],

            'prices' => [
                'title'  => 'Bei',

                'fields' => [
                    'product'            => 'Bidhaa',
                    'quantity'           => 'Kiasi',
                    'quantity-tooltip'   => 'minimum kiasi inahitajika hadi ununuzi kutoka hii msambazaji hadi qualify kwa specified bei. Hii ni expressed katika vendor\'s Bidhaa Kipimo au, ikiwa si weka, product\'s chaguo-msingi uniti ya measure.',
                    'unit-price'         => 'Bei ya uniti',
                    'unit-price-tooltip' => 'bei kwa uniti kwa hii bidhaa kutoka msambazaji, expressed katika msambazaji’s Bidhaa Kipimo au, ikiwa si weka, product\'s chaguo-msingi uniti ya measure.',
                    'currency'           => 'Sarafu',
                    'valid-from'         => 'Halali Kutoka',
                    'valid-to'           => 'Halali Mpaka',
                    'discount'           => 'Punguzo (%)',
                    'company'            => 'Kampuni',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'vendor'              => 'Msambazaji',
            'vendor-product-name' => 'Msambazaji Bidhaa Jina',
            'vendor-product-code' => 'Msambazaji Bidhaa Msimbo',
            'delay'               => 'Usafirishaji Lead Time (Siku)',
            'product'             => 'Bidhaa',
            'quantity'            => 'Kiasi',
            'unit-price'          => 'Bei ya uniti',
            'currency'            => 'Sarafu',
            'valid-from'          => 'Halali Kutoka',
            'valid-to'            => 'Halali Mpaka',
            'discount'            => 'Punguzo (%)',
            'company'             => 'Kampuni',
            'created-at'          => 'Imeundwa',
            'updated-at'          => 'Imesasishwa',
        ],

        'filters' => [
            'vendor'        => 'Kichujio na Msambazaji',
            'product'       => 'Kichujio na Bidhaa',
            'currency'      => 'Kichujio na Sarafu',
            'company'       => 'Kichujio na Kampuni',
            'price-from'    => 'Minimum Bei',
            'price-to'      => 'Maximum Bei',
            'min-qty-from'  => 'Minimum Kiasi Kutoka',
            'min-qty-to'    => 'Minimum Kiasi Hadi',
            'starts-from'   => 'Halali Kutoka Tarehe',
            'ends-before'   => 'Halali Hadi Tarehe',
            'created-from'  => 'Imeundwa Kutoka',
            'created-until' => 'Imeundwa Mpaka',
        ],

        'groups' => [
            'vendor'     => 'Msambazaji',
            'product'    => 'Bidhaa',
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Msambazaji Bei imefutwa',
                        'body'  => 'msambazaji bei ina imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Msambazaji Bei could si kuwa imefutwa',
                        'body'  => 'msambazaji bei haiwezekani kuwa imefutwa kwa sababu hii ni currently katika tumia.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Msambazaji Bei imefutwa',
                        'body'  => 'msambazaji bei ina imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Msambazaji Bei could si kuwa imefutwa',
                        'body'  => 'msambazaji bei haiwezekani kuwa imefutwa kwa sababu wao ni currently katika tumia.',
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
                    'vendor'                      => 'Msambazaji',
                    'vendor-product-name'         => 'Msambazaji Bidhaa Jina',
                    'vendor-product-name-tooltip' => 'vendor\'s bidhaa jina ita appear washa request kwa nukuu. Likizo hii blank hadi tumia ndani bidhaa jina.',
                    'vendor-product-code'         => 'Msambazaji Bidhaa Msimbo',
                    'vendor-product-code-tooltip' => 'vendor\'s bidhaa msimbo ita appear washa request kwa nukuu. Likizo hii blank hadi tumia ndani msimbo.',
                    'delay'                       => 'Usafirishaji Lead Time (Siku)',
                    'delay-tooltip'               => 'lead time (katika siku) kutoka agizo la ununuzi confirmation hadi bidhaa stakabadhi katika ghala. Inatumika na scheduler kwa kiotomatiki ununuzi agizo planning.',
                ],
            ],

            'record-information' => [
                'title'  => 'Rekodi Taarifa',

                'entries' => [
                    'created-by'   => 'Imeundwa Na',
                    'created-at'   => 'Imeundwa',
                    'last-updated' => 'Mwisho Imesasishwa',
                ],
            ],

            'prices' => [
                'title'  => 'Bei',

                'entries' => [
                    'product'            => 'Bidhaa',
                    'quantity'           => 'Kiasi',
                    'quantity-tooltip'   => 'minimum kiasi inahitajika hadi ununuzi kutoka hii msambazaji hadi qualify kwa specified bei. Hii ni expressed katika vendor\'s Bidhaa Kipimo au, ikiwa si weka, product\'s chaguo-msingi uniti ya measure.',
                    'unit-price'         => 'Bei ya uniti',
                    'unit-price-tooltip' => 'bei kwa uniti kwa hii bidhaa kutoka msambazaji, expressed katika msambazaji’s Bidhaa Kipimo au, ikiwa si weka, product\'s chaguo-msingi uniti ya measure.',
                    'currency'           => 'Sarafu',
                    'valid-from'         => 'Halali Kutoka',
                    'valid-to'           => 'Halali Mpaka',
                    'discount'           => 'Punguzo (%)',
                    'company'            => 'Kampuni',
                ],
            ],
        ],
    ],
];
