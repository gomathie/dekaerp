<?php

return [
    'title' => 'Attributes',

    'form' => [
        'attribute' => 'Attribute',
        'values'    => 'Values',
    ],

    'table' => [
        'description' => 'Onyo: adding au deleting attributes ita futa na recreate existing variants na lead hadi hasara ya yao possible customizations.',

        'header-actions' => [
            'create' => [
                'label' => 'Ongeza Attribute',

                'notification' => [
                    'title' => 'Attribute imeundwa',
                    'body'  => 'attribute ina imeundwa successfully.',
                ],
            ],
        ],

        'columns' => [
            'attribute' => 'Attribute',
            'values'    => 'Values',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Attribute imesasishwa',
                    'body'  => 'attribute ina imesasishwa successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Attribute imefutwa',
                    'body'  => 'attribute ina imefutwa successfully.',
                ],
            ],
        ],
    ],
];
