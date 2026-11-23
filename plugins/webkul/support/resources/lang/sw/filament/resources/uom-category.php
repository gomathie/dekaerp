<?php

return [
    'navigation' => [
        'title' => 'UOM Kategoria',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Jumla',

                'fields' => [
                    'name' => 'Jina',
                ],
            ],

            'uoms' => [
                'title' => 'Uniti ya Measure',

                'fields' => [
                    'uoms'     => 'Uniti',
                    'type'     => 'Aina',
                    'name'     => 'Kipimo',
                    'ratio'    => 'Ratio',
                    'rounding' => 'Rounding Precision',
                ],

                'validations' => [
                    'missing-reference'          => 'Hii kategoria unapaswa ina kumbukumbu kipimo.',
                    'multiple-references'        => 'Hii kategoria unapaswa tu ina one kumbukumbu kipimo.',
                    'ratio-greater-than-zero'    => 'conversion ratio kwa kipimo haiwezekani kuwa zero.',
                    'rounding-greater-than-zero' => 'rounding precision lazima kuwa strictly positive.',
                ],

                'actions' => [
                    'add' => 'Ongeza Uniti',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Jina',
            'uoms'       => 'UOMs',
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'groups' => [
            'created-at' => 'Imeundwa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'UOM Kategoria imesasishwa',
                    'body'  => 'UOM kategoria ina imesasishwa successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'UOM Kategoria imefutwa',
                    'body'  => 'UOM kategoria ina imefutwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'UOM Kategoria imefutwa',
                    'body'  => 'UOM kategoria ina imefutwa successfully.',
                ],
            ],
        ],
    ],
];
