<?php

return [
    'notification' => [
        'title' => 'Ununuzi Agreement imesasishwa',
        'body'  => 'ununuzi agreement ina imesasishwa successfully.',
    ],

    'header-actions' => [
        'confirm' => [
            'label' => 'Thibitisha',

            'notification' => [
                'unable' => [
                    'title' => 'Haiwezekani hadi thibitisha ununuzi agreement',
                    'body'  => 'Ongeza saa kidogo one bidhaa mstari kabla ya confirming hii ununuzi agreement.',
                ],
            ],
        ],

        'close' => [
            'label'        => 'Funga',
            'notification' => [
                'warning' => [
                    'title' => 'Haiwezekani hadi funga ununuzi agreement',
                    'body'  => 'Wewe haiwezekani funga hii ununuzi agreement kwa sababu baadhi related RFQs ni si katika Imekamilika au Imeghairiwa hali.',
                ],
            ],
        ],

        'cancel' => [
            'label' => 'Ghairi',
        ],

        'print' => [
            'label' => 'Chapisha',
        ],

        'delete' => [
            'notification' => [
                'title' => 'Ununuzi Agreement imefutwa',
                'body'  => 'ununuzi agreement ina imefutwa successfully.',
            ],
        ],
    ],
];
