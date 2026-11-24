<?php

return [
    'navigation' => [
        'title' => 'Orodha za bei za wasambazaji',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Jumla',

                'fields' => [
                    'vendor'                      => 'Msambazaji',
                    'vendor-product-name'         => 'Jina la bidhaa kwa msambazaji',
                    'vendor-product-name-tooltip' => 'Jina la bidhaa la msambazaji litaonekana kwenye ombi la nukuu ya bei. Liache wazi ili kutumia jina la ndani la bidhaa.',
                    'vendor-product-code'         => 'Msimbo wa bidhaa kwa msambazaji',
                    'vendor-product-code-tooltip' => 'Msimbo wa bidhaa wa msambazaji utaonekana kwenye ombi la nukuu ya bei. Uache wazi ili kutumia msimbo wa ndani.',
                    'delay'                       => 'Muda wa uwasilishaji (siku)',
                    'delay-tooltip'               => 'Muda (kwa siku) kuanzia kuthibitishwa kwa oda ya ununuzi hadi bidhaa kupokelewa ghalani. Hutumiwa na kipanga ratiba kupanga oda za ununuzi kiotomatiki.',
                ],
            ],

            'prices' => [
                'title'  => 'Bei',

                'fields' => [
                    'product'            => 'Bidhaa',
                    'quantity'           => 'Idadi',
                    'quantity-tooltip'   => 'Idadi ya chini inayohitajika kununua kutoka kwa msambazaji huyu ili kupata bei iliyotajwa. Hupimwa kwa kipimo cha bidhaa cha msambazaji au, kama hakijawekwa, kipimo chaguomsingi cha bidhaa.',
                    'unit-price'         => 'Bei ya kipimo',
                    'unit-price-tooltip' => 'Bei kwa kila kipimo cha bidhaa hii kutoka kwa msambazaji, kwa kipimo cha bidhaa cha msambazaji au, kama hakijawekwa, kipimo chaguomsingi cha bidhaa.',
                    'currency'           => 'Sarafu',
                    'valid-from'         => 'Halali kuanzia',
                    'valid-to'           => 'Halali hadi',
                    'discount'           => 'Punguzo (%)',
                    'company'            => 'Kampuni',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'vendor'              => 'Msambazaji',
            'vendor-product-name' => 'Jina la bidhaa kwa msambazaji',
            'vendor-product-code' => 'Msimbo wa bidhaa kwa msambazaji',
            'delay'               => 'Muda wa uwasilishaji (siku)',
            'product'             => 'Bidhaa',
            'quantity'            => 'Idadi',
            'unit-price'          => 'Bei ya kipimo',
            'currency'            => 'Sarafu',
            'valid-from'          => 'Halali kuanzia',
            'valid-to'            => 'Halali hadi',
            'discount'            => 'Punguzo (%)',
            'company'             => 'Kampuni',
            'created-at'          => 'Iliundwa',
            'updated-at'          => 'Ilisasishwa',
        ],

        'filters' => [
            'vendor'        => 'Chuja kwa msambazaji',
            'product'       => 'Chuja kwa bidhaa',
            'currency'      => 'Chuja kwa sarafu',
            'company'       => 'Chuja kwa kampuni',
            'price-from'    => 'Bei ya chini',
            'price-to'      => 'Bei ya juu',
            'min-qty-from'  => 'Idadi ya chini kuanzia',
            'min-qty-to'    => 'Idadi ya chini hadi',
            'starts-from'   => 'Halali kuanzia tarehe',
            'ends-before'   => 'Halali hadi tarehe',
            'created-from'  => 'Iliundwa kuanzia',
            'created-until' => 'Iliundwa hadi',
        ],

        'groups' => [
            'vendor'     => 'Msambazaji',
            'product'    => 'Bidhaa',
            'created-at' => 'Iliundwa',
            'updated-at' => 'Ilisasishwa',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Bei ya msambazaji imefutwa',
                        'body'  => 'Bei ya msambazaji imefutwa.',
                    ],

                    'error' => [
                        'title' => 'Bei ya msambazaji haikuweza kufutwa',
                        'body'  => 'Bei ya msambazaji haiwezi kufutwa kwa sababu inatumika.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Bei za wasambazaji zimefutwa',
                        'body'  => 'Bei za wasambazaji zimefutwa.',
                    ],

                    'error' => [
                        'title' => 'Bei za wasambazaji hazikuweza kufutwa',
                        'body'  => 'Bei za wasambazaji haziwezi kufutwa kwa sababu zinatumika.',
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
                    'vendor-product-name'         => 'Jina la bidhaa kwa msambazaji',
                    'vendor-product-name-tooltip' => 'Jina la bidhaa la msambazaji litaonekana kwenye ombi la nukuu ya bei. Liache wazi ili kutumia jina la ndani la bidhaa.',
                    'vendor-product-code'         => 'Msimbo wa bidhaa kwa msambazaji',
                    'vendor-product-code-tooltip' => 'Msimbo wa bidhaa wa msambazaji utaonekana kwenye ombi la nukuu ya bei. Uache wazi ili kutumia msimbo wa ndani.',
                    'delay'                       => 'Muda wa uwasilishaji (siku)',
                    'delay-tooltip'               => 'Muda (kwa siku) kuanzia kuthibitishwa kwa oda ya ununuzi hadi bidhaa kupokelewa ghalani. Hutumiwa na kipanga ratiba kupanga oda za ununuzi kiotomatiki.',
                ],
            ],

            'record-information' => [
                'title'  => 'Taarifa za rekodi',

                'entries' => [
                    'created-by'   => 'Iliundwa na',
                    'created-at'   => 'Iliundwa',
                    'last-updated' => 'Ilisasishwa mwisho',
                ],
            ],

            'prices' => [
                'title'  => 'Bei',

                'entries' => [
                    'product'            => 'Bidhaa',
                    'quantity'           => 'Idadi',
                    'quantity-tooltip'   => 'Idadi ya chini inayohitajika kununua kutoka kwa msambazaji huyu ili kupata bei iliyotajwa. Hupimwa kwa kipimo cha bidhaa cha msambazaji au, kama hakijawekwa, kipimo chaguomsingi cha bidhaa.',
                    'unit-price'         => 'Bei ya kipimo',
                    'unit-price-tooltip' => 'Bei kwa kila kipimo cha bidhaa hii kutoka kwa msambazaji, kwa kipimo cha bidhaa cha msambazaji au, kama hakijawekwa, kipimo chaguomsingi cha bidhaa.',
                    'currency'           => 'Sarafu',
                    'valid-from'         => 'Halali kuanzia',
                    'valid-to'           => 'Halali hadi',
                    'discount'           => 'Punguzo (%)',
                    'company'            => 'Kampuni',
                ],
            ],
        ],
    ],
];
