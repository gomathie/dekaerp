<?php

return [
    'title' => 'Kiolezo cha nukuu',

    'navigation' => [
        'title'  => 'Kiolezo cha nukuu',
        'group'  => 'Oda za mauzo',
    ],

    'form' => [
        'tabs' => [
            'products' => [
                'title'  => 'Bidhaa',
                'fields' => [
                    'products'     => 'Bidhaa',
                    'name'         => 'Jina',
                    'quantity'     => 'Idadi',
                ],
            ],

            'terms-and-conditions' => [
                'title'  => 'Vigezo na masharti',
                'fields' => [
                    'note-placeholder' => 'Andika vigezo na masharti ya nukuu za bei.',
                ],
            ],
        ],

        'sections' => [
            'general' => [
                'title' => 'Taarifa za jumla',

                'fields' => [
                    'name'               => 'Jina',
                    'quotation-validity' => 'Muda wa uhalali wa nukuu',
                    'sale-journal'       => 'Jarida la mauzo',
                ],
            ],

            'signature-and-payment' => [
                'title' => 'Sahihi na malipo',

                'fields' => [
                    'online-signature'      => 'Sahihi ya mtandaoni',
                    'online-payment'        => 'Malipo ya mtandaoni',
                    'prepayment-percentage' => 'Asilimia ya malipo ya awali',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'created-by'            => 'Iliundwa na',
            'company'               => 'Kampuni',
            'name'                  => 'Jina',
            'number-of-days'        => 'Idadi ya siku',
            'journal'               => 'Jarida la mauzo',
            'signature-required'    => 'Sahihi inahitajika',
            'payment-required'      => 'Malipo yanahitajika',
            'prepayment-percentage' => 'Asilimia ya malipo ya awali',
        ],
        'groups'  => [
            'company' => 'Kampuni',
            'name'    => 'Jina',
            'journal' => 'Jarida',
        ],
        'filters' => [
            'created-by' => 'Iliundwa na',
            'company'    => 'Kampuni',
            'name'       => 'Jina',
            'created-at' => 'Iliundwa',
            'updated-at' => 'Ilisasishwa',
        ],
        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Kiolezo cha nukuu kimefutwa',
                    'body'  => 'Kiolezo cha nukuu kimefutwa kwa mafanikio.',
                ],
            ],

        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Kiolezo cha nukuu kimefutwa',
                    'body'  => 'Kiolezo cha nukuu kimefutwa kwa mafanikio.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'tabs' => [
            'products' => [
                'title' => 'Bidhaa',
            ],
            'terms-and-conditions' => [
                'title' => 'Vigezo na masharti',
            ],
        ],
        'sections' => [
            'general' => [
                'title' => 'Taarifa za jumla',
            ],
            'signature_and_payment' => [
                'title' => 'Sahihi na malipo',
            ],
        ],
        'entries' => [
            'product'               => 'Bidhaa',
            'description'           => 'Maelezo',
            'quantity'              => 'Idadi',
            'unit-price'            => 'Bei ya kipimo',
            'section-name'          => 'Jina la sehemu',
            'note-title'            => 'Kichwa cha kidokezo',
            'name'                  => 'Jina la kiolezo',
            'quotation-validity'    => 'Muda wa uhalali wa nukuu',
            'sale-journal'          => 'Jarida la mauzo',
            'online-signature'      => 'Sahihi ya mtandaoni',
            'online-payment'        => 'Malipo ya mtandaoni',
            'prepayment-percentage' => 'Asilimia ya malipo ya awali',
        ],
    ],
];
