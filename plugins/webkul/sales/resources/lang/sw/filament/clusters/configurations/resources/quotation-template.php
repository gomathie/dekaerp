<?php

return [
    'title' => 'Nukuu Kiolezo',

    'navigation' => [
        'title'  => 'Nukuu Kiolezo',
        'group'  => 'Agizo la mauzos',
    ],

    'form' => [
        'tabs' => [
            'products' => [
                'title'  => 'Bidhaa',
                'fields' => [
                    'products'     => 'Bidhaa',
                    'name'         => 'Jina',
                    'quantity'     => 'Kiasi',
                ],
            ],

            'terms-and-conditions' => [
                'title'  => 'Terms & Conditions',
                'fields' => [
                    'note-placeholder' => 'Kuandika yako terms na conditions kwa nukuu.',
                ],
            ],
        ],

        'sections' => [
            'general' => [
                'title' => 'Jumla Taarifa',

                'fields' => [
                    'name'               => 'Jina',
                    'quotation-validity' => 'Nukuu Validity',
                    'sale-journal'       => 'Mauzo Jarida',
                ],
            ],

            'signature-and-payment' => [
                'title' => 'Signature & Malipo',

                'fields' => [
                    'online-signature'      => 'Online Signature',
                    'online-payment'        => 'Online Malipo',
                    'prepayment-percentage' => 'Prepayment Asilimia',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'created-by'            => 'Imeundwa na',
            'company'               => 'Kampuni',
            'name'                  => 'Jina',
            'number-of-days'        => 'Nambari ya siku',
            'journal'               => 'Mauzo Jarida',
            'signature-required'    => 'Signature Inahitajika',
            'payment-required'      => 'Malipo Inahitajika',
            'prepayment-percentage' => 'Prepayment Asilimia',
        ],
        'groups'  => [
            'company' => 'Kampuni',
            'name'    => 'Jina',
            'journal' => 'Jarida',
        ],
        'filters' => [
            'created-by' => 'Imeundwa Na',
            'company'    => 'Kampuni',
            'name'       => 'Jina',
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],
        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Nukuu kiolezo imefutwa',
                    'body'  => 'nukuu kiolezo ina imefutwa successfully.',
                ],
            ],

        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Nukuu kiolezo imefutwa',
                    'body'  => 'nukuu kiolezo ina imefutwa successfully.',
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
                'title' => 'Terms & Conditions',
            ],
        ],
        'sections' => [
            'general' => [
                'title' => 'Jumla Taarifa',
            ],
            'signature_and_payment' => [
                'title' => 'Signature & Malipo',
            ],
        ],
        'entries' => [
            'product'               => 'Bidhaa',
            'description'           => 'Maelezo',
            'quantity'              => 'Kiasi',
            'unit-price'            => 'Bei ya uniti',
            'section-name'          => 'Sehemu Jina',
            'note-title'            => 'Maelezo Kichwa',
            'name'                  => 'Kiolezo Jina',
            'quotation-validity'    => 'Nukuu Validity',
            'sale-journal'          => 'Mauzo Jarida',
            'online-signature'      => 'Online Signature',
            'online-payment'        => 'Online Malipo',
            'prepayment-percentage' => 'Prepayment Asilimia',
        ],
    ],
];
