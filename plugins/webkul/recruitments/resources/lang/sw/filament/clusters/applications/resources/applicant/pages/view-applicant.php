<?php

return [
    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Mwombaji imefutwa',
                'body'  => 'mwombaji ina imefutwa successfully.',
            ],
        ],

        'refuse' => [
            'title'        => 'Kataa Sababu',
            'form'         => [
                'fields' => [
                    'notify'   => 'Notify',
                    'email-to' => 'Barua pepe Hadi',
                ],
            ],
            'notification' => [
                'title' => 'Mwombaji imekataliwa',
                'body'  => 'mwombaji ina imekataliwa successfully.',
            ],
        ],

        'reopen' => [
            'title'        => 'Rejesha Mwombaji kutoka kataa',
            'notification' => [
                'title' => 'Mwombaji reopened',
                'body'  => 'mwombaji ina reopened successfully.',
            ],
        ],

        'state' => [
            'notification' => [
                'title' => 'Mwombaji hali imesasishwa',
                'body'  => 'mwombaji hali ina imesasishwa successfully.',
            ],
        ],
    ],

    'mail' => [
        'application-refused' => [
            'subject' => 'Yako Kazi Application: :application',
        ],
    ],
];
