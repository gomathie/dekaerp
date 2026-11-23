<?php

return [
    'title' => 'Manage Chaguo-msingi Akaunti',

    'setup' => [
        'action'       => 'Weka juu uhasibu kwa hii kampuni',
        'notice'       => 'Uhasibu ni si weka juu kwa hii kampuni bado. Tumia "Weka juu uhasibu" juu ya hadi unda yake chati ya akaunti, majarida na chaguo-msingi mipangilio.',
        'notification' => [
            'title' => 'Uhasibu weka juu',
            'body'  => 'Chati ya akaunti, majarida na chaguo-msingi mipangilio zilikuwa imeundwa kwa hii kampuni.',
        ],
    ],

    'form' => [
        'exchange-difference-entries' => [
            'label' => 'Exchange Difference Maingizo',

            'fields' => [
                'journal' => [
                    'label' => 'Jarida',
                ],

                'gain' => [
                    'label' => 'Gain',
                ],

                'loss' => [
                    'label' => 'Hasara',
                ],
            ],
        ],

        'bank-transfer-and-payments' => [
            'label' => 'Benki Uhamisho na Malipo',

            'fields' => [
                'bank-suspense-account' => [
                    'label' => 'Benki Suspense Akaunti',
                ],

                'transfer-account' => [
                    'label' => 'Uhamisho Akaunti',
                ],
            ],
        ],

        'product-accounts' => [
            'label' => 'Bidhaa Akaunti',

            'fields' => [
                'income-account' => [
                    'label' => 'Mapato Akaunti',
                ],

                'expense-account' => [
                    'label' => 'Gharama Akaunti',
                ],
            ],
        ],
    ],
];
