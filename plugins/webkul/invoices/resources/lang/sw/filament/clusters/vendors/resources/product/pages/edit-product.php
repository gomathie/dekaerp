<?php

return [
    'notification' => [
        'title' => 'Bidhaa imesasishwa',
        'body'  => 'Bidhaa imesasishwa kwa mafanikio.',
    ],

    'header-actions' => [
        'update-quantity' => [
            'label'                     => 'Sasisha idadi',
            'modal-heading'             => 'Sasisha idadi ya bidhaa',
            'modal-submit-action-label' => 'Sasisha',

            'form' => [
                'fields' => [
                    'on-hand-qty' => 'Idadi iliyopo',
                ],
            ],
        ],

        'delete' => [
            'notification' => [
                'title' => 'Bidhaa imefutwa',
                'body'  => 'Bidhaa imefutwa kwa mafanikio.',
            ],
        ],
    ],
];
