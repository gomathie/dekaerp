<?php

return [
    'notification' => [
        'title' => 'Bidhaa imesasishwa',
        'body'  => 'bidhaa ina imesasishwa successfully.',
    ],

    'header-actions' => [
        'update-quantity' => [
            'label'                     => 'Sasisha Kiasi',
            'modal-heading'             => 'Sasisha Bidhaa Kiasi',
            'modal-submit-action-label' => 'Sasisha',

            'form' => [
                'fields' => [
                    'on-hand-qty' => 'Washa Mkono Kiasi',
                ],
            ],
        ],

        'delete' => [
            'notification' => [
                'title' => 'Bidhaa imefutwa',
                'body'  => 'bidhaa ina imefutwa successfully.',
            ],
        ],
    ],
];
