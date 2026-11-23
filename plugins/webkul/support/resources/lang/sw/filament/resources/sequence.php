<?php

return [
    'model-label' => 'Mfuatano',

    'plural-model-label' => 'Sequences',

    'navigation' => [
        'title' => 'Sequences',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'       => 'Jumla',
                'description' => 'Sequences kwa majarida, maghala na operation types ni imeundwa kiotomatiki wakati zile rekodi ni imeundwa — hariri wao hapa. Kwa mkono creation ni tu needed kwa maalum msimbo-based sequences.',

                'fields' => [
                    'name'      => 'Jina',
                    'code'      => 'Msimbo',
                    'code-help' => 'Technical identifier inatumika na hati, e.g. mauzo.agizo. Auto-imeundwa sequences tayari ina kulia msimbo.',
                    'company'   => 'Kampuni',
                ],
            ],

            'format' => [
                'title' => 'Numbering',

                'fields' => [
                    'prefix'           => 'Kiambishi',
                    'prefix-help'      => 'Placeholders: %(mwaka), %(y), %(mwezi), %(siku). Mfano: INV/%(mwaka)/',
                    'suffix'           => 'Kiambishi tamati',
                    'padding'          => 'Nambari Padding',
                    'next-number'      => 'Ifuatayo Nambari',
                    'next-number-help' => 'Unaweza tu kuwa increased. Hadi restart numbering safely, futa mfuatano — hii ita kuwa recreated kutoka highest existing hati nambari.',
                    'step'             => 'Step',
                    'reset-frequency'  => 'Weka upya Counter',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'            => 'Jina',
            'code'            => 'Msimbo',
            'applies-to'      => 'Inatumika Hadi',
            'company'         => 'Kampuni',
            'next-preview'    => 'Ifuatayo Hati Nambari',
            'next-number'     => 'Ifuatayo Nambari',
            'reset-frequency' => 'Weka upya Counter',
        ],

        'variants' => [
            'refund'         => 'Rudisho',
            'payment'        => 'Malipo',
            'refund-payment' => 'Rudisho + Malipo',
        ],

        'filters' => [
            'company' => 'Kampuni',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Mfuatano imesasishwa',
                    'body'  => 'mfuatano ina imesasishwa successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Mfuatano imefutwa',
                    'body'  => 'mfuatano ina imefutwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Sequences imefutwa',
                    'body'  => 'sequences ina imefutwa successfully.',
                ],
            ],
        ],
    ],
];
