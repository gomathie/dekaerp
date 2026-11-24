<?php

return [
    'title' => 'Simamia akaunti chaguomsingi',

    'setup' => [
        'action'       => 'Weka uhasibu kwa kampuni hii',
        'notice'       => 'Uhasibu bado haujawekwa kwa kampuni hii. Tumia "Weka uhasibu" hapo juu ili kuunda chati yake ya akaunti, majarida na mipangilio chaguomsingi.',
        'notification' => [
            'title' => 'Uhasibu umewekwa',
            'body'  => 'Chati ya akaunti, majarida na mipangilio chaguomsingi vimeundwa kwa kampuni hii.',
        ],
    ],

    'form' => [
        'exchange-difference-entries' => [
            'label' => 'Maingizo ya tofauti za ubadilishaji',

            'fields' => [
                'journal' => [
                    'label' => 'Jarida',
                ],

                'gain' => [
                    'label' => 'Faida',
                ],

                'loss' => [
                    'label' => 'Hasara',
                ],
            ],
        ],

        'bank-transfer-and-payments' => [
            'label' => 'Uhamisho wa benki na malipo',

            'fields' => [
                'bank-suspense-account' => [
                    'label' => 'Akaunti ya muda ya benki',
                ],

                'transfer-account' => [
                    'label' => 'Akaunti ya uhamisho',
                ],
            ],
        ],

        'product-accounts' => [
            'label' => 'Akaunti za bidhaa',

            'fields' => [
                'income-account' => [
                    'label' => 'Akaunti ya mapato',
                ],

                'expense-account' => [
                    'label' => 'Akaunti ya gharama',
                ],
            ],
        ],
    ],
];
