<?php

return [
    'model-label' => 'Mfuatano',

    'plural-model-label' => 'Mifuatano',

    'navigation' => [
        'title' => 'Mifuatano',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'       => 'Jumla',
                'description' => 'Mifuatano ya majarida, maghala na aina za operesheni huundwa kiotomatiki rekodi hizo zinapoundwa — zihariri hapa. Kuunda kwa mkono kunahitajika tu kwa mifuatano maalum inayotegemea msimbo.',

                'fields' => [
                    'name'      => 'Jina',
                    'code'      => 'Msimbo',
                    'code-help' => 'Kitambulisho cha kiufundi kinachotumiwa na hati, mf. sales.order. Mifuatano iliyoundwa kiotomatiki tayari ina msimbo sahihi.',
                    'company'   => 'Kampuni',
                ],
            ],

            'format' => [
                'title' => 'Upangaji wa namba',

                'fields' => [
                    'prefix'           => 'Kiambishi awali',
                    'prefix-help'      => 'Vishika nafasi: %(year), %(y), %(month), %(day). Mfano: INV/%(year)/',
                    'suffix'           => 'Kiambishi tamati',
                    'padding'          => 'Idadi ya tarakimu',
                    'next-number'      => 'Namba inayofuata',
                    'next-number-help' => 'Inaweza kuongezwa tu. Ili kuanza upya upangaji wa namba kwa usalama, futa mfuatano — utaundwa upya kuanzia namba ya juu zaidi ya hati iliyopo.',
                    'step'             => 'Hatua',
                    'reset-frequency'  => 'Weka upya kihesabu',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'            => 'Jina',
            'code'            => 'Msimbo',
            'applies-to'      => 'Inatumika kwa',
            'company'         => 'Kampuni',
            'next-preview'    => 'Namba ya hati inayofuata',
            'next-number'     => 'Namba inayofuata',
            'reset-frequency' => 'Weka upya kihesabu',
        ],

        'variants' => [
            'refund'         => 'Marejesho',
            'payment'        => 'Malipo',
            'refund-payment' => 'Marejesho + malipo',
        ],

        'filters' => [
            'company' => 'Kampuni',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Mfuatano umesasishwa',
                    'body'  => 'Mfuatano umesasishwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Mfuatano umefutwa',
                    'body'  => 'Mfuatano umefutwa kikamilifu.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Mifuatano imefutwa',
                    'body'  => 'Mifuatano imefutwa kikamilifu.',
                ],
            ],
        ],
    ],
];
